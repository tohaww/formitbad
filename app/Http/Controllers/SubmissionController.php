<?php

namespace App\Http\Controllers;

use App\Jobs\DispatchWebhookJob;
use App\Jobs\SyncSubmissionToGoogleSheetJob;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * List submissions for a specific form.
     */
    public function index(Request $request, string $formId): JsonResponse
    {
        $form = $request->user()->forms()->findOrFail($formId);

        $submissions = $form->submissions()
            ->latest()
            ->paginate($request->query('per_page', 25));

        return response()->json([
            'status' => 'success',
            'form' => [
                'id' => $form->id,
                'title' => $form->title,
                'slug' => $form->slug,
            ],
            'data' => $submissions,
        ]);
    }

    /**
     * Manually retry syncing a submission to Google Sheets or Webhook.
     */
    public function resync(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $submission = $user
            ? Submission::whereHas('form', fn($q) => $q->where('user_id', $user->id))->findOrFail($id)
            : Submission::findOrFail($id);

        $target = $request->input('target', 'all');

        if (in_array($target, ['all', 'sheet'])) {
            SyncSubmissionToGoogleSheetJob::dispatch($submission);
        }

        if (in_array($target, ['all', 'webhook'])) {
            DispatchWebhookJob::dispatch($submission);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Proses sinkronisasi ulang berhasil dijadwalkan.',
        ]);
    }
}

