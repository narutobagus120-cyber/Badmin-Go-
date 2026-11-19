<?php

namespace Tests\Feature;

use App\Models\Lapangan;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_petugas_account_from_environment_config(): void
    {
        config([
            'admin.name' => 'Admin Badmin',
            'admin.email' => 'admin@example.com',
            'admin.phone' => '08123456789',
            'admin.password' => 'secure-admin-password',
        ]);

        app(AdminUserSeeder::class)->run();

        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('petugas', $admin->role);
        $this->assertTrue(Hash::check('secure-admin-password', $admin->password));

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'secure-admin-password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_seeder_does_not_promote_a_customer_account(): void
    {
        $customer = $this->createUser('pelanggan');
        config([
            'admin.name' => 'Admin Badmin',
            'admin.email' => $customer->email,
            'admin.phone' => '08123456789',
            'admin.password' => 'secure-admin-password',
        ]);

        try {
            app(AdminUserSeeder::class)->run();
            $this->fail('Expected the seeder to reject a customer email.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('sudah digunakan akun pelanggan', $exception->getMessage());
        }

        $this->assertSame('pelanggan', $customer->fresh()->role);
    }

    public function test_login_redirects_each_role_to_its_dashboard(): void
    {
        $customer = $this->createUser('pelanggan');
        $admin = $this->createUser('petugas', 'admin@example.com');

        $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect(route('customer.dashboard'));

        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_customers_and_admins_cannot_access_each_others_actions(): void
    {
        $customer = $this->createUser('pelanggan');
        $admin = $this->createUser('petugas', 'admin@example.com');

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('customer.dashboard'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('booking.detail', 1))
            ->assertForbidden();
    }

    public function test_admin_can_manage_courts_schedules_and_download_reports(): void
    {
        $admin = $this->createUser('petugas', 'admin@example.com');

        $this->actingAs($admin)
            ->post(route('admin.lapangan.store'), [
                'nama_lapangan' => 'Lapangan A',
                'jenis_lapangan' => 'Indoor',
                'fasilitas' => 'Lantai vinyl',
                'harga_per_jam' => 80000,
                'status_lapangan' => 'tersedia',
            ])
            ->assertRedirect();

        $lapangan = Lapangan::firstOrFail();

        $this->patch(route('admin.lapangan.update', $lapangan->id_lapangan), [
            'nama_lapangan' => 'Lapangan Premium',
            'jenis_lapangan' => 'Indoor',
            'fasilitas' => 'Lantai vinyl premium',
            'harga_per_jam' => 90000,
            'status_lapangan' => 'tersedia',
        ])->assertRedirect();

        $this->post(route('admin.jadwal.store'), [
            'id_lapangan' => $lapangan->id_lapangan,
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
        ])->assertRedirect();

        $this->post(route('admin.jadwal.store'), [
            'id_lapangan' => $lapangan->id_lapangan,
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
        ])->assertSessionHasErrors('jam_mulai');

        $this->assertDatabaseCount('jadwal', 1);
        $this->assertDatabaseHas('lapangan', [
            'id_lapangan' => $lapangan->id_lapangan,
            'harga_per_jam' => 90000,
            'fasilitas' => 'Lantai vinyl premium',
        ]);
        $this->assertDatabaseHas('jadwal', [
            'id_lapangan' => $lapangan->id_lapangan,
            'status_jadwal' => 'tersedia',
        ]);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Laporan transaksi dan pendapatan')
            ->assertSee('Kelola lapangan dan tarif');

        $this->get(route('admin.export', 'pelanggan'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    public function test_customer_can_book_only_an_admin_created_available_slot_once(): void
    {
        $customer = $this->createUser('pelanggan');
        $lapangan = Lapangan::create([
            'nama_lapangan' => 'Lapangan Premium',
            'jenis_lapangan' => 'Indoor',
            'fasilitas' => 'Vinyl flooring',
            'harga_per_jam' => 50000,
            'status_lapangan' => 'tersedia',
        ]);
        $idJadwal = DB::table('jadwal')->insertGetId([
            'id_lapangan' => $lapangan->id_lapangan,
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'status_jadwal' => 'tersedia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($customer)
            ->get(route('booking.detail', $lapangan->id_lapangan))
            ->assertOk()
            ->assertSee('08:00');

        $this->actingAs($customer)
            ->post(route('booking.form'), ['id_jadwal' => $idJadwal])
            ->assertOk()
            ->assertSee('Lapangan Premium');

        $this->post(route('booking.simpan'), ['id_jadwal' => $idJadwal])
            ->assertRedirect(route('pembayaran.index', ['id_booking' => 1]));

        $this->assertDatabaseHas('booking', [
            'id_user' => $customer->id_user,
            'id_jadwal' => $idJadwal,
            'total_harga' => 100000,
        ]);
        $this->assertDatabaseHas('jadwal', [
            'id_jadwal' => $idJadwal,
            'status_jadwal' => 'dipesan',
        ]);

        $this->actingAs($this->createUser('petugas', 'admin@example.com'))
            ->patch(route('admin.jadwal.update', $idJadwal), [
                'id_lapangan' => $lapangan->id_lapangan,
                'tanggal' => now()->addDays(2)->toDateString(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
            ])
            ->assertNotFound();

        $this->actingAs($customer)
            ->from(route('booking.detail', $lapangan->id_lapangan))
            ->post(route('booking.simpan'), ['id_jadwal' => $idJadwal])
            ->assertSessionHasErrors('id_jadwal');
    }

    private function createUser(string $role, string $email = 'customer@example.com'): User
    {
        return User::create([
            'nama_user' => $role === 'petugas' ? 'Admin' : 'Pelanggan',
            'email' => $email,
            'password' => Hash::make('password'),
            'no_telepon' => '08123456789',
            'role' => $role,
        ]);
    }
}
