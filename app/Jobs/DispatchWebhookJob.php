<?php

namespace App\Jobs;

use App\Models\Submission;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Exponential backoff in seconds between attempts.
     *
     * @var array<int>
     */
    public array $backoff = [10, 60, 300];

    public function __construct(
        public Submission $submission
    ) {}

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $this->submission->loadMissing('form');
        $form = $this->submission->form;

        if (!$form || empty($form->webhook_url)) {
            Log::info("DispatchWebhookJob: Form {$form?->id} has no webhook_url. Skipping.");
            return;
        }

        $secret = $form->webhook_secret ?: config('services.webhook.default_secret', env('DEFAULT_WEBHOOK_SECRET'));
        $timeout = (int)config('services.webhook.timeout', env('WEBHOOK_TIMEOUT_SECONDS', 10));

        $timestamp = time();
        $payloadData = [
            'event' => 'submission.created',
            'form_id' => $form->id,
            'form_slug' => $form->slug,
            'submission_id' => $this->submission->id,
            'timestamp' => $timestamp,
            'data' => $this->submission->payload,
            'submitted_at' => $this->submission->created_at?->toISOString() ?: now()->toISOString(),
        ];

        $encodedPayload = json_encode($payloadData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'CustomFormBuilder-Webhook/1.0',
            'X-Form-Id' => $form->id,
            'X-Submission-Id' => $this->submission->id,
        ];

        if (!empty($secret)) {
            $hash = hash_hmac('sha256', "{$timestamp}.{$encodedPayload}", $secret);
            $headers['X-Webhook-Signature'] = "sha256={$hash}";
            $headers['X-Form-Signature'] = "t={$timestamp},v1={$hash}";
        }

        try {
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->withBody($encodedPayload, 'application/json')
                ->post($form->webhook_url);

            if ($response->successful()) {
                $this->submission->update(['webhook_delivered' => true]);
                Log::info("DispatchWebhookJob: Submission {$this->submission->id} successfully delivered to {$form->webhook_url} (HTTP {$response->status()}).");
                return;
            }

            Log::warning("DispatchWebhookJob failed with HTTP {$response->status()} for form {$form->id}: {$response->body()}");

            // Trigger retry on 429, 500, 502, 503, 504
            if ($response->status() === 429 || $response->serverError()) {
                throw new Exception("Webhook endpoint returned HTTP {$response->status()}: " . $response->body());
            }
        } catch (Exception $e) {
            Log::error("DispatchWebhookJob error for submission {$this->submission->id}: " . $e->getMessage());
            if (!app()->runningUnitTests()) {
                throw $e;
            }
        }
    }
}
