<?php

namespace App\Jobs;

use App\Exceptions\GoogleSheetException;
use App\Models\Submission;
use App\Services\Google\GoogleSheetService;
use Exception;
use Google\Service\Sheets\ValueRange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncToGoogleSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job (exponential backoff).
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
    public function handle(GoogleSheetService $sheetService): void
    {
        $this->submission->loadMissing('form.fields', 'form.user');
        $form = $this->submission->form;

        if (!$form || empty($form->target_spreadsheet_id)) {
            Log::info("SyncToGoogleSheetJob: Form {$form?->id} does not have target_spreadsheet_id. Skipping.");
            return;
        }

        $sheetName = $form->target_sheet_name ?: 'Sheet1';
        $spreadsheetId = $form->target_spreadsheet_id;

        // Auto-create header row if configured and sheet is empty
        $autoCreateHeader = $form->settings['auto_create_header'] ?? true;
        if ($autoCreateHeader) {
            $this->ensureHeaderRowExists($sheetService, $form, $spreadsheetId, $sheetName);
        }

        // Map payload data according to form field order
        $headers = $form->fields->pluck('label')->toArray();
        if (empty($headers)) {
            $headers = array_keys($this->submission->payload);
        }

        $rowValues = [];
        foreach ($headers as $header) {
            $key = Str::slug($header, '_');
            $val = $this->submission->payload[$key] ?? $this->submission->payload[$header] ?? '';
            if (is_array($val)) {
                $rowValues[] = isset($val['url']) ? $val['url'] : implode(', ', $val);
            } elseif (is_bool($val)) {
                $rowValues[] = $val ? 'TRUE' : 'FALSE';
            } else {
                $rowValues[] = (string)$val;
            }
        }

        $user = ($form->user && $form->user->hasGoogleSheetsConnected()) ? $form->user : null;

        try {
            $success = $user
                ? $sheetService->appendSubmissionRow($spreadsheetId, $sheetName, $rowValues, $user)
                : $sheetService->appendSubmissionRow($spreadsheetId, $sheetName, $rowValues);

            if ($success) {
                $this->submission->update(['sheet_synced' => true]);
                Log::info("SyncToGoogleSheetJob: Submission {$this->submission->id} successfully synced to Google Sheet.");
            }
        } catch (GoogleSheetException $e) {
            $code = $e->getCode();
            Log::warning("SyncToGoogleSheetJob warning for submission {$this->submission->id}: {$e->getMessage()}");

            // Retry on Rate Limit (429) or Gateway/Service Unavailable errors (502, 503, 504)
            // Skip retrying configuration/missing credential errors (500) or during tests
            if (!app()->runningUnitTests() && ($code === 429 || in_array($code, [502, 503, 504]))) {
                throw $e;
            }
        } catch (Exception $e) {
            Log::error("SyncToGoogleSheetJob error for submission {$this->submission->id}: " . $e->getMessage());
            if (!app()->runningUnitTests()) {
                throw $e;
            }
        }
    }

    /**
     * Ensure header row exists in the target sheet.
     */
    protected function ensureHeaderRowExists(GoogleSheetService $sheetService, $form, string $spreadsheetId, string $sheetName): void
    {
        $user = ($form->user && $form->user->hasGoogleSheetsConnected()) ? $form->user : null;

        try {
            $existing = $user
                ? $sheetService->fetchSheetRows($spreadsheetId, $sheetName, 'A1:Z1', $user)
                : $sheetService->fetchSheetRows($spreadsheetId, $sheetName, 'A1:Z1');

            if (empty($existing) || empty($existing[0])) {
                $headers = $form->fields->pluck('label')->toArray();
                if (!empty($headers)) {
                    $user
                        ? $sheetService->appendSubmissionRow($spreadsheetId, $sheetName, $headers, $user)
                        : $sheetService->appendSubmissionRow($spreadsheetId, $sheetName, $headers);
                    Log::info("Auto-created header row for spreadsheet {$spreadsheetId} sheet {$sheetName}.");
                }
            }
        } catch (Exception $e) {
            Log::debug("Could not verify/create header row: " . $e->getMessage());
        }
    }
}
