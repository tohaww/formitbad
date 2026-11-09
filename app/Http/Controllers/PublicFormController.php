<?php

namespace App\Http\Controllers;

use App\Enums\FieldType;
use App\Exceptions\GoogleSheetException;
use App\Jobs\DispatchWebhookJob;
use App\Jobs\SyncToGoogleSheetJob;
use App\Models\Form;
use App\Models\FormField;
use App\Services\Google\RemoteDataProxyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    /**
     * Render the public form HTML view.
     */
    public function renderView(string $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with(['fields' => fn($q) => $q->orderBy('order_index')])
            ->firstOrFail();

        return view('forms.public_form', compact('form'));
    }

    /**
     * Get public form schema and fields by slug (API).
     */
    public function show(string $slug): JsonResponse
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with(['fields' => fn($q) => $q->orderBy('order_index')])
            ->first();

        if (!$form) {
            return response()->json([
                'status' => 'error',
                'message' => 'Formulir tidak ditemukan atau sedang dinonaktifkan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $form->id,
                'title' => $form->title,
                'slug' => $form->slug,
                'description' => $form->description,
                'fields' => $form->fields->map(fn(FormField $f) => [
                    'id' => $f->id,
                    'key' => Str::slug($f->label, '_'),
                    'label' => $f->label,
                    'field_type' => $f->field_type->value,
                    'order_index' => $f->order_index,
                    'is_required' => $f->is_required,
                    'placeholder' => $f->placeholder,
                    'config' => $f->config ?: [],
                ]),
            ],
        ]);
    }

    /**
     * Fetch remote dropdown options from Google Sheets with caching and filtering.
     */
    public function getRemoteOptions(
        Request $request,
        string $slug,
        string $field_id,
        RemoteDataProxyService $proxyService
    ): JsonResponse {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$form) {
            return response()->json([
                'status' => 'error',
                'message' => 'Formulir tidak ditemukan atau sedang tidak aktif.',
            ], 404);
        }

        $field = $form->fields()->where('id', $field_id)->first();

        if (!$field) {
            return response()->json([
                'status' => 'error',
                'message' => 'Field formulir tidak ditemukan.',
            ], 404);
        }

        if ($field->field_type !== FieldType::REMOTE_DATA_DROPDOWN) {
            return response()->json([
                'status' => 'error',
                'message' => 'Field bukan bertipe remote_data_dropdown.',
            ], 400);
        }

        $q = $request->query('q');
        $page = (int)$request->query('page', 1);
        $limit = (int)$request->query('limit', 20);

        try {
            $result = $proxyService->getRemoteOptions($field, $q, $page, $limit);
            return response()->json($result);
        } catch (GoogleSheetException $e) {
            $statusCode = $e->getCode();
            if ($statusCode < 400 || $statusCode >= 600) {
                $statusCode = 502;
            }

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'detail' => $e->getDetail(),
            ], $statusCode);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat mengambil data dari Google Sheets.',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle public form submission.
     */
    public function submit(Request $request, string $slug): JsonResponse
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->first();

        if (!$form) {
            return response()->json([
                'status' => 'error',
                'message' => 'Formulir tidak aktif atau tidak ditemukan.',
            ], 404);
        }

        // Build dynamic validation rules based on form fields
        $rules = [];
        $fieldKeyMap = [];
        $maxUploadKb = ((int)config('filesystems.max_upload_size_mb', env('MAX_UPLOAD_SIZE_MB', 10))) * 1024;

        foreach ($form->fields as $field) {
            $key = Str::slug($field->label, '_');
            $fieldKeyMap[$key] = $field;

            $fieldRules = [$field->is_required ? 'required' : 'nullable'];

            switch ($field->field_type) {
                case FieldType::EMAIL:
                    $fieldRules[] = 'email';
                    if (!empty($field->config['domain_restriction'])) {
                        $fieldRules[] = 'ends_with:@' . ltrim($field->config['domain_restriction'], '@');
                    }
                    break;
                case FieldType::NUMBER:
                    $fieldRules[] = 'numeric';
                    break;
                case FieldType::URL:
                    $fieldRules[] = 'url';
                    break;
                case FieldType::DATE:
                    $fieldRules[] = 'date';
                    break;
                case FieldType::SELECT:
                case FieldType::RADIO:
                    if (!empty($field->config['options']) && is_array($field->config['options'])) {
                        $fieldRules[] = 'in:' . implode(',', $field->config['options']);
                    }
                    break;
                case FieldType::CHECKBOX:
                    $fieldRules[] = 'array';
                    break;
                case FieldType::RATING:
                    $scale = $field->config['scale'] ?? 5;
                    $fieldRules[] = "integer|min:1|max:{$scale}";
                    break;
                case FieldType::FILE:
                    $fieldRules[] = "file|max:{$maxUploadKb}";
                    break;
                case FieldType::REMOTE_DATA_DROPDOWN:
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi formulir gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        // Process file uploads if any
        foreach ($form->fields as $field) {
            $key = Str::slug($field->label, '_');
            if ($field->field_type === FieldType::FILE && $request->hasFile($key)) {
                $file = $request->file($key);
                $disk = config('filesystems.default', env('FILESYSTEM_DISK', 'public'));
                $storedPath = $file->store("submissions/{$form->id}", $disk);

                $validatedData[$key] = [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $storedPath,
                    'url' => asset("storage/{$storedPath}"),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                ];
            }
        }

        // Create submission record
        $submission = $form->submissions()->create([
            'payload' => $validatedData,
            'sheet_synced' => false,
            'webhook_delivered' => false,
        ]);

        // Dispatch background sync jobs
        SyncToGoogleSheetJob::dispatch($submission);
        DispatchWebhookJob::dispatch($submission);

        return response()->json([
            'status' => 'success',
            'message' => 'Jawaban Anda telah berhasil dikirim.',
            'submission_id' => $submission->id,
            'submitted_at' => $submission->created_at->toISOString(),
        ], 201);
    }
}
