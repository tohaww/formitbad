<?php

namespace Tests\Feature;

use App\Enums\FieldType;
use App\Exceptions\GoogleSheetException;
use App\Models\Form;
use App\Models\FormField;
use App\Models\User;
use App\Services\Google\GoogleSheetService;
use Google\Service\Exception as GoogleApiException;
use Google\Service\Sheets;
use Google\Service\Sheets\Resource\SpreadsheetsValues;
use Google\Service\Sheets\ValueRange;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class RemoteDataProxyEngineTest extends TestCase
{
    use RefreshDatabase;

    protected Form $form;
    protected FormField $remoteField;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'google_id' => 'gid-12345',
        ]);

        $this->form = Form::create([
            'user_id' => $user->id,
            'title' => 'Form Pendaftaran Ujian',
            'slug' => 'pendaftaran-ujian',
            'description' => 'Test form remote options',
            'target_spreadsheet_id' => 'spreadsheet-test-123',
            'target_sheet_name' => 'Data',
            'is_active' => true,
        ]);

        $this->remoteField = $this->form->fields()->create([
            'label' => 'Mata Kuliah',
            'field_type' => FieldType::REMOTE_DATA_DROPDOWN,
            'order_index' => 1,
            'is_required' => true,
            'config' => [
                'spreadsheet_id' => 'spreadsheet-test-123',
                'sheet_name' => 'MataKuliah',
                'range' => 'A2:C',
                'column_value' => 'A', // Kode MK
                'column_label' => 'B', // Nama MK
                'column_subtext' => 'C', // SKS / Prodi
            ],
        ]);
    }

    public function test_google_sheet_service_fetch_and_append_helpers(): void
    {
        $mockValuesResource = Mockery::mock(SpreadsheetsValues::class);
        $mockSheets = Mockery::mock(Sheets::class);
        $mockSheets->spreadsheets_values = $mockValuesResource;

        // Mock fetchSheetRows
        $expectedRows = [
            ['IF101', 'Algoritma & Pemrograman', '3 SKS - Teknik Informatika'],
            ['IF102', 'Struktur Data', '3 SKS - Teknik Informatika'],
        ];

        $mockValueRange = new ValueRange(['values' => $expectedRows]);

        $mockValuesResource->shouldReceive('get')
            ->with('spreadsheet-test-123', "'MataKuliah'!A2:C")
            ->once()
            ->andReturn($mockValueRange);

        // Mock appendSubmissionRow
        $mockValuesResource->shouldReceive('append')
            ->once()
            ->andReturn(new ValueRange());

        $service = new GoogleSheetService();
        $service->setService($mockSheets);

        // Test fetchSheetRows
        $rows = $service->fetchSheetRows('spreadsheet-test-123', 'MataKuliah', 'A2:C');
        $this->assertCount(2, $rows);
        $this->assertEquals('Algoritma & Pemrograman', $rows[0][1]);

        // Test appendSubmissionRow
        $appended = $service->appendSubmissionRow('spreadsheet-test-123', 'MataKuliah', ['IF103', 'Basis Data', '3 SKS']);
        $this->assertTrue($appended);
    }

    public function test_remote_options_endpoint_returns_data_with_caching_and_pagination(): void
    {
        $sheetServiceMock = Mockery::mock(GoogleSheetService::class);

        $fakeSheetData = [
            ['IF101', 'Algoritma & Pemrograman', '3 SKS - Teknik Informatika'],
            ['IF102', 'Struktur Data & Algoritma', '3 SKS - Teknik Informatika'],
            ['SI201', 'Manajemen Basis Data', '3 SKS - Sistem Informasi'],
            ['SI202', 'Analisis Proses Bisnis', '2 SKS - Sistem Informasi'],
            ['TI301', 'Kecerdasan Buatan & Machine Learning', '3 SKS - Teknik Informatika'],
        ];

        // Should be called only ONCE due to 15-minute caching
        $sheetServiceMock->shouldReceive('fetchSheetRows')
            ->once()
            ->with('spreadsheet-test-123', 'MataKuliah', 'A2:C')
            ->andReturn($fakeSheetData);

        $this->app->instance(GoogleSheetService::class, $sheetServiceMock);

        // First Request: page 1, limit 2
        $response = $this->getJson("/api/public/forms/{$this->form->slug}/fields/{$this->remoteField->id}/remote-options?page=1&limit=2");

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('pagination.page', 1)
            ->assertJsonPath('pagination.limit', 2)
            ->assertJsonPath('pagination.has_more', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.value', 'IF101')
            ->assertJsonPath('data.0.label', 'Algoritma & Pemrograman')
            ->assertJsonPath('data.0.subtext', '3 SKS - Teknik Informatika');

        // Second Request: uses cache, filter with q='bisnis'
        $responseFilter = $this->getJson("/api/public/forms/{$this->form->slug}/fields/{$this->remoteField->id}/remote-options?q=bisnis");

        $responseFilter->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.value', 'SI202')
            ->assertJsonPath('data.0.label', 'Analisis Proses Bisnis');

        // Third Request: filter matching subtext with q='Sistem Informasi'
        $responseSubtext = $this->getJson("/api/public/forms/{$this->form->slug}/fields/{$this->remoteField->id}/remote-options?q=Sistem%20Informasi");

        $responseSubtext->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_remote_options_endpoint_returns_404_when_form_or_field_not_found(): void
    {
        // Bad form slug
        $res1 = $this->getJson("/api/public/forms/non-existent-slug/fields/{$this->remoteField->id}/remote-options");
        $res1->assertNotFound()
            ->assertJsonPath('status', 'error');

        // Bad field id
        $badFieldUuid = '00000000-0000-0000-0000-000000000000';
        $res2 = $this->getJson("/api/public/forms/{$this->form->slug}/fields/{$badFieldUuid}/remote-options");
        $res2->assertNotFound()
            ->assertJsonPath('status', 'error');
    }

    public function test_remote_options_endpoint_returns_400_when_field_is_not_remote_dropdown(): void
    {
        $textField = $this->form->fields()->create([
            'label' => 'Nama Peserta',
            'field_type' => FieldType::TEXT,
            'order_index' => 2,
        ]);

        $res = $this->getJson("/api/public/forms/{$this->form->slug}/fields/{$textField->id}/remote-options");
        $res->assertStatus(400)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Field bukan bertipe remote_data_dropdown.');
    }

    public function test_remote_options_endpoint_handles_google_api_permission_denied_gracefully(): void
    {
        $sheetServiceMock = Mockery::mock(GoogleSheetService::class);

        $sheetServiceMock->shouldReceive('fetchSheetRows')
            ->once()
            ->andThrow(new GoogleSheetException(
                "Akses Google Sheets ditolak (403 Forbidden). Pastikan spreadsheet telah dibagikan (share) dengan izin Viewer/Editor ke email Service Account.",
                403,
                "The caller does not have permission"
            ));

        $this->app->instance(GoogleSheetService::class, $sheetServiceMock);

        $res = $this->getJson("/api/public/forms/{$this->form->slug}/fields/{$this->remoteField->id}/remote-options");

        $res->assertStatus(403)
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'detail']);
    }
}

