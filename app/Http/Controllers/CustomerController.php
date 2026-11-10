<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CustomerController extends Controller
{
    // ==========================================
    // KATALOG LAPANGAN
    // ==========================================
    public function katalog()
    {
        $lapangan = Lapangan::all();

        return view('customer.katalog', compact('lapangan'));
    }

    // ==========================================
    // DETAIL LAPANGAN
    // ==========================================
    public function detailBooking($id)
    {
        $lapangan = Lapangan::findOrFail($id);

        return view('customer.booking_detail', compact('lapangan'));
    }

    // ==========================================
    // VALIDASI WAKTU BOOKING
    // ==========================================
   private function validasiWaktu($tanggal, $jamMulai, $durasi)
{
    // Ubah durasi menjadi angka
    $durasi = (int) $durasi;

    $mulai = Carbon::createFromFormat('H:i', $jamMulai);

    // Jam mulai harus tepat pada kelipatan 1 jam
    if ($mulai->minute !== 0) {
        throw ValidationException::withMessages([
            'jam_mulai' => 'Jam mulai harus pada kelipatan satu jam.'
        ]);
    }

    // Hitung jam selesai
    $selesai = $mulai->copy()->addHours($durasi);

    // Jam operasional 08.00 - 22.00
    if (
        $mulai->hour < 8 ||
        $selesai->hour > 22
    ) {
        throw ValidationException::withMessages([
            'durasi' => 'Booking hanya tersedia pukul 08.00 sampai 22.00.'
        ]);
    }

    // Tidak boleh booking pada jam yang sudah lewat
    if (
        $tanggal === now()->toDateString() &&
        $mulai->format('H:i') <= now()->format('H:i')
    ) {
        throw ValidationException::withMessages([
            'jam_mulai' => 'Jam yang dipilih sudah lewat.'
        ]);
    }

    return [
        'jam_mulai' => $mulai->format('H:i:s'),
        'jam_selesai' => $selesai->format('H:i:s'),
    ];
}

    // ==========================================
    // CEK BENTROK JADWAL
    // ==========================================
    private function cekBentrok(
        $idLapangan,
        $tanggal,
        $jamMulai,
        $jamSelesai
    ) {
        return DB::table('jadwal')
            ->where('id_lapangan', $idLapangan)
            ->whereDate('tanggal', $tanggal)
            ->where('status_jadwal', 'dipesan')
            ->whereTime('jam_mulai', '<', $jamSelesai)
            ->whereTime('jam_selesai', '>', $jamMulai)
            ->exists();
    }

    // ==========================================
    // FORM BOOKING
    // ==========================================
    public function formBooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'id_lapangan' => 'required|exists:lapangan,id_lapangan',

            'tanggal' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addDays(30)->toDateString(),
            ],

            'jam_mulai' => 'required|date_format:H:i',
            'durasi' => 'required|integer|min:1|max:14',
        ]);

        $lapangan = Lapangan::findOrFail(
            $validated['id_lapangan']
        );

        if ($lapangan->status_lapangan !== 'tersedia') {
            return back()->withInput()->withErrors([
                'id_lapangan' => 'Lapangan sedang tidak tersedia.'
            ]);
        }

        // Validasi waktu dan durasi
        $waktu = $this->validasiWaktu(
            $validated['tanggal'],
            $validated['jam_mulai'],
            $validated['durasi']
        );

        // Periksa jadwal bentrok
        if ($this->cekBentrok(
            $lapangan->id_lapangan,
            $validated['tanggal'],
            $waktu['jam_mulai'],
            $waktu['jam_selesai']
        )) {
            return back()->withInput()->withErrors([
                'jam_mulai' => 'Jadwal sudah dipesan. Silakan pilih jam lain.'
            ]);
        }

        // Hitung total harga
        $totalHarga = $lapangan->harga_per_jam
            * $validated['durasi'];

        // Data jadwal sementara untuk tampilan
        $jadwal = (object) [
            'id_jadwal' => null,
            'id_lapangan' => $lapangan->id_lapangan,
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $waktu['jam_mulai'],
            'jam_selesai' => $waktu['jam_selesai'],
            'durasi' => $validated['durasi'],
        ];

        return view('customer.form_booking', [
            'lapangan' => $lapangan,
            'jadwal' => $jadwal,
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $waktu['jam_selesai'],
            'durasi' => $validated['durasi'],
            'totalHarga' => $totalHarga,
            'user' => Auth::user(),
        ]);
    }

    // ==========================================
    // SIMPAN BOOKING
    // ==========================================
    public function simpanBooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'id_lapangan' => 'required|exists:lapangan,id_lapangan',

            'tanggal' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addDays(30)->toDateString(),
            ],

            'jam_mulai' => 'required|date_format:H:i',
            'durasi' => 'required|integer|min:1|max:14',
        ]);

        $idUser = Auth::id();

        $idBooking = DB::transaction(function () use (
            $validated,
            $idUser
        ) {

            // Kunci data lapangan agar booking bersamaan
            // pada lapangan yang sama diproses bergantian.
            $lapangan = Lapangan::where(
                'id_lapangan',
                $validated['id_lapangan']
            )->lockForUpdate()->firstOrFail();

            if ($lapangan->status_lapangan !== 'tersedia') {
                throw ValidationException::withMessages([
                    'id_lapangan' => 'Lapangan tidak tersedia.'
                ]);
            }

            // Validasi ulang waktu
            $waktu = $this->validasiWaktu(
                $validated['tanggal'],
                $validated['jam_mulai'],
                $validated['durasi']
            );

            // Cek ulang bentrok sebelum menyimpan
            if ($this->cekBentrok(
                $lapangan->id_lapangan,
                $validated['tanggal'],
                $waktu['jam_mulai'],
                $waktu['jam_selesai']
            )) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Jadwal sudah dipesan. Silakan pilih waktu lain.'
                ]);
            }

            // Hitung harga otomatis
            $totalHarga = $lapangan->harga_per_jam
                * $validated['durasi'];

            // Buat jadwal baru secara otomatis
            $idJadwal = DB::table('jadwal')->insertGetId([
                'id_lapangan' => $lapangan->id_lapangan,
                'tanggal' => $validated['tanggal'],
                'jam_mulai' => $waktu['jam_mulai'],
                'jam_selesai' => $waktu['jam_selesai'],
                'status_jadwal' => 'dipesan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Simpan data booking
            $idBooking = DB::table('booking')->insertGetId([
                'id_user' => $idUser,
                'id_jadwal' => $idJadwal,
                'tanggal_booking' => now(),
                'total_harga' => $totalHarga,
                'status_booking' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $idBooking;
        });

        return redirect()
            ->route('pembayaran.index', [
                'id_booking' => $idBooking
            ])
            ->with(
                'success',
                'Booking berhasil disimpan. Silakan lanjutkan pembayaran.'
            );
    }

    // ==========================================
    // HALAMAN PEMBAYARAN
    // ==========================================
    public function pembayaran($id_booking)
    {
        if (!Auth::check()) {
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

        if (!$pemesanan) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        $kodeBooking = 'BG-' . date('Y') . '-' .
            str_pad($pemesanan->id_booking, 5, '0', STR_PAD_LEFT);

        return view('pembayaran.index', compact(
            'pemesanan',
            'kodeBooking'
        ));
    }

    // ==========================================
    // STATUS PEMESANAN
    // ==========================================
    public function statusPemesanan($id_booking)
    {
        if (!Auth::check()) {
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

        if (!$pemesanan) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        $kodeBooking = 'BG-' . date('Y') . '-' .
            str_pad($pemesanan->id_booking, 5, '0', STR_PAD_LEFT);

        return view('customer.pemesanan.status', compact(
            'pemesanan',
            'kodeBooking'
        ));
    }

    // ==========================================
    // BUKTI PEMESANAN
    // ==========================================
    public function buktiPemesanan($id_booking)
    {
        if (!Auth::check()) {
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

        if (!$pemesanan) {
            abort(404, 'Bukti pemesanan tidak ditemukan.');
        }

        $kodeBooking = 'BG-' . date('Y') . '-' .
            str_pad($pemesanan->id_booking, 5, '0', STR_PAD_LEFT);

        return view('customer.pemesanan.bukti', compact(
            'pemesanan',
            'kodeBooking'
        ));
    }

    // ==========================================
    // RIWAYAT BOOKING
    // ==========================================
    public function riwayat()
    {
        if (!Auth::check()) {
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