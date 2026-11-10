<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Unauthenticated user accessing root renders official landing page.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('Formnis');

        $loginResponse = $this->get('/login');
        $loginResponse->assertOk()
            ->assertSee('Sign in with Google');
    }
}
