<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PetugasController;
use App\Http\Middleware\RequireAdmin;
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
Route::get('/customer/dashboard', [CustomerController::class, 'katalog'])
    ->middleware(RequireLoginForBooking::class)
    ->name('customer.dashboard');

Route::middleware(RequireLoginForBooking::class)->group(function () {
    Route::get('/booking/{id}', [CustomerController::class, 'detailBooking'])->name('booking.detail');
    Route::post('/booking/form', [CustomerController::class, 'formBooking'])->name('booking.form');
    Route::post('/booking/simpan', [CustomerController::class, 'simpanBooking'])->name('booking.simpan');
    Route::get('/riwayat-sewa', [CustomerController::class, 'riwayat'])->name('booking.riwayat');
    Route::get('/pembayaran/{id_booking}', [CustomerController::class, 'pembayaran'])->name('pembayaran.index');
    Route::post('/pembayaran/{id_booking}/upload', [CustomerController::class, 'uploadBuktiPembayaran'])->name('pembayaran.upload');
    Route::get('/status-pemesanan/{id_booking}', [CustomerController::class, 'statusPemesanan'])->name('pemesanan.status');
    Route::get('/bukti-pemesanan/{id_booking}', [CustomerController::class, 'buktiPemesanan'])->name('pemesanan.bukti');
});

Route::middleware(RequireAdmin::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/lapangan', [AdminController::class, 'storeLapangan'])->name('lapangan.store');
    Route::patch('/lapangan/{id}', [AdminController::class, 'updateLapangan'])->name('lapangan.update');
    Route::post('/jadwal', [AdminController::class, 'storeJadwal'])->name('jadwal.store');
    Route::patch('/jadwal/{id}', [AdminController::class, 'updateJadwal'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [AdminController::class, 'deleteJadwal'])->name('jadwal.delete');
    Route::post('/pembayaran/{id_pembayaran}/{status}', [PetugasController::class, 'verifikasiPembayaran'])
        ->name('pembayaran.verifikasi');
    Route::get('/export/{type}', [AdminController::class, 'export'])->name('export');
});

Route::middleware(RequireAdmin::class)->group(function () {
    Route::get('/petugas/dashboard', [AdminController::class, 'dashboard'])->name('petugas.dashboard');
    Route::post('/petugas/pembayaran/{id_pembayaran}/{status}', [PetugasController::class, 'verifikasiPembayaran'])
        ->name('petugas.verifikasi');
});
