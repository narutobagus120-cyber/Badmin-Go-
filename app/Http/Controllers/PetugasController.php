<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    public function dashboard()
    {
        if (! Auth::check() || Auth::user()->role !== 'petugas') {
            abort(403, 'Akses hanya untuk petugas.');
        }

        $pembayaran = DB::table('pembayaran')
            ->join('booking', 'pembayaran.id_booking', '=', 'booking.id_booking')
            ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->join('user', 'booking.id_user', '=', 'user.id_user')
            ->select(
                'pembayaran.id_pembayaran',
                'pembayaran.id_booking',
                'pembayaran.metode_pembayaran',
                'pembayaran.bukti_pembayaran',
                'pembayaran.status_pembayaran',
                'pembayaran.tanggal_pembayaran',
                'booking.total_harga',
                'booking.status_booking',
                'jadwal.tanggal as tanggal_sewa',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'lapangan.nama_lapangan',
                'user.nama_user'
            )
            ->orderByDesc('pembayaran.created_at')
            ->get();

        return view('petugas.dashboard', compact('pembayaran'));
    }

    public function verifikasiPembayaran(Request $request, $id_pembayaran, $status)
    {
        if (! Auth::check() || Auth::user()->role !== 'petugas') {
            abort(403, 'Akses hanya untuk petugas.');
        }

        if (! in_array($status, ['berhasil', 'ditolak'], true)) {
            abort(400, 'Status verifikasi tidak valid.');
        }

        $pembayaran = DB::table('pembayaran')
            ->where('id_pembayaran', $id_pembayaran)
            ->first();

        if (! $pembayaran) {
            abort(404, 'Data pembayaran tidak ditemukan.');
        }

        DB::transaction(function () use ($pembayaran, $status) {
            DB::table('pembayaran')
                ->where('id_pembayaran', $pembayaran->id_pembayaran)
                ->update([
                    'status_pembayaran' => $status,
                    'updated_at' => now(),
                ]);

            DB::table('booking')
                ->where('id_booking', $pembayaran->id_booking)
                ->update([
                    'status_booking' => $status === 'berhasil' ? 'dikonfirmasi' : 'dibatalkan',
                    'updated_at' => now(),
                ]);
        });

        $message = $status === 'berhasil'
            ? 'Pembayaran berhasil diverifikasi.'
            : 'Pembayaran ditolak dan status booking diperbarui.';

        return redirect()->route('petugas.dashboard')->with('success', $message);
    }
}
