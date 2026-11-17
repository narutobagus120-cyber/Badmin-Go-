<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_booking_pages_and_actions_redirect_guests_to_login(): void
    {
        $message = 'Anda harus login terlebih dahulu untuk melanjutkan booking.';

        $this->get('/booking/1')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', $message);

        $this->followingRedirects()
            ->post('/booking/form')
            ->assertOk()
            ->assertSee($message);

        $this->post('/booking/simpan')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', $message);
    }

    public function test_authenticated_user_can_log_out_and_cannot_continue_booking(): void
    {
        $user = new User([
            'nama_user' => 'Pelanggan',
            'email' => 'pelanggan@example.com',
            'password' => 'password',
            'no_telepon' => '08123456789',
            'role' => 'pelanggan',
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Keluar')
            ->assertSee(route('logout'));

        $this->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Anda berhasil keluar dari akun.');

        $this->assertGuest();
        $this->get('/booking/1')->assertRedirect(route('login'));
    }
}
