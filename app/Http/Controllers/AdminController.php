<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $lapangan = Lapangan::orderBy('nama_lapangan')->get();

        $jadwal = DB::table('jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->whereDate('jadwal.tanggal', '>=', now()->toDateString())
            ->whereDate('jadwal.tanggal', '<=', now()->addDays(30)->toDateString())
            ->select('jadwal.*', 'lapangan.nama_lapangan')
            ->orderBy('jadwal.tanggal')
            ->orderBy('jadwal.jam_mulai')
            ->get();

        $pelanggan = User::query()
            ->where('role', 'pelanggan')
            ->leftJoin('booking', 'user.id_user', '=', 'booking.id_user')
            ->select(
                'user.id_user',
                'user.nama_user',
                'user.email',
                'user.no_telepon'
            )
            ->selectRaw('COUNT(booking.id_booking) as jumlah_booking')
            ->groupBy('user.id_user', 'user.nama_user', 'user.email', 'user.no_telepon')
            ->orderBy('user.nama_user')
            ->get();

        $bookingQuery = DB::table('booking')
            ->join('user', 'booking.id_user', '=', 'user.id_user')
            ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->leftJoin('pembayaran', 'booking.id_booking', '=', 'pembayaran.id_booking')
            ->select(
                'booking.id_booking',
                'booking.total_harga',
                'booking.status_booking',
                'user.nama_user',
                'lapangan.nama_lapangan',
                'jadwal.tanggal',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'pembayaran.id_pembayaran',
                'pembayaran.status_pembayaran'
            );

        $booking = (clone $bookingQuery)
            ->orderByDesc('booking.id_booking')
            ->get();

        $pemesananTerbaru = (clone $bookingQuery)
            ->orderByDesc('booking.id_booking')
            ->limit(5)
            ->get();

        $transaksi = DB::table('pembayaran')
            ->join('booking', 'pembayaran.id_booking', '=', 'booking.id_booking')
            ->join('user', 'booking.id_user', '=', 'user.id_user')
            ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
            ->select(
                'pembayaran.id_pembayaran',
                'pembayaran.tanggal_pembayaran',
                'pembayaran.metode_pembayaran',
                'pembayaran.status_pembayaran',
                'booking.id_booking',
                'booking.total_harga',
                'user.nama_user',
                'lapangan.nama_lapangan'
            )
            ->orderByDesc('pembayaran.tanggal_pembayaran')
            ->get();

        $pendapatanHariIni = DB::table('pembayaran')
            ->join('booking', 'pembayaran.id_booking', '=', 'booking.id_booking')
            ->where('pembayaran.status_pembayaran', 'berhasil')
            ->whereDate('pembayaran.tanggal_pembayaran', now()->toDateString())
            ->sum('booking.total_harga');

        return view('admin.dashboard', [
            'lapangan' => $lapangan,
            'jadwal' => $jadwal,
            'pelanggan' => $pelanggan,
            'booking' => $booking,
            'pemesananTerbaru' => $pemesananTerbaru,
            'transaksi' => $transaksi,
            'jumlahBooking' => DB::table('booking')->count(),
            'jumlahLapanganAktif' => Lapangan::where('status_lapangan', 'tersedia')->count(),
            'jumlahPelanggan' => User::where('role', 'pelanggan')->count(),
            'pendapatanHariIni' => $pendapatanHariIni,
        ]);
    }

    public function storeLapangan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lapangan' => 'required|string|max:50',
            'jenis_lapangan' => 'required|string|max:50',
            'fasilitas' => 'nullable|string|max:5000',
            'harga_per_jam' => 'required|numeric|min:1|max:99999999.99',
            'status_lapangan' => 'required|in:tersedia,tidak tersedia',
        ]);

        Lapangan::create($validated);

        return redirect()->to(route('admin.dashboard').'#manajemen-lapangan')
            ->with('success', 'Lapangan berhasil ditambahkan.');
    }

    public function updateLapangan(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lapangan' => 'required|string|max:50',
            'jenis_lapangan' => 'required|string|max:50',
            'fasilitas' => 'nullable|string|max:5000',
            'harga_per_jam' => 'required|numeric|min:1|max:99999999.99',
            'status_lapangan' => 'required|in:tersedia,tidak tersedia',
        ]);

        Lapangan::findOrFail($id)->update($validated);

        return redirect()->to(route('admin.dashboard').'#manajemen-lapangan')
            ->with('success', 'Data lapangan berhasil diperbarui.');
    }

    public function storeJadwal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_lapangan' => 'required|exists:lapangan,id_lapangan',
            'tanggal' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:'.now()->addDays(30)->toDateString(),
            ],
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
        ]);

        $this->validateScheduleDuration($validated['jam_mulai'], $validated['jam_selesai']);

        DB::transaction(function () use ($validated): void {
            $lapangan = Lapangan::where('id_lapangan', $validated['id_lapangan'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lapangan->status_lapangan !== 'tersedia') {
                throw ValidationException::withMessages([
                    'id_lapangan' => 'Lapangan yang tidak aktif tidak dapat diberi jadwal.',
                ]);
            }

            $this->ensureScheduleDoesNotOverlap(
                (int) $validated['id_lapangan'],
                $validated['tanggal'],
                $validated['jam_mulai'],
                $validated['jam_selesai']
            );

            DB::table('jadwal')->insert([
                'id_lapangan' => $validated['id_lapangan'],
                'tanggal' => $validated['tanggal'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'status_jadwal' => 'tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->to(route('admin.dashboard').'#manajemen-jadwal')
            ->with('success', 'Jadwal tersedia berhasil ditambahkan.');
    }

    public function updateJadwal(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_lapangan' => 'required|exists:lapangan,id_lapangan',
            'tanggal' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:'.now()->addDays(30)->toDateString(),
            ],
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
        ]);

        $this->validateScheduleDuration($validated['jam_mulai'], $validated['jam_selesai']);

        DB::transaction(function () use ($validated, $id): void {
            $jadwal = DB::table('jadwal')
                ->where('id_jadwal', $id)
                ->where('status_jadwal', 'tersedia')
                ->lockForUpdate()
                ->first();

            if (! $jadwal) {
                abort(404, 'Jadwal tersedia tidak ditemukan.');
            }

            $lapangan = Lapangan::where('id_lapangan', $validated['id_lapangan'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lapangan->status_lapangan !== 'tersedia') {
                throw ValidationException::withMessages([
                    'id_lapangan' => 'Lapangan yang tidak aktif tidak dapat diberi jadwal.',
                ]);
            }

            $this->ensureScheduleDoesNotOverlap(
                (int) $validated['id_lapangan'],
                $validated['tanggal'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $id
            );

            DB::table('jadwal')
                ->where('id_jadwal', $id)
                ->update([
                    'id_lapangan' => $validated['id_lapangan'],
                    'tanggal' => $validated['tanggal'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'updated_at' => now(),
                ]);
        });

        return redirect()->to(route('admin.dashboard').'#manajemen-jadwal')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function deleteJadwal(int $id): RedirectResponse
    {
        $deleted = DB::table('jadwal')
            ->where('id_jadwal', $id)
            ->where('status_jadwal', 'tersedia')
            ->delete();

        if (! $deleted) {
            abort(404, 'Jadwal tersedia tidak ditemukan atau sudah dipesan.');
        }

        return redirect()->to(route('admin.dashboard').'#manajemen-jadwal')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    public function export(string $type): StreamedResponse
    {
        [$headers, $rows] = match ($type) {
            'pelanggan' => [
                ['Nama pelanggan', 'Email', 'Telepon', 'Jumlah booking'],
                DB::table('user')
                    ->leftJoin('booking', 'user.id_user', '=', 'booking.id_user')
                    ->where('user.role', 'pelanggan')
                    ->select('user.nama_user', 'user.email', 'user.no_telepon')
                    ->selectRaw('COUNT(booking.id_booking) as jumlah_booking')
                    ->groupBy('user.id_user', 'user.nama_user', 'user.email', 'user.no_telepon')
                    ->get()
                    ->map(fn (object $row): array => [
                        $row->nama_user,
                        $row->email,
                        $row->no_telepon,
                        $row->jumlah_booking,
                    ]),
            ],
            'booking' => [
                ['Kode booking', 'Pelanggan', 'Lapangan', 'Tanggal', 'Jam mulai', 'Jam selesai', 'Total', 'Status'],
                DB::table('booking')
                    ->join('user', 'booking.id_user', '=', 'user.id_user')
                    ->join('jadwal', 'booking.id_jadwal', '=', 'jadwal.id_jadwal')
                    ->join('lapangan', 'jadwal.id_lapangan', '=', 'lapangan.id_lapangan')
                    ->select(
                        'booking.id_booking',
                        'user.nama_user',
                        'lapangan.nama_lapangan',
                        'jadwal.tanggal',
                        'jadwal.jam_mulai',
                        'jadwal.jam_selesai',
                        'booking.total_harga',
                        'booking.status_booking'
                    )
                    ->orderBy('booking.id_booking')
                    ->get()
                    ->map(fn (object $row): array => [
                        'BG-'.date('Y').'-'.str_pad($row->id_booking, 5, '0', STR_PAD_LEFT),
                        $row->nama_user,
                        $row->nama_lapangan,
                        $row->tanggal,
                        $row->jam_mulai,
                        $row->jam_selesai,
                        $row->total_harga,
                        $row->status_booking,
                    ]),
            ],
            'transaksi' => [
                ['Tanggal', 'Kode transaksi', 'Pelanggan', 'Metode', 'Nominal', 'Status'],
                DB::table('pembayaran')
                    ->join('booking', 'pembayaran.id_booking', '=', 'booking.id_booking')
                    ->join('user', 'booking.id_user', '=', 'user.id_user')
                    ->select(
                        'pembayaran.id_pembayaran',
                        'pembayaran.tanggal_pembayaran',
                        'user.nama_user',
                        'pembayaran.metode_pembayaran',
                        'booking.total_harga',
                        'pembayaran.status_pembayaran'
                    )
                    ->orderBy('pembayaran.id_pembayaran')
                    ->get()
                    ->map(fn (object $row): array => [
                        $row->tanggal_pembayaran,
                        'TRX-'.str_pad($row->id_pembayaran, 5, '0', STR_PAD_LEFT),
                        $row->nama_user,
                        $row->metode_pembayaran,
                        $row->total_harga,
                        $row->status_pembayaran,
                    ]),
            ],
            default => throw ValidationException::withMessages([
                'type' => 'Jenis data yang diminta tidak valid.',
            ]),
        };

        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers, escape: '');

            foreach ($rows as $row) {
                $safeRow = array_map(function (mixed $value): mixed {
                    if (is_string($value) && preg_match('/^[=+\-@]/', $value) === 1) {
                        return "'".$value;
                    }

                    return $value;
                }, $row);

                fputcsv($output, $safeRow, escape: '');
            }

            fclose($output);
        }, "badmin-{$type}.csv", ['Content-Type' => 'text/csv']);
    }

    private function validateScheduleDuration(string $jamMulai, string $jamSelesai): void
    {
        $mulai = Carbon::createFromFormat('H:i', $jamMulai);
        $selesai = Carbon::createFromFormat('H:i', $jamSelesai);
        $durasiMenit = $mulai->diffInMinutes($selesai);

        if (
            $selesai <= $mulai ||
            $mulai->minute !== 0 ||
            $selesai->minute !== 0 ||
            $mulai->hour < 8 ||
            $selesai->hour > 22 ||
            $durasiMenit < 60 ||
            $durasiMenit > 840
        ) {
            throw ValidationException::withMessages([
                'jam_selesai' => 'Jadwal harus berdurasi 1–14 jam dalam jam operasional 08.00–22.00.',
            ]);
        }
    }

    private function ensureScheduleDoesNotOverlap(
        int $idLapangan,
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $exceptId = null
    ): void {
        $jadwalBentrok = DB::table('jadwal')
            ->where('id_lapangan', $idLapangan)
            ->whereDate('tanggal', $tanggal)
            ->whereTime('jam_mulai', '<', $jamSelesai)
            ->whereTime('jam_selesai', '>', $jamMulai)
            ->when($exceptId !== null, function ($query) use ($exceptId) {
                $query->where('id_jadwal', '!=', $exceptId);
            })
            ->exists();

        if ($jadwalBentrok) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jadwal bertabrakan dengan slot lain di lapangan tersebut.',
            ]);
        }
    }
}
