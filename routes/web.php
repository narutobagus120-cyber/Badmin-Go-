<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// 1. Rute Halaman Utama (Welcome bawaan Laravel)
Route::get('/', function () {
    return view('welcome');
});

// 2. Rute Proses Akun SMASH COURT (Di luar rute welcome)
Route::post('/register', [AuthController::class, 'register'])->name('register.process');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
