<?php

namespace App\Http\Controllers;

use App\Models\User; // Menggunakan model User bawaan yang sudah diubah
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->role === 'petugas'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('customer.dashboard');
        }

        return view('auth.login');
    }

    // Menampilkan halaman form register
    public function showRegister(): View
    {
        return view('auth.register');
    }

    // Fungsi untuk memproses pendaftaran akun Pelanggan baru
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_user' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:user',
            'password' => 'required|string|min:6',
            'no_telepon' => 'required|string|max:15',
        ]);

        User::create([
            'nama_user' => $request->nama_user,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'no_telepon' => $request->no_telepon,
            'role' => 'pelanggan', // Otomatis jadi pelanggan
        ]);

        return redirect()->route('login')->with('success', 'Registrasi sukses! Silakan login.');
    }

    // Fungsi untuk memproses login masuk sistem
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Mencari user berdasarkan email di tabel user
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Jika password cocok, daftarkan session login
            Auth::login($user);
            $request->session()->regenerate();

            // Cek role untuk mengarahkan halaman
            if ($user->role === 'petugas') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'pelanggan') {
                return redirect()->route('customer.dashboard');
            }

            Auth::logout();

            return back()->withErrors([
                'loginError' => 'Peran akun tidak memiliki akses ke aplikasi.',
            ]);
        }

        return back()->withErrors(['loginError' => 'Email atau password salah!']);
    }

    // Fungsi untuk keluar aplikasi (Logout)
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil keluar dari akun.');
    }
}
