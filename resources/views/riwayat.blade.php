<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - Badmin-Go</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-slate-800 border-b border-slate-700 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-xl font-bold text-emerald-400">Badmin-Go</h1>
        <div class="space-x-4 text-sm font-medium">
            <a href="{{ route('katalog') }}" class="text-slate-300 hover:text-white">Katalog</a>
            <a href="{{ route('booking.riwayat') }}" class="text-emerald-400">Riwayat Sewa</a>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-bold mb-2 text-white">Riwayat Pemesanan Saya</h2>
        <p class="text-slate-400 text-sm mb-6">Daftar transaksi dan status pemesanan lapangan Anda</p>

        <div class="space-y-4">
            @foreach($bookingList as $booking)
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 flex flex-col md:flex-row justify-between md:items-center gap-4 shadow-md">
                <div>
                    <h3 class="font-bold text-lg text-emerald-400 mb-1">{{ $booking->lapangan->nama_lapangan ?? 'Lapangan Badminton' }}</h3>
                    <p class="text-xs text-slate-400 mb-2">
                        Waktu Booking: <span class="text-slate-200">{{ $booking->tanggal_booking }}</span>
                    </p>
                    <p class="text-xs text-slate-400">
                        Total Biaya: <span class="text-white font-semibold">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                    </p>
                </div>

                <!-- Status Tag -->
                <div>
                    @if($booking->status_booking == 'menunggu_konfirmasi')
                        <span class="inline-block px-3 py-1.5 text-xs font-bold rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Menunggu Konfirmasi
                        </span>
                    @elseif($booking->status_booking == 'disetujui' || $booking->status_booking == 'berhasil')
                        <span class="inline-block px-3 py-1.5 text-xs font-bold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Pemesanan Berhasil
                        </span>
                    @else
                        <span class="inline-block px-3 py-1.5 text-xs font-bold rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            Dibatalkan
                        </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </main>

</body>
</html>