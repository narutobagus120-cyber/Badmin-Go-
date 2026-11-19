<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('admin.name');
        $email = config('admin.email');
        $phone = config('admin.phone');
        $password = config('admin.password');

        if (
            blank($name) &&
            blank($email) &&
            blank($phone) &&
            blank($password)
        ) {
            if ($this->command !== null) {
                $this->command->info('Admin seeder dilewati: kredensial ADMIN_* belum dikonfigurasi.');
            }

            return;
        }

        if (
            ! is_string($name) ||
            ! is_string($email) ||
            ! is_string($phone) ||
            ! is_string($password) ||
            trim($name) === '' ||
            trim($email) === '' ||
            trim($phone) === '' ||
            $password === ''
        ) {
            throw new RuntimeException('Isi ADMIN_NAME, ADMIN_EMAIL, ADMIN_PHONE, dan ADMIN_PASSWORD di .env.');
        }

        $name = trim($name);
        $email = trim($email);
        $phone = trim($phone);

        if (
            mb_strlen($name) > 100 ||
            filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
            mb_strlen($email) > 100 ||
            mb_strlen($phone) > 15 ||
            mb_strlen($password) < 12 ||
            mb_strlen($password) > 72
        ) {
            throw new RuntimeException('Data admin tidak valid. Kata sandi harus 12–72 karakter.');
        }

        $existingUser = User::where('email', $email)->first();
        if ($existingUser !== null) {
            if ($existingUser->role !== 'petugas') {
                throw new RuntimeException('ADMIN_EMAIL sudah digunakan akun pelanggan; gunakan email lain.');
            }

            if ($this->command !== null) {
                $this->command->info('Akun petugas dengan ADMIN_EMAIL tersebut sudah ada; kata sandinya tidak diubah.');
            }

            return;
        }

        User::create([
            'nama_user' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'no_telepon' => $phone,
            'role' => 'petugas',
        ]);

        if ($this->command !== null) {
            $this->command->info('Akun petugas berhasil dibuat.');
        }
    }
}
