<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_booking_actions_redirect_guests_to_login(): void
    {
        $message = 'Anda harus login terlebih dahulu untuk melanjutkan booking.';

        $this->followingRedirects()
            ->post('/booking/form')
            ->assertOk()
            ->assertSee($message);

        $this->post('/booking/simpan')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', $message);
    }
}
