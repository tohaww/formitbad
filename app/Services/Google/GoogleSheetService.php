<?php

namespace App\Services\Google;

use App\Exceptions\GoogleSheetException;
use App\Models\User;
use Exception;
use Google\Client;
use Google\Exception as GoogleApiClient;
use Google\Service\Sheets;
use Google\Service\Sheets\AddSheetRequest;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    protected ?Sheets $sheetsService = null;

    /**
     * Initialize Google API Client with Service Account credentials.
     *
     * @throws GoogleSheetException
     */
    public function getService(): Sheets
    {
        if ($this->sheetsService) {
            return $this->sheetsService;
        }

        $credentials = config('services.google.credentials_path', env('GOOGLE_APPLICATION_CREDENTIALS'));

        if (empty($credentials)) {
            throw new GoogleSheetException(
                "Kredensial Google Service Account belum dikonfigurasikan di GOOGLE_APPLICATION_CREDENTIALS.",
                500
            );
        }

        try {
            $client = new Client();
            $client->setApplicationName(config('app.name', 'CustomFormBuilder'));
            $client->addScope(Sheets::SPREADSHEETS);

            if (is_string($credentials) && str_starts_with(trim($credentials), '{')) {
                // Raw JSON credentials content
                $authConfig = json_decode($credentials, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new GoogleSheetException("Format JSON pada GOOGLE_APPLICATION_CREDENTIALS tidak valid.", 500);
                }
                $client->setAuthConfig($authConfig);
            } elseif (is_string($credentials) && (file_exists($credentials) || file_exists(base_path($credentials)))) {
                // File path to JSON credentials (both absolute & relative paths supported)
                $filePath = file_exists($credentials) ? $credentials : base_path($credentials);
                $client->setAuthConfig($filePath);
            } else {
                throw new GoogleSheetException(
                    "File kredensial Google Service Account tidak ditemukan pada path: {$credentials}",
                    500,
                    "Periksa path GOOGLE_APPLICATION_CREDENTIALS pada file .env Anda."
                );
            }

            $this->sheetsService = new Sheets($client);
            return $this->sheetsService;
        } catch (GoogleSheetException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error("Failed to initialize Google Sheets client: " . $e->getMessage());
            throw new GoogleSheetException(
                "Gagal menginisialisasi Google API Client: " . $e->getMessage(),
                500,
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Get Google Sheets service instance for a specific user via OAuth.
     * If user does not have OAuth connected, falls back to the central Service Account.
     */
    public function getServiceForUser(?User $user): Sheets
    {
        if ($this->sheetsService) {
            return $this->sheetsService;
        }

        if ($user && $user->hasGoogleSheetsConnected()) {
            return $this->createUserOAuthService($user);
        }

        return $this->getService();
    }

    /**
     * Create Google Sheets client using user's OAuth access & refresh tokens.
     */
    public function createUserOAuthService(User $user): Sheets
    {
        try {
            $client = new Client();
            $client->setApplicationName(config('app.name', 'CustomFormBuilder'));
            $client->setClientId(config('services.google.client_id', env('GOOGLE_CLIENT_ID')));
            $client->setClientSecret(config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET')));
            $client->addScope(Sheets::SPREADSHEETS);
            $client->addScope('https://www.googleapis.com/auth/drive.file');

            $accessToken = [
                'access_token' => $user->google_sheets_access_token,
                'refresh_token' => $user->google_sheets_refresh_token,
                'created' => time(),
            ];

            if ($user->google_sheets_token_expires_at) {
                $accessToken['expires_in'] = max(0, $user->google_sheets_token_expires_at->diffInSeconds(now()));
            }

            $client->setAccessToken($accessToken);

            // Auto-refresh token if expired
            if ($client->isAccessTokenExpired() && !empty($user->google_sheets_refresh_token)) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_sheets_refresh_token);
                if (!isset($newToken['error'])) {
                    $user->update([
                        'google_sheets_access_token' => $newToken['access_token'] ?? $user->google_sheets_access_token,
                        'google_sheets_refresh_token' => $newToken['refresh_token'] ?? $user->google_sheets_refresh_token,
                        'google_sheets_token_expires_at' => isset($newToken['expires_in']) ? now()->addSeconds((int)$newToken['expires_in']) : now()->addHour(),
                    ]);
                }
            }

            return new Sheets($client);
        } catch (Exception $e) {
            Log::warning("Failed to initialize user OAuth Sheets client: " . $e->getMessage() . ". Falling back to service account.");
            return $this->getService();
        }
    }

    /**
     * Create a brand new Google Spreadsheet in the user's Google Drive (Jotform-style).
     *
     * @return array{spreadsheet_id: string, spreadsheet_url: string, title: string, sheet_name: string}
     */
    public function createNewSpreadsheetForUser(User $user, string $title, array $headers = []): array
    {
        $service = $this->getServiceForUser($user);

        $spreadsheet = new Spreadsheet([
            'properties' => [
                'title' => $title,
            ],
        ]);

        $created = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId,properties.title,spreadsheetUrl',
        ]);

        $spreadsheetId = $created->getSpreadsheetId();
        $spreadsheetUrl = $created->getSpreadsheetUrl() ?: "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit";

        // Write initial header row if headers provided
        if (!empty($headers)) {
            $this->appendSubmissionRow($spreadsheetId, 'Sheet1', $headers, $user);
        }

        return [
            'spreadsheet_id' => $spreadsheetId,
            'spreadsheet_url' => $spreadsheetUrl,
            'title' => $title,
            'sheet_name' => 'Sheet1',
        ];
    }

    /**
     * Set explicit Google Sheets instance (useful for mocking in unit/feature tests).
     */
    public function setService(Sheets $service): void
    {
        $this->sheetsService = $service;
    }

    /**
     * Helper to read row data from Google Sheets.
     *
     * @param string $spreadsheetId ID spreadsheet Google
     * @param string $sheetName Nama tab sheet (misal: Sheet1)
     * @param string $range Rentang kolom/baris (misal: A:D atau A2:D100)
     * @param User|null $user Pengguna pemilik OAuth token (opsional)
     * @return array<array<mixed>> Matriks baris dan kolom
     *
     * @throws GoogleSheetException
     */
    public function fetchSheetRows(string $spreadsheetId, string $sheetName, string $range, ?User $user = null): array
    {
        $service = $this->getServiceForUser($user);

        // Format range e.g. "'Sheet1'!A:D"
        $formattedRange = str_contains($range, '!')
            ? $range
            : "'" . trim(str_replace("'", '', $sheetName)) . "'!" . trim($range);

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $formattedRange);
            $values = $response->getValues();

            return is_array($values) ? $values : [];
        } catch (GoogleApiClient $e) {
            $this->handleApiException($e, $spreadsheetId, $sheetName);
        } catch (Exception $e) {
            Log::error("fetchSheetRows unexpected error: " . $e->getMessage());
            throw new GoogleSheetException(
                "Gagal mengambil data baris dari Google Sheets: " . $e->getMessage(),
                500,
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Helper to append a new submission row to Google Sheets.
     *
     * @param string $spreadsheetId ID spreadsheet target
     * @param string $sheetName Nama tab sheet target
     * @param array<mixed> $rowValues Nilai baris yang akan ditambahkan
     * @param User|null $user Pengguna pemilik OAuth token (opsional)
     * @return bool
     *
     * @throws GoogleSheetException
     */
    public function appendSubmissionRow(string $spreadsheetId, string $sheetName, array $rowValues, ?User $user = null): bool
    {
        $service = $this->getServiceForUser($user);
        $sheetNameClean = trim(str_replace("'", '', $sheetName));
        $range = "'{$sheetNameClean}'!A:Z";

        try {
            $body = new ValueRange([
                'values' => [$rowValues],
            ]);

            $service->spreadsheets_values->append($spreadsheetId, $range, $body, [
                'valueInputOption' => 'USER_ENTERED',
                'insertDataOption' => 'INSERT_ROWS',
            ]);

            return true;
        } catch (GoogleApiClient $e) {
            $this->handleApiException($e, $spreadsheetId, $sheetName);
        } catch (Exception $e) {
            Log::error("appendSubmissionRow error: " . $e->getMessage());
            throw new GoogleSheetException(
                "Gagal menulis baris ke Google Sheets: " . $e->getMessage(),
                500,
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Ensure a sheet (tab) exists in the spreadsheet. If not, create it.
     */
    public function ensureSheetTabExists(string $spreadsheetId, string $sheetName, ?User $user = null): bool
    {
        $service = $this->getServiceForUser($user);
        $cleanSheetName = trim(str_replace("'", '', $sheetName));

        try {
            $spreadsheet = $service->spreadsheets->get($spreadsheetId);
            $sheets = $spreadsheet->getSheets() ?: [];
            foreach ($sheets as $sheet) {
                if ($sheet->getProperties()?->getTitle() === $cleanSheetName) {
                    return true;
                }
            }

            // Tab doesn't exist, create it via batchUpdate
            $addSheetRequest = new AddSheetRequest([
                'properties' => [
                    'title' => $cleanSheetName,
                ],
            ]);
            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
                'requests' => [
                    ['addSheet' => $addSheetRequest],
                ],
            ]);
            $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
            Log::info("Created new sheet tab '{$cleanSheetName}' in spreadsheet {$spreadsheetId}.");
            return true;
        } catch (Exception $e) {
            Log::warning("ensureSheetTabExists notice: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse and map Google API exceptions into user-friendly GoogleSheetException.
     *
     * @throws GoogleSheetException
     */
    protected function handleApiException(GoogleApiClient $e, string $spreadsheetId, string $sheetName): never
    {
        $code = (int)$e->getCode();
        $rawMessage = $e->getMessage();
        $detail = null;

        // Try extracting structured error message from JSON response
        $decoded = json_decode($rawMessage, true);
        if (isset($decoded['error']['message'])) {
            $detail = $decoded['error']['message'];
        } else {
            $detail = $rawMessage;
        }

        Log::warning("Google API Sheets Error [{$code}]: {$detail} (Spreadsheet: {$spreadsheetId}, Sheet: {$sheetName})");

        switch ($code) {
            case 403:
                throw new GoogleSheetException(
                    "Akses Google Sheets ditolak (403 Forbidden). Pastikan akun Google Anda atau Service Account memiliki hak akses Editor ke spreadsheet ini.",
                    403,
                    $detail,
                    $e
                );
            case 404:
                throw new GoogleSheetException(
                    "Spreadsheet atau tab sheet '{$sheetName}' tidak ditemukan (404 Not Found). Periksa kembali spreadsheet_id dan sheet_name pada konfigurasi.",
                    404,
                    $detail,
                    $e
                );
            case 400:
                throw new GoogleSheetException(
                    "Permintaan ke Google Sheets tidak valid (400 Bad Request). Periksa kembali format rentang (range) atau nama sheet.",
                    400,
                    $detail,
                    $e
                );
            default:
                throw new GoogleSheetException(
                    "Gagal berkomunikasi dengan Google Sheets API (Kode HTTP: {$code}).",
                    $code >= 400 && $code < 600 ? $code : 502,
                    $detail,
                    $e
                );
        }
    }
}
