<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\User;
use Database\Seeders\FormBuilderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConditionalLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FormBuilderSeeder::class);
    }

    public function test_user_can_save_form_conditions(): void
    {
        $user = User::first();
        $form = $user->forms()->first();
        $fields = $form->fields;

        $conditionsPayload = [
            [
                'id' => 'cond_1',
                'if_field_id' => $fields[0]->id,
                'operator' => 'equals',
                'value' => 'Mahasiswa',
                'action' => 'show',
                'target_field_id' => $fields[1]->id,
            ],
        ];

        $response = $this->actingAs($user)->putJson("/api/dashboard/forms/{$form->id}", [
            'settings' => [
                'conditions' => $conditionsPayload,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        $form->refresh();
        $this->assertEquals($conditionsPayload, $form->settings['conditions']);
    }

    public function test_public_form_renders_with_conditions_schema(): void
    {
        $user = User::first();
        $form = $user->forms()->first();
        $fields = $form->fields;

        $form->update([
            'settings' => [
                'conditions' => [
                    [
                        'id' => 'cond_test_123',
                        'if_field_id' => $fields[0]->id,
                        'operator' => 'equals',
                        'value' => 'PNS',
                        'action' => 'show',
                        'target_field_id' => $fields[1]->id,
                    ],
                ],
            ],
        ]);

        $response = $this->get("/f/{$form->slug}");

        $response->assertOk()
            ->assertSee('cond_test_123')
            ->assertSee('PNS');
    }
}
