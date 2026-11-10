<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;

class LapanganController extends Controller
{
    // Fungsi untuk menampilkan daftar lapangan ke halaman katalog
    public function index()
    {
        // Ambil data dari database lewat Model
        $lapangan = Lapangan::all();

        // Tampilkan ke view
        return view('katalog', compact('lapangan'));
    }
}