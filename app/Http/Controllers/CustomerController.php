<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    // ==========================================
    // KATALOG LAPANGAN
    // ==========================================
    public function katalog(): View
    {
        $lapangan = Lapangan::where('status_lapangan', 'tersedia')->get();

        return view('customer.katalog', compact('lapangan'));
    }

    // ==========================================
    // DETAIL LAPANGAN
    // ==========================================
    public function detailBooking(int $id): View
    {
        $lapangan = Lapangan::findOrFail($id);
        abort_if($lapangan->status_lapangan !== 'tersedia', 404);

        $jadwal = DB::table('jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->where('jadwal.id_lapangan', $lapangan->id_lapangan)
            ->where('lapangan.status_lapangan', 'tersedia')
            ->where('jadwal.status_jadwal', 'tersedia')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->whereDate('tanggal', '<=', now()->addDays(30)->toDateString())
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('customer.booking_detail', compact('lapangan', 'jadwal'));
    }

    // ==========================================
    // FORM BOOKING
    // ==========================================
    public function formBooking(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'id_jadwal' => 'required|integer|exists:jadwal,id_jadwal',
        ]);

        $jadwal = DB::table('jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->where('jadwal.id_jadwal', $validated['id_jadwal'])
            ->where('jadwal.status_jadwal', 'tersedia')
            ->where('lapangan.status_lapangan', 'tersedia')
            ->select('jadwal.*')
            ->first();

        if (! $jadwal) {
            throw ValidationException::withMessages([
                'id_jadwal' => 'Jadwal tidak tersedia. Silakan pilih jadwal lain.',
            ]);
        }

        $tanggalJadwal = Carbon::parse($jadwal->tanggal)->toDateString();
        if (
            $tanggalJadwal < now()->toDateString() ||
            $tanggalJadwal > now()->addDays(30)->toDateString()
        ) {
            throw ValidationException::withMessages([
                'id_jadwal' => 'Jadwal berada di luar periode pemesanan.',
            ]);
        }

        $lapangan = Lapangan::findOrFail($jadwal->id_lapangan);
        $durasi = $this->hitungDurasiJadwal($jadwal->jam_mulai, $jadwal->jam_selesai);
        $totalHarga = $lapangan->harga_per_jam * $durasi;

        return view('customer.form_booking', [
            'lapangan' => $lapangan,
            'jadwal' => $jadwal,
            'tanggal' => $tanggalJadwal,
            'jam_mulai' => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
            'durasi' => $durasi,
            'totalHarga' => $totalHarga,
            'user' => Auth::user(),
        ]);
    }

    // ==========================================
    // SIMPAN BOOKING
    // ==========================================
    public function simpanBooking(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'id_jadwal' => 'required|integer|exists:jadwal,id_jadwal',
        ]);

        $idUser = Auth::id();

        $idBooking = DB::transaction(function () use (
            $validated,
            $idUser
        ) {
            $jadwal = DB::table('jadwal')
                ->where('id_jadwal', $validated['id_jadwal'])
                ->lockForUpdate()
                ->first();

            if (! $jadwal || $jadwal->status_jadwal !== 'tersedia') {
                throw ValidationException::withMessages([
                    'id_jadwal' => 'Jadwal sudah tidak tersedia. Silakan pilih jadwal lain.',
                ]);
            }

            $tanggalJadwal = Carbon::parse($jadwal->tanggal)->toDateString();
            if (
                $tanggalJadwal < now()->toDateString() ||
                $tanggalJadwal > now()->addDays(30)->toDateString()
            ) {
                throw ValidationException::withMessages([
                    'id_jadwal' => 'Jadwal berada di luar periode pemesanan.',
                ]);
            }

            $lapangan = Lapangan::where('id_lapangan', $jadwal->id_lapangan)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lapangan->status_lapangan !== 'tersedia') {
                throw ValidationException::withMessages([
                    'id_jadwal' => 'Lapangan tidak tersedia.',
                ]);
            }

            $durasi = $this->hitungDurasiJadwal($jadwal->jam_mulai, $jadwal->jam_selesai);

            $totalHarga = $lapangan->harga_per_jam * $durasi;

            DB::table('jadwal')
                ->where('id_jadwal', $jadwal->id_jadwal)
                ->update([
                    'status_jadwal' => 'dipesan',
                    'updated_at' => now(),
                ]);

            return DB::table('booking')->insertGetId([
                'id_user' => $idUser,
                'id_jadwal' => $jadwal->id_jadwal,
                'tanggal_booking' => now(),
                'total_harga' => $totalHarga,
                'status_booking' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('pembayaran.index', [
                'id_booking' => $idBooking,
            ])
            ->with(
                'success',
                'Booking berhasil disimpan. Silakan lanjutkan pembayaran.'
            );
    }

    private function hitungDurasiJadwal(string $jamMulai, string $jamSelesai): int
    {
        $mulai = Carbon::parse($jamMulai);
        $selesai = Carbon::parse($jamSelesai);

        if (
            $selesai <= $mulai ||
            $mulai->minute !== 0 ||
            $selesai->minute !== 0 ||
            $mulai->hour < 8 ||
            $selesai->hour > 22
        ) {
            throw ValidationException::withMessages([
                'id_jadwal' => 'Durasi jadwal tidak valid.',
            ]);
        }

        $durasi = (int) $mulai->diffInHours($selesai);

        if ($durasi < 1 || $durasi > 14) {
            throw ValidationException::withMessages([
                'id_jadwal' => 'Durasi jadwal tidak valid.',
            ]);
        }

        return $durasi;
    }

    // ==========================================
    // HALAMAN PEMBAYARAN
    // ==========================================
    public function pembayaran(int $id_booking): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $pemesanan = DB::table('booking')
            ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->where('booking.id_booking', $id_booking)
            ->where('booking.id_user', Auth::id())
            ->select(
                'booking.*',
                'jadwal.tanggal as tanggal_sewa',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'lapangan.nama_lapangan'
            )
            ->first();

        if (! $pemesanan) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        $kodeBooking = 'BG-'.date('Y').'-'.
            str_pad($pemesanan->id_booking, 5, '0', STR_PAD_LEFT);

        return view('pembayaran.index', compact(
            'pemesanan',
            'kodeBooking'
        ));
    }

    // ==========================================
    // UPLOAD BUKTI PEMBAYARAN
    // ==========================================
    public function uploadBuktiPembayaran(Request $request, int $id_booking): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $booking = DB::table('booking')
            ->where('id_booking', $id_booking)
            ->where('id_user', Auth::id())
            ->first();

        if (! $booking) {
            abort(404, 'Data booking tidak ditemukan.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|string|max:30',
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        DB::table('pembayaran')->updateOrInsert(
            ['id_booking' => $id_booking],
            [
                'tanggal_pembayaran' => now(),
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'bukti_pembayaran' => $path,
                'status_pembayaran' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('booking')
            ->where('id_booking', $id_booking)
            ->update([
                'status_booking' => 'menunggu',
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('pemesanan.status', ['id_booking' => $id_booking])
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi petugas.');
    }

    // ==========================================
    // STATUS PEMESANAN
    // ==========================================
    public function statusPemesanan(int $id_booking): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $pemesanan = DB::table('booking')
            ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->leftJoin('pembayaran', 'booking.id_booking', '=', 'pembayaran.id_booking')
            ->where('booking.id_booking', $id_booking)
            ->where('booking.id_user', Auth::id())
            ->select(
                'booking.id_booking',
                'booking.status_booking',
                'booking.total_harga',
                'jadwal.tanggal',
                'lapangan.nama_lapangan',
                'pembayaran.status_pembayaran'
            )
            ->first();

        if (! $pemesanan) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        $kodeBooking = 'BG-'.date('Y').'-'.
            str_pad($pemesanan->id_booking, 5, '0', STR_PAD_LEFT);

        return view('customer.pemesanan.status', compact(
            'pemesanan',
            'kodeBooking'
        ));
    }

    // ==========================================
    // BUKTI PEMESANAN
    // ==========================================
    public function buktiPemesanan(int $id_booking): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $pemesanan = DB::table('booking')
            ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->leftJoin('pembayaran', 'booking.id_booking', '=', 'pembayaran.id_booking')
            ->where('booking.id_booking', $id_booking)
            ->where('booking.id_user', Auth::id())
            ->select(
                'booking.*',
                'jadwal.tanggal',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'lapangan.nama_lapangan',
                'lapangan.jenis_lapangan',
                'pembayaran.metode_pembayaran',
                'pembayaran.status_pembayaran'
            )
            ->first();

        if (! $pemesanan) {
            abort(404, 'Bukti pemesanan tidak ditemukan.');
        }

        $kodeBooking = 'BG-'.date('Y').'-'.
            str_pad($pemesanan->id_booking, 5, '0', STR_PAD_LEFT);

        return view('customer.pemesanan.bukti', compact(
            'pemesanan',
            'kodeBooking'
        ));
    }

    // ==========================================
    // RIWAYAT BOOKING
    // ==========================================
    public function riwayat(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $riwayat = DB::table('booking')
            ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->leftJoin('pembayaran', 'booking.id_booking', '=', 'pembayaran.id_booking')
            ->where('booking.id_user', Auth::id())
            ->select(
                'booking.id_booking',
                'booking.total_harga',
                'booking.status_booking',
                'jadwal.tanggal',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'lapangan.nama_lapangan',
                'pembayaran.status_pembayaran'
            )
            ->orderByDesc('booking.id_booking')
            ->get();

        return view('customer.riwayat', compact('riwayat'));
    }
}
