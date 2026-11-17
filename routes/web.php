<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PetugasController;
use App\Http\Middleware\RequireLoginForBooking;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('landing');
});
Route::get('/landing', function () {
    return view('landing');
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Halaman Sisi Pelanggan & Katalog (Badmin-Go)
// Menggunakan CustomerController untuk halaman katalog
Route::get('/katalog', [CustomerController::class, 'katalog'])->name('katalog');
Route::middleware(RequireLoginForBooking::class)->group(function () {
    Route::get('/booking/{id}', [CustomerController::class, 'detailBooking'])->name('booking.detail');
    Route::post('/booking/form', [CustomerController::class, 'formBooking'])->name('booking.form');
    Route::post('/booking/simpan', [CustomerController::class, 'simpanBooking'])->name('booking.simpan');
});
Route::get('/riwayat-sewa', [CustomerController::class, 'riwayat'])->name('booking.riwayat');
Route::get('/pembayaran/{id_booking}', [CustomerController::class, 'pembayaran'])->name('pembayaran.index');
Route::post('/pembayaran/{id_booking}/upload', [CustomerController::class, 'uploadBuktiPembayaran'])->name('pembayaran.upload');
Route::get('/status-pemesanan/{id_booking}', [CustomerController::class, 'statusPemesanan'])->name('pemesanan.status');
Route::get('/bukti-pemesanan/{id_booking}', [CustomerController::class, 'buktiPemesanan'])->name('pemesanan.bukti');

Route::get('/petugas/dashboard', [PetugasController::class, 'dashboard'])->name('petugas.dashboard');
Route::post('/petugas/pembayaran/{id_pembayaran}/{status}', [PetugasController::class, 'verifikasiPembayaran'])->name('petugas.verifikasi');
