<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\User;
use Database\Seeders\FormBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FormThemeFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FormBuilderSeeder::class);
    }

    public function test_user_can_save_form_theme_settings(): void
    {
        $user = User::first();
        $form = $user->forms()->first();

        $themePayload = [
            'page_bg' => '#0f172a',
            'card_bg' => '#1e293b',
            'primary_color' => '#ff6100',
            'font_family' => 'Poppins',
            'card_radius' => 'rounded-3xl',
            'card_width' => 'max-w-3xl',
            'logo_url' => 'https://example.com/logo.png',
            'logo_align' => 'center',
            'logo_height' => '64',
        ];

        $response = $this->actingAs($user)->putJson("/api/dashboard/forms/{$form->id}", [
            'settings' => [
                'theme' => $themePayload,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        $form->refresh();
        $this->assertEquals($themePayload, $form->settings['theme']);
    }

    public function test_public_form_view_renders_with_saved_theme(): void
    {
        $user = User::first();
        $form = $user->forms()->first();

        $form->update([
            'settings' => [
                'theme' => [
                    'page_bg' => '#f0fdf4',
                    'card_bg' => '#ffffff',
                    'primary_color' => '#059669',
                    'font_family' => 'Outfit',
                    'card_radius' => 'rounded-2xl',
                    'card_width' => 'max-w-2xl',
                    'logo_url' => 'https://example.com/custom-logo.png',
                    'logo_align' => 'center',
                    'logo_height' => '56',
                ],
            ],
        ]);

        $response = $this->get("/f/{$form->slug}");

        $response->assertOk()
            ->assertSee('#f0fdf4', false)
            ->assertSee('#059669', false)
            ->assertSee('Outfit', false)
            ->assertSee('https://example.com/custom-logo.png', false);
    }

    public function test_unauthorized_user_cannot_update_another_users_theme(): void
    {
        $owner = User::first();
        $form = $owner->forms()->first();

        $otherUser = User::create([
            'name' => 'Other Tester',
            'email' => 'other@itb-ad.ac.id',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($otherUser)->putJson("/api/dashboard/forms/{$form->id}", [
            'settings' => [
                'theme' => [
                    'primary_color' => '#dc2626',
                ],
            ],
        ]);

        $response->assertNotFound();
    }
}
