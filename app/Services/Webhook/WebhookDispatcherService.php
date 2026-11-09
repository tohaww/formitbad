<?php

namespace App\Services\Webhook;

use App\Models\Submission;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcherService
{
    /**
     * Dispatch submission payload to the form's designated webhook URL.
     */
    public function dispatch(Submission $submission): bool
    {
        $form = $submission->form;
        if (!$form || empty($form->webhook_url)) {
            return false;
        }

        $secret = $form->webhook_secret ?: config('services.webhook.default_secret', env('DEFAULT_WEBHOOK_SECRET', 'secret'));
        $timeout = (int)config('services.webhook.timeout', env('WEBHOOK_TIMEOUT_SECONDS', 10));

        $timestamp = time();
        $payloadData = [
            'event' => 'form.submission.created',
            'form_id' => $form->id,
            'form_slug' => $form->slug,
            'submission_id' => $submission->id,
            'timestamp' => $timestamp,
            'data' => $submission->payload,
            'submitted_at' => $submission->created_at?->toISOString() ?: now()->toISOString(),
        ];

        $encodedPayload = json_encode($payloadData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = hash_hmac('sha256', "{$timestamp}.{$encodedPayload}", $secret);

        try {
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'CustomFormBuilder-Webhook/1.0',
                    'X-Form-Signature' => "t={$timestamp},v1={$signature}",
                    'X-Form-Id' => $form->id,
                    'X-Submission-Id' => $submission->id,
                ])
                ->withBody($encodedPayload, 'application/json')
                ->post($form->webhook_url);

            if ($response->successful()) {
                return true;
            }

            Log::warning("Webhook dispatch failed with HTTP {$response->status()} for form {$form->id}: {$response->body()}");
            return false;
        } catch (Exception $e) {
            Log::error("Webhook dispatch error for submission {$submission->id}: " . $e->getMessage());
            return false;
        }
    }
}

