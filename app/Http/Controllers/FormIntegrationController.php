<?php

namespace App\Http\Controllers;

use App\Exceptions\GoogleSheetException;
use App\Models\Form;
use App\Services\Google\GoogleSheetService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FormIntegrationController extends Controller
{
    /**
     * Get integration status and current configuration for a form.
     */
    public function index(Request $request, string $id): JsonResponse
    {
        $form = Form::findOrFail($id);

        if ($request->user() && $form->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        $sheetsConnected = !empty($form->target_spreadsheet_id);
        $webhookConnected = !empty($form->webhook_url);
        $settings = $form->settings ?: [];
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'google_sheets' => [
                    'connected' => $sheetsConnected,
                    'spreadsheet_id' => $form->target_spreadsheet_id,
                    'sheet_name' => $form->target_sheet_name ?: 'Sheet1',
                    'auto_create_header' => $settings['auto_create_header'] ?? true,
                    'user_oauth_connected' => $user ? $user->hasGoogleSheetsConnected() : false,
                    'user_oauth_email' => $user?->google_sheets_email,
                    'user_oauth_name' => $user?->google_sheets_name,
                ],
                'webhooks' => [
                    'connected' => $webhookConnected,
                    'webhook_url' => $form->webhook_url,
                    'has_secret' => !empty($form->webhook_secret),
                    'webhook_secret' => $form->webhook_secret,
                ],
            ],
        ]);
    }

    /**
     * Save Google Sheets integration configuration.
     */
    public function saveGoogleSheets(Request $request, string $id): JsonResponse
    {
        $form = Form::findOrFail($id);

        if ($request->user() && $form->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        $validated = $request->validate([
            'spreadsheet_id' => 'required|string',
            'sheet_name' => 'nullable|string',
            'auto_create_header' => 'nullable|boolean',
        ]);

        $settings = $form->settings ?: [];
        $settings['auto_create_header'] = (bool)($validated['auto_create_header'] ?? true);

        $form->update([
            'target_spreadsheet_id' => trim($validated['spreadsheet_id']),
            'target_sheet_name' => trim($validated['sheet_name'] ?? '') ?: 'Sheet1',
            'settings' => $settings,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Integrasi Google Sheets berhasil dihubungkan.',
            'data' => [
                'connected' => true,
                'spreadsheet_id' => $form->target_spreadsheet_id,
                'sheet_name' => $form->target_sheet_name,
                'auto_create_header' => $settings['auto_create_header'],
            ],
        ]);
    }

    /**
     * Test Google Sheets connection with Service Account credentials.
     */
    public function testGoogleSheets(Request $request, string $id, GoogleSheetService $sheetService): JsonResponse
    {
        $form = Form::findOrFail($id);

        if ($request->user() && $form->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        $spreadsheetId = $request->input('spreadsheet_id') ?: $form->target_spreadsheet_id;
        $sheetName = $request->input('sheet_name') ?: $form->target_sheet_name ?: 'Sheet1';

        if (empty($spreadsheetId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Spreadsheet ID belum diisi.',
            ], 422);
        }

        try {
            $user = $request->user();
            $rows = $sheetService->fetchSheetRows($spreadsheetId, $sheetName, 'A1:Z1', $user);
            $headerCount = isset($rows[0]) ? count($rows[0]) : 0;

            $authMethod = ($user && $user->hasGoogleSheetsConnected())
                ? "Akun Google ({$user->google_sheets_email})"
                : "Google Service Account";

            return response()->json([
                'status' => 'success',
                'message' => "Koneksi ke Google Sheets berhasil via {$authMethod}!",
                'sheet_name' => $sheetName,
                'existing_headers' => $rows[0] ?? [],
                'header_columns_count' => $headerCount,
            ]);
        } catch (GoogleSheetException $e) {
            $code = $e->getCode();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'detail' => $e->getDetail(),
            ], $code >= 400 && $code < 600 ? $code : 502);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menguji koneksi Google Sheets: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Redirect user to Google OAuth to authorize Google Sheets & Google Drive management (Jotform style).
     */
    public function connectGoogleSheetsOAuth(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $formId = $request->query('form_id');

        $client = new \Google\Client();
        $client->setClientId(config('services.google.client_id', env('GOOGLE_CLIENT_ID')));
        $client->setClientSecret(config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET')));
        $client->setRedirectUri(route('auth.google.callback'));
        $client->setAccessType('offline');
        $client->setPrompt('consent select_account');
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
        $client->addScope('https://www.googleapis.com/auth/drive.file');
        $client->addScope('openid');
        $client->addScope('email');
        $client->addScope('profile');

        $state = base64_encode(json_encode([
            'action' => 'connect_sheets',
            'form_id' => $formId,
            'user_id' => $user?->id,
            'time' => time(),
        ]));
        $client->setState($state);

        return redirect()->away($client->createAuthUrl());
    }

    /**
     * Automatically create a new Google Spreadsheet in the authenticated user's Google Drive.
     */
    public function createAutoSpreadsheet(Request $request, string $id, GoogleSheetService $sheetService): JsonResponse
    {
        $form = Form::with(['fields' => fn($q) => $q->orderBy('order_index')])->findOrFail($id);
        $user = $request->user();

        if ($user && $form->user_id !== $user->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        if (!$user || !$user->hasGoogleSheetsConnected()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan hubungkan akun Google Anda terlebih dahulu dengan menekan tombol "Connect with Google".',
            ], 400);
        }

        $title = $request->input('title') ?: ($form->title . ' (Tanggapan)');
        $headers = $form->fields->pluck('label')->toArray();

        try {
            $result = $sheetService->createNewSpreadsheetForUser($user, $title, $headers);

            $settings = $form->settings ?: [];
            $settings['auto_create_header'] = true;

            $form->update([
                'target_spreadsheet_id' => $result['spreadsheet_id'],
                'target_sheet_name' => $result['sheet_name'],
                'settings' => $settings,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Spreadsheet baru berhasil dibuat dan dihubungkan secara otomatis di Google Drive Anda!',
                'data' => [
                    'connected' => true,
                    'spreadsheet_id' => $result['spreadsheet_id'],
                    'spreadsheet_url' => $result['spreadsheet_url'],
                    'sheet_name' => $result['sheet_name'],
                    'title' => $result['title'],
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat spreadsheet otomatis: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect user's Google OAuth connection.
     */
    public function disconnectGoogleAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->update([
                'google_sheets_access_token' => null,
                'google_sheets_refresh_token' => null,
                'google_sheets_token_expires_at' => null,
                'google_sheets_email' => null,
                'google_sheets_name' => null,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Koneksi akun Google berhasil diputuskan.',
        ]);
    }

    /**
     * Save Webhook configuration.
     */
    public function saveWebhooks(Request $request, string $id): JsonResponse
    {
        $form = Form::findOrFail($id);

        if ($request->user() && $form->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        $validated = $request->validate([
            'webhook_url' => 'required|url',
            'webhook_secret' => 'nullable|string',
        ]);

        $form->update([
            'webhook_url' => trim($validated['webhook_url']),
            'webhook_secret' => trim($validated['webhook_secret'] ?? '') ?: null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Integrasi Webhook berhasil dihubungkan.',
            'data' => [
                'connected' => true,
                'webhook_url' => $form->webhook_url,
                'has_secret' => !empty($form->webhook_secret),
            ],
        ]);
    }

    /**
     * Send test webhook payload.
     */
    public function testWebhooks(Request $request, string $id): JsonResponse
    {
        $form = Form::findOrFail($id);

        if ($request->user() && $form->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        $url = $request->input('webhook_url') ?: $form->webhook_url;
        $secret = $request->input('webhook_secret') ?: $form->webhook_secret ?: config('services.webhook.default_secret', env('DEFAULT_WEBHOOK_SECRET'));
        $timeout = (int)config('services.webhook.timeout', env('WEBHOOK_TIMEOUT_SECONDS', 10));

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'status' => 'error',
                'message' => 'URL Webhook tidak valid atau belum diisi.',
            ], 422);
        }

        $timestamp = time();
        $payloadData = [
            'event' => 'form.webhook.ping',
            'form_id' => $form->id,
            'form_slug' => $form->slug,
            'timestamp' => $timestamp,
            'message' => 'Ping test webhook dari CustomFormBuilder',
            'data' => [
                'test_field' => 'Nilai pengujian',
                'ping' => 'pong',
            ],
        ];

        $encodedPayload = json_encode($payloadData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'CustomFormBuilder-Webhook/1.0',
            'X-Form-Id' => $form->id,
        ];

        if (!empty($secret)) {
            $signature = hash_hmac('sha256', "{$timestamp}.{$encodedPayload}", $secret);
            $headers['X-Webhook-Signature'] = "sha256={$signature}";
            $headers['X-Form-Signature'] = "t={$timestamp},v1={$signature}";
        }

        $start = microtime(true);
        try {
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->withBody($encodedPayload, 'application/json')
                ->post($url);

            $latency = round((microtime(true) - $start) * 1000, 2);

            return response()->json([
                'status' => 'success',
                'delivered' => $response->successful(),
                'http_status' => $response->status(),
                'latency_ms' => $latency,
                'response_body' => substr($response->body(), 0, 500),
                'signature_sent' => $headers['X-Webhook-Signature'] ?? null,
            ]);
        } catch (Exception $e) {
            $latency = round((microtime(true) - $start) * 1000, 2);
            return response()->json([
                'status' => 'error',
                'delivered' => false,
                'latency_ms' => $latency,
                'message' => 'Gagal menghubungi Webhook endpoint: ' . $e->getMessage(),
            ], 502);
        }
    }

    /**
     * Disconnect an integration provider ('google-sheets' or 'webhooks').
     */
    public function disconnect(Request $request, string $id, string $provider): JsonResponse
    {
        $form = Form::findOrFail($id);

        if ($request->user() && $form->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diperbolehkan.');
        }

        if ($provider === 'google-sheets') {
            $form->update([
                'target_spreadsheet_id' => null,
                'target_sheet_name' => null,
            ]);
            $msg = 'Integrasi Google Sheets berhasil diputus.';
        } elseif ($provider === 'webhooks') {
            $form->update([
                'webhook_url' => null,
                'webhook_secret' => null,
            ]);
            $msg = 'Integrasi Webhooks berhasil diputus.';
        } else {
            return response()->json([
                'status' => 'error',
                'message' => "Penyedia integrasi '{$provider}' tidak dikenal.",
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => $msg,
        ]);
    }
}

