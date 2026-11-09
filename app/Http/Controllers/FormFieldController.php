<?php

namespace App\Http\Controllers;

use App\Enums\FieldType;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FormFieldController extends Controller
{
    /**
     * Add a new field to the specified form.
     */
    public function store(Request $request, string $formId): JsonResponse
    {
        $form = $request->user()->forms()->findOrFail($formId);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'field_type' => ['required', Rule::enum(FieldType::class)],
            'order_index' => 'nullable|integer',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'config' => 'nullable|array',
        ]);

        if (!isset($validated['order_index'])) {
            $validated['order_index'] = ($form->fields()->max('order_index') ?? 0) + 1;
        }

        $field = $form->fields()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Field formulir berhasil ditambahkan.',
            'data' => $field,
        ], 201);
    }

    /**
     * Update an existing field.
     */
    public function update(Request $request, string $fieldId): JsonResponse
    {
        $field = FormField::whereHas('form', fn($q) => $q->where('user_id', $request->user()->id))
            ->findOrFail($fieldId);

        $validated = $request->validate([
            'label' => 'sometimes|required|string|max:255',
            'field_type' => ['sometimes', 'required', Rule::enum(FieldType::class)],
            'order_index' => 'nullable|integer',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'config' => 'nullable|array',
        ]);

        $field->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Field formulir berhasil diperbarui.',
            'data' => $field,
        ]);
    }

    /**
     * Batch reorder fields of a form.
     */
    public function reorder(Request $request, string $formId): JsonResponse
    {
        $form = $request->user()->forms()->findOrFail($formId);

        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|uuid',
            'fields.*.order_index' => 'required|integer',
        ]);

        foreach ($validated['fields'] as $item) {
            $form->fields()->where('id', $item['id'])->update(['order_index' => $item['order_index']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Urutan field berhasil diperbarui.',
            'data' => $form->fields()->orderBy('order_index')->get(),
        ]);
    }

    /**
     * Batch sync and persist all form fields (structure, order, and config).
     */
    public function syncFields(Request $request, string $id): JsonResponse
    {
        $form = Form::findOrFail($id);

        if ($request->user() && $form->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        $validated = $request->validate([
            'fields' => 'present|array',
            'fields.*.id' => 'nullable|string',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|string',
            'fields.*.order_index' => 'required|integer',
            'fields.*.is_required' => 'nullable|boolean',
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.config' => 'nullable|array',
        ]);

        $incomingIds = [];
        foreach ($validated['fields'] as $index => $item) {
            $fieldId = $item['id'] ?? null;
            $data = [
                'label' => $item['label'],
                'field_type' => $item['field_type'],
                'order_index' => $item['order_index'] ?? ($index + 1),
                'is_required' => (bool)($item['is_required'] ?? false),
                'placeholder' => $item['placeholder'] ?? null,
                'config' => $item['config'] ?? [],
            ];

            if ($fieldId && Str::isUuid($fieldId) && $form->fields()->where('id', $fieldId)->exists()) {
                $form->fields()->where('id', $fieldId)->update($data);
                $incomingIds[] = $fieldId;
            } else {
                $newField = $form->fields()->create($data);
                $incomingIds[] = $newField->id;
            }
        }

        // Delete any fields removed from the canvas
        $form->fields()->whereNotIn('id', $incomingIds)->delete();

        $freshFields = $form->fields()->orderBy('order_index')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Susunan formulir berhasil disimpan.',
            'data' => $freshFields,
        ]);
    }

    /**
     * Delete a field from a form.
     */
    public function destroy(Request $request, string $fieldId): JsonResponse
    {
        $field = FormField::whereHas('form', fn($q) => $q->where('user_id', $request->user()->id))
            ->findOrFail($fieldId);

        $field->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Field formulir berhasil dihapus.',
        ]);
    }
}
