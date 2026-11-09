<?php

namespace Tests\Feature;

use App\Jobs\DispatchWebhookJob;
use App\Jobs\SyncToGoogleSheetJob;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Submission;
use App\Models\User;
use App\Services\Google\GoogleSheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class FormIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Form $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'email' => 'admin@itbad.ac.id',
            'name' => 'Admin Kampus ITB AD',
            'google_id' => 'google-admin-12345',
            'avatar_url' => 'https://example.com/avatar.png',
        ]);

        $this->form = Form::create([
            'user_id' => $this->user->id,
            'title' => 'Formulir Survei Kemahasiswaan',
            'slug' => 'survei-kemahasiswaan-2026',
            'description' => 'Survei evaluasi layanan akademik kampus.',
            'target_spreadsheet_id' => '1SheetIdTest123456789',
            'target_sheet_name' => 'DataSurvei',
            'webhook_url' => 'https://webhook.site/test-endpoint',
            'webhook_secret' => 'whsec_testsecret123',
            'is_active' => true,
            'settings' => [
                'auto_create_header' => true,
                'thank_you_title' => 'Terima Kasih Banyak!',
                'thank_you_message' => 'Respon survei Anda sangat berharga bagi kemajuan kampus.',
            ],
        ]);

        FormField::create([
            'form_id' => $this->form->id,
            'label' => 'Nama Mahasiswa',
            'field_type' => 'text',
            'order_index' => 1,
            'is_required' => true,
        ]);

        FormField::create([
            'form_id' => $this->form->id,
            'label' => 'Program Studi',
            'field_type' => 'text',
            'order_index' => 2,
            'is_required' => true,
        ]);
    }

    public function test_can_fetch_integration_status_and_config(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/dashboard/forms/{$this->form->id}/integrations");

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.google_sheets.connected', true)
            ->assertJsonPath('data.google_sheets.spreadsheet_id', '1SheetIdTest123456789')
            ->assertJsonPath('data.google_sheets.sheet_name', 'DataSurvei')
            ->assertJsonPath('data.google_sheets.auto_create_header', true)
            ->assertJsonPath('data.webhooks.connected', true)
            ->assertJsonPath('data.webhooks.webhook_url', 'https://webhook.site/test-endpoint')
            ->assertJsonPath('data.webhooks.has_secret', true);
    }

    public function test_can_save_google_sheets_integration(): void
    {
        $payload = [
            'spreadsheet_id' => '1NewSheetIdABCXYZ999',
            'sheet_name' => 'RespondenBaru',
            'auto_create_header' => false,
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/dashboard/forms/{$this->form->id}/integrations/google-sheets", $payload);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.connected', true)
            ->assertJsonPath('data.spreadsheet_id', '1NewSheetIdABCXYZ999')
            ->assertJsonPath('data.sheet_name', 'RespondenBaru')
            ->assertJsonPath('data.auto_create_header', false);

        $this->form->refresh();
        $this->assertEquals('1NewSheetIdABCXYZ999', $this->form->target_spreadsheet_id);
        $this->assertEquals('RespondenBaru', $this->form->target_sheet_name);
        $this->assertFalse($this->form->settings['auto_create_header']);
    }

    public function test_can_disconnect_google_sheets_integration(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/dashboard/forms/{$this->form->id}/integrations/google-sheets");

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        $this->form->refresh();
        $this->assertNull($this->form->target_spreadsheet_id);
        $this->assertNull($this->form->target_sheet_name);
    }

    public function test_can_save_webhooks_integration(): void
    {
        $payload = [
            'webhook_url' => 'https://api.external.com/webhooks/listener',
            'webhook_secret' => 'super_secret_signing_key_456',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/dashboard/forms/{$this->form->id}/integrations/webhooks", $payload);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.connected', true)
            ->assertJsonPath('data.webhook_url', 'https://api.external.com/webhooks/listener')
            ->assertJsonPath('data.has_secret', true);

        $this->form->refresh();
        $this->assertEquals('https://api.external.com/webhooks/listener', $this->form->webhook_url);
        $this->assertEquals('super_secret_signing_key_456', $this->form->webhook_secret);
    }

    public function test_can_disconnect_webhooks_integration(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/dashboard/forms/{$this->form->id}/integrations/webhooks");

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        $this->form->refresh();
        $this->assertNull($this->form->webhook_url);
        $this->assertNull($this->form->webhook_secret);
    }

    public function test_can_test_webhook_ping_delivery(): void
    {
        Http::fake([
            'https://api.external.com/webhooks/ping-receiver' => Http::response([
                'received' => true,
                'status' => 'acknowledged',
            ], 200),
        ]);

        $payload = [
            'webhook_url' => 'https://api.external.com/webhooks/ping-receiver',
            'webhook_secret' => 'whsec_pingsecret',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/dashboard/forms/{$this->form->id}/integrations/webhooks/test", $payload);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('delivered', true)
            ->assertJsonPath('http_status', 200)
            ->assertJsonStructure(['status', 'delivered', 'http_status', 'latency_ms', 'signature_sent']);
    }

    public function test_sync_to_google_sheet_job_updates_sheet_synced(): void
    {
        $submission = Submission::create([
            'form_id' => $this->form->id,
            'payload' => [
                'nama_mahasiswa' => 'Dewi Sartika',
                'program_studi' => 'Sistem Informasi',
            ],
            'sheet_synced' => false,
            'webhook_delivered' => false,
        ]);

        // Mock GoogleSheetService
        $mockSheetService = Mockery::mock(GoogleSheetService::class);
        $mockSheetService->shouldReceive('fetchSheetRows')
            ->with($this->form->target_spreadsheet_id, $this->form->target_sheet_name, 'A1:Z1')
            ->andReturn([['Nama Mahasiswa', 'Program Studi']]);

        $mockSheetService->shouldReceive('appendSubmissionRow')
            ->once()
            ->with(
                $this->form->target_spreadsheet_id,
                $this->form->target_sheet_name,
                ['Dewi Sartika', 'Sistem Informasi']
            )
            ->andReturn(true);

        $job = new SyncToGoogleSheetJob($submission);
        $job->handle($mockSheetService);

        $submission->refresh();
        $this->assertTrue($submission->sheet_synced);
    }

    public function test_dispatch_webhook_job_sends_structured_payload_and_signature(): void
    {
        Http::fake([
            'https://webhook.site/test-endpoint' => Http::response(['ok' => true], 200),
        ]);

        $submission = Submission::create([
            'form_id' => $this->form->id,
            'payload' => [
                'nama_mahasiswa' => 'Dewi Sartika',
                'program_studi' => 'Sistem Informasi',
            ],
            'sheet_synced' => false,
            'webhook_delivered' => false,
        ]);

        $job = new DispatchWebhookJob($submission);
        $job->handle();

        $submission->refresh();
        $this->assertTrue($submission->webhook_delivered);

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) use ($submission) {
            $hasSignature = $request->hasHeader('X-Webhook-Signature');
            $data = $request->data();

            return $request->url() === 'https://webhook.site/test-endpoint' &&
                $hasSignature &&
                str_starts_with($request->header('X-Webhook-Signature')[0], 'sha256=') &&
                $data['event'] === 'submission.created' &&
                $data['form_id'] === $this->form->id &&
                $data['submission_id'] === $submission->id &&
                $data['data']['nama_mahasiswa'] === 'Dewi Sartika';
        });
    }

    public function test_user_can_initiate_google_sheets_oauth_consent_flow(): void
    {
        $response = $this->actingAs($this->user)->get(route('web.dashboard.integrations.sheets.connect', [
            'form_id' => $this->form->id,
        ]));

        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com', $targetUrl);
        $this->assertStringContainsString('spreadsheets', $targetUrl);
    }

    public function test_user_can_automatically_create_spreadsheet_via_user_oauth(): void
    {
        $this->user->update([
            'google_sheets_access_token' => 'mock_access_token_123',
            'google_sheets_refresh_token' => 'mock_refresh_token_123',
            'google_sheets_email' => 'user@gmail.com',
        ]);

        $mockSheetService = Mockery::mock(GoogleSheetService::class);
        $mockSheetService->shouldReceive('createNewSpreadsheetForUser')
            ->once()
            ->andReturn([
                'spreadsheet_id' => '1NewAutoCreatedSpreadsheetId_999',
                'spreadsheet_url' => 'https://docs.google.com/spreadsheets/d/1NewAutoCreatedSpreadsheetId_999/edit',
                'title' => 'Formulir Uji Pendaftaran (Tanggapan)',
                'sheet_name' => 'Sheet1',
            ]);

        $this->app->instance(GoogleSheetService::class, $mockSheetService);

        $response = $this->actingAs($this->user)->postJson(
            route('web.dashboard.forms.integrations.sheets.create_auto', $this->form->id),
            ['title' => 'Formulir Uji Pendaftaran (Tanggapan)']
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'connected' => true,
                    'spreadsheet_id' => '1NewAutoCreatedSpreadsheetId_999',
                    'sheet_name' => 'Sheet1',
                ],
            ]);

        $this->form->refresh();
        $this->assertEquals('1NewAutoCreatedSpreadsheetId_999', $this->form->target_spreadsheet_id);
    }

    public function test_user_can_disconnect_google_account(): void
    {
        $this->user->update([
            'google_sheets_access_token' => 'token_to_remove',
            'google_sheets_refresh_token' => 'refresh_to_remove',
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            route('web.dashboard.integrations.sheets.disconnect_user')
        );

        $response->assertOk()->assertJson(['status' => 'success']);

        $this->user->refresh();
        $this->assertFalse($this->user->hasGoogleSheetsConnected());
    }
}
