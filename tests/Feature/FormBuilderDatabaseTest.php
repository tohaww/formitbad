<?php

namespace Tests\Feature;

use App\Enums\FieldType;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Submission;
use App\Models\User;
use App\Services\Auth\GoogleAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class FormBuilderDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user_with_uuid(): void
    {
        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@itbad.ac.id',
            'google_id' => 'google-uid-12345',
            'avatar_url' => 'https://example.com/avatar.jpg',
        ]);

        $this->assertNotNull($user->id);
        $this->assertTrue(Str::isUuid($user->id));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'budi@itbad.ac.id',
            'google_id' => 'google-uid-12345',
        ]);
    }

    public function test_can_create_form_and_relationships_with_uuid(): void
    {
        $user = User::create([
            'name' => 'Dosen Pengampu',
            'email' => 'dosen@itbad.ac.id',
            'google_id' => 'google-uid-9999',
            'avatar_url' => null,
        ]);

        $form = Form::create([
            'user_id' => $user->id,
            'title' => 'Form Pendaftaran Skripsi',
            'slug' => 'pendaftaran-skripsi-2026',
            'description' => 'Form pengajuan judul skripsi mahasiswa.',
            'target_spreadsheet_id' => 'spreadsheet-id-test',
            'target_sheet_name' => 'Sheet1',
            'webhook_url' => 'https://webhook.site/test',
            'webhook_secret' => 'secret123',
            'is_active' => true,
        ]);

        $this->assertTrue(Str::isUuid($form->id));
        $this->assertEquals($user->id, $form->user->id);

        $field = FormField::create([
            'form_id' => $form->id,
            'label' => 'Judul Usulan',
            'field_type' => FieldType::TEXT,
            'order_index' => 1,
            'is_required' => true,
            'placeholder' => 'Masukkan judul skripsi...',
            'config' => ['min_words' => 5],
        ]);

        $this->assertTrue(Str::isUuid($field->id));
        $this->assertEquals(FieldType::TEXT, $field->field_type);
        $this->assertIsArray($field->config);
        $this->assertEquals(5, $field->config['min_words']);

        $submission = Submission::create([
            'form_id' => $form->id,
            'payload' => [
                'judul_usulan' => 'Rancang Bangun Sistem Informasi Berbasis Microservices',
            ],
            'sheet_synced' => true,
            'webhook_delivered' => false,
        ]);

        $this->assertTrue(Str::isUuid($submission->id));
        $this->assertIsArray($submission->payload);
        $this->assertTrue($submission->sheet_synced);
        $this->assertFalse($submission->webhook_delivered);

        // Test form relationship to fields and submissions
        $this->assertCount(1, $form->fresh()->fields);
        $this->assertCount(1, $form->fresh()->submissions);
        $this->assertEquals($submission->id, $form->fresh()->submissions->first()->id);
    }

    public function test_google_auth_service_allowed_domain_and_user_creation(): void
    {
        config(['services.google.allowed_domain' => 'itbad.ac.id']);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-mock-id-777');
        $googleUser->shouldReceive('getEmail')->andReturn('mahasiswa@itbad.ac.id');
        $googleUser->shouldReceive('getName')->andReturn('Mahasiswa Baru');
        $googleUser->shouldReceive('getNickname')->andReturn(null);
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');

        $authService = app(GoogleAuthService::class);
        $user = $authService->findOrCreateUser($googleUser);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('mahasiswa@itbad.ac.id', $user->email);
        $this->assertEquals('google-mock-id-777', $user->google_id);

        // Test rejection on unallowed domain
        $unauthorizedGoogleUser = Mockery::mock(SocialiteUser::class);
        $unauthorizedGoogleUser->shouldReceive('getEmail')->andReturn('intruder@gmail.com');

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $authService->validateAllowedDomain('intruder@gmail.com');
    }

    public function test_google_auth_redirect_and_routes_exist(): void
    {
        $responseApi = $this->getJson('/api/auth/google/url');
        $responseApi->assertOk()
            ->assertJsonStructure(['status', 'url']);
    }

    public function test_database_seeder_populates_all_tables(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@itbad.ac.id',
            'google_id' => '109876543210987654321',
        ]);

        $this->assertDatabaseHas('forms', [
            'slug' => 'workshop-it-ai-2026',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('forms', [
            'slug' => 'evaluasi-layanan-sistem',
            'is_active' => true,
        ]);

        $workshopForm = Form::where('slug', 'workshop-it-ai-2026')->first();
        $this->assertNotNull($workshopForm);
        $this->assertCount(8, $workshopForm->fields);
        $this->assertCount(2, $workshopForm->submissions);

        $evalForm = Form::where('slug', 'evaluasi-layanan-sistem')->first();
        $this->assertNotNull($evalForm);
        $this->assertCount(3, $evalForm->fields);
        $this->assertCount(1, $evalForm->submissions);
    }
}
