
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen text-slate-900">

    <!-- NAVBAR -->
    <nav class="bg-[#07172c] text-white px-8 py-3 flex justify-between items-center">
        <a href="{{ url('/landing') }}" class="font-bold text-lg">
            BADMIN <span class="text-blue-500">GO</span>
        </a>

        <div class="flex gap-3 text-sm">
            <a href="{{ url('/landing') }}"
               class="bg-blue-600 px-5 py-2 rounded-md">
                Beranda
            </a>

            <a href="{{ route('booking.riwayat') }}"
               class="bg-blue-700 px-5 py-2 rounded-md">
                Riwayat
            </a>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="max-w-5xl mx-auto px-6 py-10">

        <h2 class="text-2xl font-bold mb-2">
            Riwayat Penyewaan
        </h2>

        <p class="text-slate-500 text-sm mb-8">
            Lihat riwayat pemesanan lapanganmu di sini.
        </p>

        @if ($riwayat->isEmpty())

            <!-- JIKA BELUM ADA BOOKING -->
            <div class="bg-white border border-slate-200 rounded-xl p-10 text-center">

                <div class="text-5xl mb-4">📋</div>

                <h3 class="font-bold text-lg mb-2">
                    Belum Ada Riwayat Booking
                </h3>

                <p class="text-sm text-slate-500 mb-6">
                    Kamu belum memiliki data penyewaan lapangan.
                </p>

                <a href="{{ route('katalog') }}"
                   class="inline-block bg-blue-600 hover:bg-blue-700
                          text-white px-6 py-3 rounded-lg font-semibold">
                    Cari Lapangan
                </a>

            </div>

        @else

            <!-- JIKA ADA BOOKING -->
            <div class="space-y-5">

                @foreach ($riwayat as $item)

                    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                            <div>
                                <p class="text-xs text-slate-400 mb-1">
                                    Kode Booking
                                </p>

                                <h3 class="font-bold text-blue-600 text-lg">
                                    BG-{{ date('Y') }}-{{ str_pad($item->id_booking, 5, '0', STR_PAD_LEFT) }}
                                </h3>

                                <h4 class="font-bold text-slate-800 mt-3">
                                    {{ $item->nama_lapangan }}
                                </h4>

                                <p class="text-sm text-slate-500 mt-1">
                                    Tanggal:
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </p>

                                <p class="text-sm text-slate-500">
                                    Jam:
                                    {{ substr($item->jam_mulai, 0, 5) }}
                                    -
                                    {{ substr($item->jam_selesai, 0, 5) }}
                                </p>

                                <p class="font-semibold text-slate-800 mt-3">
                                    Total:
                                    Rp{{ number_format($item->total_harga, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="flex flex-col items-start md:items-end gap-3">

                                <div>
                                    <p class="text-xs text-slate-400 mb-1">
                                        Status Booking
                                    </p>

                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $item->status_booking == 'menunggu' ? 'bg-yellow-100 text-yellow-700' : ($item->status_booking == 'dikonfirmasi' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600') }}">
                                        {{ ucfirst($item->status_booking) }}
                                    </span>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-400 mb-1">
                                        Status Pembayaran
                                    </p>

                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $item->status_pembayaran == 'diterima' ? 'bg-green-100 text-green-700' : ($item->status_pembayaran == 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ $item->status_pembayaran ? ucfirst($item->status_pembayaran) : 'Belum Dibayar' }}
                                    </span>
                                </div>

                                <a href="{{ route('pemesanan.bukti', ['id_booking' => $item->id_booking]) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white
                                          px-5 py-2 rounded-lg text-sm font-semibold">
                                    Lihat Bukti Pemesanan
                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </main>

</body>
</html>
