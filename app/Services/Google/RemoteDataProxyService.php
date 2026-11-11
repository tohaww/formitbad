<?php

namespace App\Services\Google;

use App\Exceptions\GoogleSheetException;
use App\Models\FormField;
use Illuminate\Support\Facades\Cache;

class RemoteDataProxyService
{
    public function __construct(
        protected GoogleSheetService $sheetService
    ) {}

    /**
     * Fetch, cache, filter, and paginate remote options from Google Sheets.
     *
     * @param FormField $field
     * @param string|null $query
     * @param int $page
     * @param int $limit
     * @return array<string, mixed>
     *
     * @throws GoogleSheetException
     */
    public function getRemoteOptions(FormField $field, ?string $query = null, int $page = 1, int $limit = 20): array
    {
        $config = $field->config ?: [];

        $spreadsheetId = $config['spreadsheet_id'] ?? $field->form?->target_spreadsheet_id;
        $sheetName = $config['sheet_name'] ?? $field->form?->target_sheet_name ?? 'Sheet1';
        $range = $config['range'] ?? 'A:D';

        if (empty($spreadsheetId)) {
            throw new GoogleSheetException(
                "Konfigurasi 'spreadsheet_id' belum didefinisikan pada field ini.",
                422
            );
        }

        // Cache Key uniquely identifies spreadsheet + sheetName + range
        $cleanRange = trim(str_replace("'", '', $range));
        $cacheKey = "sheet_remote_data:{$spreadsheetId}:{$sheetName}:" . md5($cleanRange);
        $ttl = (int)env('SPREADSHEET_CACHE_TTL', 900); // 15 menit (900 detik)

        $user = $field->form?->user;

        // Fetch from Cache or Google Sheets API
        $rawRows = Cache::remember($cacheKey, $ttl, function () use ($spreadsheetId, $sheetName, $cleanRange, $user) {
            return $this->sheetService->fetchSheetRows($spreadsheetId, $sheetName, $cleanRange, $user);
        });

        if (empty($rawRows)) {
            return [
                'status' => 'success',
                'data' => [],
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'has_more' => false,
                ],
            ];
        }

        // Determine column indexes
        $firstRow = $rawRows[0] ?? [];
        $valCol = $config['column_value'] ?? $config['value_column'] ?? 'A';
        $labelCol = $config['column_label'] ?? $config['label_column'] ?? 'B';
        $subtextCol = $config['column_subtext'] ?? $config['subtext_column'] ?? 'C';

        $valIdx = $this->resolveColumnIndex($valCol, $firstRow);
        $labelIdx = $this->resolveColumnIndex($labelCol, $firstRow);

        // Support single or multiple subtext columns (e.g. "B, D" or "B,D" or "B - D" or "NIM, Prodi")
        $subtextCols = preg_split('/[,;\-]+/', (string)$subtextCol, -1, PREG_SPLIT_NO_EMPTY);
        $subtextIndexes = [];
        if (!empty($subtextCols)) {
            foreach ($subtextCols as $colItem) {
                $colItem = trim($colItem);
                if ($colItem !== '') {
                    $subtextIndexes[] = $this->resolveColumnIndex($colItem, $firstRow);
                }
            }
            $subtextIndexes = array_values(array_unique($subtextIndexes));
        } else {
            $subtextIndexes = [$this->resolveColumnIndex($subtextCol, $firstRow)];
        }

        // Check if first row is a header row (e.g. if column specified was a header name)
        $hasHeader = $this->isHeaderRow($valCol, $labelCol, $firstRow);
        $dataRows = $hasHeader ? array_slice($rawRows, 1) : $rawRows;

        // Transform rows to items
        $items = [];
        foreach ($dataRows as $row) {
            $value = isset($row[$valIdx]) ? trim((string)$row[$valIdx]) : '';
            $label = isset($row[$labelIdx]) ? trim((string)$row[$labelIdx]) : '';

            $subtextParts = [];
            foreach ($subtextIndexes as $sIdx) {
                if (isset($row[$sIdx]) && trim((string)$row[$sIdx]) !== '') {
                    $subtextParts[] = trim((string)$row[$sIdx]);
                }
            }
            $subtext = !empty($subtextParts) ? implode(' • ', $subtextParts) : null;

            if ($value === '' && $label === '') {
                continue;
            }

            $items[] = [
                'value' => $value !== '' ? $value : $label,
                'label' => $label !== '' ? $label : $value,
                'subtext' => $subtext,
            ];
        }

        // Server-side filtering by query parameter `q` (case-insensitive substring match)
        $q = trim((string)$query);
        if ($q !== '') {
            $items = array_values(array_filter($items, function (array $item) use ($q) {
                if (mb_stripos($item['label'], $q) !== false) {
                    return true;
                }
                if (!empty($item['value']) && mb_stripos($item['value'], $q) !== false) {
                    return true;
                }
                if (!empty($item['subtext']) && mb_stripos($item['subtext'], $q) !== false) {
                    return true;
                }
                return false;
            }));
        }

        // Pagination
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        $total = count($items);
        $offset = ($page - 1) * $limit;
        $sliced = array_slice($items, $offset, $limit);
        $hasMore = ($offset + $limit) < $total;

        return [
            'status' => 'success',
            'data' => array_values($sliced),
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'has_more' => $hasMore,
            ],
        ];
    }

    /**
     * Resolve column reference (letter like 'A', integer index, or header name) to 0-based column index.
     */
    protected function resolveColumnIndex(mixed $columnRef, array $headers = []): int
    {
        if ($columnRef === null || $columnRef === '') {
            return 0;
        }

        if (is_int($columnRef) || ctype_digit((string)$columnRef)) {
            return (int)$columnRef;
        }

        $colStr = trim((string)$columnRef);

        // Check if matches header name in first row
        foreach ($headers as $idx => $header) {
            if (strcasecmp(trim((string)$header), $colStr) === 0) {
                return $idx;
            }
        }

        // Check single or double letter column e.g. A, B, AA
        if (preg_match('/^[A-Za-z]+$/', $colStr) && strlen($colStr) <= 2) {
            $colStr = strtoupper($colStr);
            if (strlen($colStr) === 1) {
                return ord($colStr) - ord('A');
            } elseif (strlen($colStr) === 2) {
                return (ord($colStr[0]) - ord('A') + 1) * 26 + (ord($colStr[1]) - ord('A'));
            }
        }

        return 0;
    }

    /**
     * Check if first row is a header row.
     */
    protected function isHeaderRow(mixed $valCol, mixed $labelCol, array $firstRow): bool
    {
        foreach ([$valCol, $labelCol] as $col) {
            if (is_string($col) && !preg_match('/^[A-Za-z]{1,2}$/', $col)) {
                foreach ($firstRow as $header) {
                    if (strcasecmp(trim((string)$header), trim($col)) === 0) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

