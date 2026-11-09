<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
    /**
     * Display a listing of forms belonging to the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $forms = $request->user()->forms()
            ->withCount(['fields', 'submissions'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $forms,
        ]);
    }

    /**
     * Store a newly created form.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug',
            'description' => 'nullable|string',
            'target_spreadsheet_id' => 'nullable|string',
            'target_sheet_name' => 'nullable|string',
            'webhook_url' => 'nullable|url',
            'webhook_secret' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Form::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $form = $request->user()->forms()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Formulir berhasil dibuat.',
            'data' => $form,
        ], 201);
    }

    /**
     * Display the specified form with its fields and submissions summary.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $form = $request->user()->forms()
            ->with(['fields' => fn($q) => $q->orderBy('order_index')])
            ->withCount('submissions')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $form,
        ]);
    }

    /**
     * Update the specified form.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $form = $request->user() ? $request->user()->forms()->findOrFail($id) : Form::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => "sometimes|required|string|max:255|unique:forms,slug,{$form->id}",
            'description' => 'nullable|string',
            'target_spreadsheet_id' => 'nullable|string',
            'target_sheet_name' => 'nullable|string',
            'webhook_url' => 'nullable|url',
            'webhook_secret' => 'nullable|string',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
        ]);

        if (isset($validated['settings']) && is_array($form->settings)) {
            $validated['settings'] = array_merge($form->settings, $validated['settings']);
        }

        $form->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Formulir berhasil diperbarui.',
            'data' => $form,
        ]);
    }

    /**
     * Remove the specified form.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $form = $request->user()->forms()->findOrFail($id);
        $form->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Formulir berhasil dihapus.',
        ]);
    }

    /**
     * Send test webhook ping payload with HMAC SHA256 signature.
     */
    public function testWebhook(Request $request, string $id): JsonResponse
    {
        $form = $request->user()->forms()->findOrFail($id);

        if (empty($form->webhook_url)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Formulir belum memiliki webhook_url yang dikonfigurasi.',
            ], 422);
        }

        $secret = $form->webhook_secret ?: config('services.webhook.default_secret', env('DEFAULT_WEBHOOK_SECRET', 'secret'));
        $timeout = (int)config('services.webhook.timeout', env('WEBHOOK_TIMEOUT_SECONDS', 10));

        $timestamp = time();
        $payloadData = [
            'event' => 'form.webhook.ping',
            'form_id' => $form->id,
            'form_slug' => $form->slug,
            'timestamp' => $timestamp,
            'message' => 'Tes ping webhook dari CustomFormBuilder',
        ];

        $encodedPayload = json_encode($payloadData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = hash_hmac('sha256', "{$timestamp}.{$encodedPayload}", $secret);

        $start = microtime(true);
        try {
            $response = \Illuminate\Support\Facades\Http::timeout($timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'CustomFormBuilder-Webhook/1.0',
                    'X-Form-Signature' => "t={$timestamp},v1={$signature}",
                    'X-Form-Id' => $form->id,
                ])
                ->withBody($encodedPayload, 'application/json')
                ->post($form->webhook_url);

            $latency = round((microtime(true) - $start) * 1000, 2);

            return response()->json([
                'status' => 'success',
                'delivered' => $response->successful(),
                'http_status' => $response->status(),
                'latency_ms' => $latency,
                'response_body' => $response->body(),
                'signature_sent' => "t={$timestamp},v1={$signature}",
            ]);
        } catch (\Exception $e) {
            $latency = round((microtime(true) - $start) * 1000, 2);
            return response()->json([
                'status' => 'error',
                'delivered' => false,
                'latency_ms' => $latency,
                'message' => 'Gagal menghubungi webhook URL: ' . $e->getMessage(),
            ], 502);
        }
    }

    /**
     * Export form submissions as CSV.
     */
    public function export(Request $request, string $id)
    {
        $form = $request->user()
            ? $request->user()->forms()->with(['fields' => fn($q) => $q->orderBy('order_index')])->findOrFail($id)
            : Form::with(['fields' => fn($q) => $q->orderBy('order_index')])->findOrFail($id);

        $submissions = $form->submissions()->latest()->get();

        $headers = array_merge(
            ['ID Pengiriman', 'Waktu Submit', 'Synced to Sheet', 'Webhook Delivered'],
            $form->fields->pluck('label')->toArray()
        );

        $callback = function () use ($form, $submissions, $headers) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);

            foreach ($submissions as $sub) {
                $row = [
                    $sub->id,
                    $sub->created_at->format('Y-m-d H:i:s'),
                    $sub->sheet_synced ? 'YES' : 'NO',
                    $sub->webhook_delivered ? 'YES' : 'NO',
                ];

                foreach ($form->fields as $field) {
                    $key = Str::slug($field->label, '_');
                    $val = $sub->payload[$key] ?? $sub->payload[$field->label] ?? '';
                    if (is_array($val)) {
                        $row[] = isset($val['url']) ? $val['url'] : implode(', ', $val);
                    } elseif (is_bool($val)) {
                        $row[] = $val ? 'TRUE' : 'FALSE';
                    } else {
                        $row[] = (string)$val;
                    }
                }

                fputcsv($output, $row);
            }

            fclose($output);
        };

        $filename = "submissions-{$form->slug}-" . date('Ymd_His') . ".csv";

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

