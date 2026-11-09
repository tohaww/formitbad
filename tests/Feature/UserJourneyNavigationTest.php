<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserJourneyNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Form $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'email' => 'dosen@itbad.ac.id',
            'name' => 'Dr. H. Ahmad Dahlan, M.Pd.',
            'google_id' => 'gid-100200300',
            'avatar_url' => 'https://example.com/avatar.png',
        ]);

        $this->form = Form::create([
            'user_id' => $this->user->id,
            'title' => 'Formulir Registrasi Seminar Nasional',
            'slug' => 'seminar-nasional-itb-ad',
            'description' => 'Pendaftaran seminar kecerdasan buatan dan fintech.',
            'is_active' => true,
        ]);

        FormField::create([
            'form_id' => $this->form->id,
            'label' => 'Nama Peserta',
            'field_type' => 'text',
            'order_index' => 1,
            'is_required' => true,
        ]);
    }

    public function test_unauthenticated_user_accessing_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_can_view_clean_login_page_with_google_button(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Masuk ke Akun Anda')
            ->assertSee('Sign in with Google')
            ->assertSee(route('auth.google.redirect'))
            ->assertSee('CustomFormBuilder');
    }

    public function test_unauthenticated_user_accessing_dashboard_redirects_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_accessing_root_redirects_to_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/');
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_user_accessing_login_page_redirects_to_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/login');
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_user_can_view_dashboard_with_forms_list_and_stats(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertOk()
            ->assertSee($this->user->name)
            ->assertSee('+ Create Form')
            ->assertSee($this->form->title)
            ->assertSee('/f/' . $this->form->slug)
            ->assertSee('Edit')
            ->assertSee('View')
            ->assertSee('Data')
            ->assertSee('Hapus Formulir');
    }

    public function test_authenticated_user_can_create_new_form_from_dashboard(): void
    {
        $response = $this->actingAs($this->user)->post('/forms', [
            'title' => 'Formulir Ujian Proposal Tesis',
        ]);

        $this->assertDatabaseHas('forms', [
            'user_id' => $this->user->id,
            'title' => 'Formulir Ujian Proposal Tesis',
        ]);

        $newForm = Form::where('title', 'Formulir Ujian Proposal Tesis')->first();

        // Redirects directly to the new form's builder workspace
        $response->assertRedirect(route('forms.edit', $newForm->id));
    }

    public function test_authenticated_user_can_access_form_builder_workspace(): void
    {
        $response = $this->actingAs($this->user)->get(route('forms.edit', $this->form->id));

        $response->assertOk()
            ->assertSee($this->form->title)
            ->assertSee('BUILD')
            ->assertSee('SETTINGS')
            ->assertSee('PUBLISH')
            ->assertSee('My Forms')
            ->assertSee('Add Form Element');
    }

    public function test_user_cannot_access_another_users_form_in_builder(): void
    {
        $anotherUser = User::create([
            'email' => 'other@itbad.ac.id',
            'name' => 'Pengguna Lain',
            'google_id' => 'gid-999999',
        ]);

        $response = $this->actingAs($anotherUser)->get(route('forms.edit', $this->form->id));
        $response->assertNotFound();
    }

    public function test_authenticated_user_can_delete_form_from_dashboard(): void
    {
        $response = $this->actingAs($this->user)->delete(route('forms.destroy_web', $this->form->id));

        $response->assertRedirect('/dashboard')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('forms', [
            'id' => $this->form->id,
        ]);
    }

    public function test_user_can_logout_and_is_redirected_to_login(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');

        $response->assertRedirect('/login')
            ->assertSessionHas('message');

        $this->assertGuest();
    }

    public function test_public_user_can_access_form_renderer_without_login(): void
    {
        $response = $this->get('/f/' . $this->form->slug);

        $response->assertOk()
            ->assertSee($this->form->title)
            ->assertSee('Nama Peserta');
    }
}

