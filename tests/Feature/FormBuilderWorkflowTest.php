<?php

namespace Tests\Feature;

use App\Enums\FieldType;
use App\Models\Form;
use App\Models\User;
use Database\Seeders\FormBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormBuilderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FormBuilderSeeder::class);
    }

    public function test_can_fetch_public_form_schema_by_slug(): void
    {
        $response = $this->getJson('/api/public/forms/workshop-it-ai-2026');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title', 'Pendaftaran Workshop IT & AI 2026')
            ->assertJsonPath('data.slug', 'workshop-it-ai-2026')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'description',
                    'fields' => [
                        '*' => ['id', 'key', 'label', 'field_type', 'order_index', 'is_required', 'placeholder', 'config'],
                    ],
                ],
            ]);

        // Ensure sensitive backend fields are NOT leaked to public
        $response->assertJsonMissing(['target_spreadsheet_id']);
        $response->assertJsonMissing(['webhook_secret']);
        $response->assertJsonMissing(['user_id']);
    }

    public function test_can_submit_public_form_successfully(): void
    {
        $payload = [
            'nama_lengkap' => 'Bambang Sudirman, M.Kom.',
            'email_institusi' => 'bambang@itbad.ac.id',
            'nomor_whatsapp' => '081234567899',
            'kategori_peserta' => 'Dosen',
            'sesi_workshop' => 'Sesi 1: 09:00 - 12:00 WIB',
            'topik_yang_diminati' => ['Google OAuth & Identity Flow'],
            'ekspektasi_catatan_tambahan' => 'Siap mengikuti workshop sampai selesai.',
        ];

        $response = $this->postJson('/api/public/forms/workshop-it-ai-2026/submit', $payload);

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['status', 'message', 'submission_id', 'submitted_at']);

        $submissionId = $response->json('submission_id');

        $this->assertDatabaseHas('submissions', [
            'id' => $submissionId,
            'sheet_synced' => false,
            'webhook_delivered' => false,
        ]);
    }

    public function test_public_form_submission_validation_fails_when_required_is_missing(): void
    {
        // Missing required 'nama_lengkap' and 'email_institusi'
        $payload = [
            'nomor_whatsapp' => '081234567899',
        ];

        $response = $this->postJson('/api/public/forms/workshop-it-ai-2026/submit', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonValidationErrors(['nama_lengkap', 'email_institusi']);
    }

    public function test_authenticated_user_can_create_and_manage_forms(): void
    {
        $user = User::first();
        Sanctum::actingAs($user);

        // 1. Create form
        $createResponse = $this->postJson('/api/forms', [
            'title' => 'Formulir Feedback Pengguna',
            'description' => 'Feedback mengenai UI baru.',
            'target_spreadsheet_id' => 'sheet-12345',
            'target_sheet_name' => 'Feedback',
            'is_active' => true,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.slug', 'formulir-feedback-pengguna');

        $formId = $createResponse->json('data.id');

        // 2. Add field to form
        $fieldResponse = $this->postJson("/api/forms/{$formId}/fields", [
            'label' => 'Kesan Pertama',
            'field_type' => FieldType::TEXTAREA->value,
            'order_index' => 1,
            'is_required' => true,
            'placeholder' => 'Ceritakan pengalaman Anda...',
        ]);

        $fieldResponse->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.label', 'Kesan Pertama');

        // 3. List forms
        $listResponse = $this->getJson('/api/forms');
        $listResponse->assertOk()
            ->assertJsonPath('status', 'success');
    }

    public function test_sync_fields_persistence_endpoint(): void
    {
        $form = Form::where('slug', 'workshop-it-ai-2026')->first();

        $syncPayload = [
            'fields' => [
                [
                    'id' => null,
                    'label' => 'Nama Lengkap Baru',
                    'field_type' => 'text',
                    'order_index' => 1,
                    'is_required' => true,
                    'placeholder' => 'Ketik nama...',
                    'config' => ['max_length' => 100],
                ],
                [
                    'id' => null,
                    'label' => 'Dosen Pembimbing',
                    'field_type' => 'remote_data_dropdown',
                    'order_index' => 2,
                    'is_required' => false,
                    'placeholder' => 'Pilih Dosen...',
                    'config' => [
                        'spreadsheet_id' => 'sheet-test-id',
                        'sheet_name' => 'DataDosen',
                        'range' => 'A2:C',
                        'column_value' => 'A',
                        'column_label' => 'B',
                        'column_subtext' => 'C',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($form->user)->putJson("/api/dashboard/forms/{$form->id}/fields", $syncPayload);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data');

        $this->assertDatabaseHas('form_fields', [
            'form_id' => $form->id,
            'label' => 'Nama Lengkap Baru',
            'order_index' => 1,
        ]);

        $this->assertDatabaseHas('form_fields', [
            'form_id' => $form->id,
            'label' => 'Dosen Pembimbing',
            'field_type' => 'remote_data_dropdown',
        ]);
    }

    public function test_public_short_url_and_builder_ui_render_successfully(): void
    {
        $form = Form::where('slug', 'workshop-it-ai-2026')->first();

        // 1. Test short public URL /f/{slug} (no auth required)
        $shortResponse = $this->get("/f/{$form->slug}");
        $shortResponse->assertOk()
            ->assertSee($form->title)
            ->assertSee('tom-select');

        // 2. Unauthenticated user accessing builder redirects to /login
        $unauthResponse = $this->get("/forms/{$form->id}/edit");
        $unauthResponse->assertRedirect('/login');

        // 3. Authenticated user can access form builder workspace UI
        $builderResponse = $this->actingAs($form->user)->get("/forms/{$form->id}/edit");
        $builderResponse->assertOk()
            ->assertSee($form->title)
            ->assertSee('BUILD')
            ->assertSee('SETTINGS')
            ->assertSee('PUBLISH')
            ->assertSee('Add Form Element')
            ->assertSee('My Forms');
    }
}

