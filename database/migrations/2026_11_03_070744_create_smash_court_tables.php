<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABEL USER
        Schema::create('user', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama_user', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('no_telepon', 15);
            $table->enum('role', ['pelanggan', 'petugas']);
            $table->timestamps();
        });

        // 2. TABEL LAPANGAN
        Schema::create('lapangan', function (Blueprint $table) {
            $table->id('id_lapangan');
            $table->string('nama_lapangan', 50);
            $table->string('jenis_lapangan', 50);
            $table->text('fasilitas');
            $table->decimal('harga_per_jam', 10, 2);
            $table->enum('status_lapangan', ['tersedia', 'tidak tersedia']);
            $table->timestamps();
        });

        // 3. TABEL JADWAL
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_lapangan')->constrained('lapangan', 'id_lapangan')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status_jadwal', ['tersedia', 'dipesan']);
            $table->timestamps();
        });

        // 4. TABEL BOOKING
        Schema::create('booking', function (Blueprint $table) {
            $table->id('id_booking');
            $table->foreignId('id_user')->constrained('user', 'id_user')->onDelete('cascade');
            $table->foreignId('id_jadwal')->constrained('jadwal', 'id_jadwal')->onDelete('cascade');
            $table->dateTime('tanggal_booking');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status_booking', ['menunggu', 'dikonfirmasi', 'dibatalkan']);
            $table->timestamps();
        });

        // 5. TABEL PEMBAYARAN
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_booking')->constrained('booking', 'id_booking')->onDelete('cascade');
            $table->dateTime('tanggal_pembayaran');
            $table->string('metode_pembayaran', 30);
            $table->string('bukti_pembayaran', 255);
            $table->enum('status_pembayaran', ['menunggu', 'berhasil', 'ditolak']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('booking');
        Schema::dropIfExists('jadwal');
        Schema::dropIfExists('lapangan');
        Schema::dropIfExists('user');
    }
};
