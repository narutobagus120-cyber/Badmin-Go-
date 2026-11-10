
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pemesanan - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen text-slate-900">

    <nav class="bg-[#07172c] text-white px-8 py-3 flex justify-between items-center">
        <h1 class="font-bold text-lg">
            BADMIN <span class="text-blue-500">GO</span>
        </h1>

        <div class="flex gap-3 text-sm">
            <a href="{{ route('katalog') }}"
               class="bg-blue-600 px-5 py-2 rounded-md">Beranda</a>

            <a href="{{ route('booking.riwayat') }}"
               class="bg-blue-600 px-5 py-2 rounded-md">Riwayat</a>
        </div>
    </nav>

    <main class="max-w-2xl mx-auto px-6 py-10">

        <div class="bg-white border border-slate-200 rounded-xl p-8">

            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold mb-2">
                    Bukti Pemesanan
                </h2>

                <p class="text-sm text-slate-500">
                    BADMIN GO
                </p>
            </div>

            <div class="border-t border-b border-dashed py-5 mb-5">
                <div class="flex justify-between text-sm mb-3">
                    <span class="text-slate-500">Kode booking</span>
                    <span class="font-bold">{{ $kodeBooking }}</span>
                </div>

                <div class="flex justify-between text-sm mb-3">
                    <span class="text-slate-500">Nama lapangan</span>
                    <span class="font-semibold">{{ $pemesanan->nama_lapangan }}</span>
                </div>

                <div class="flex justify-between text-sm mb-3">
                    <span class="text-slate-500">Tanggal bermain</span>
                    <span class="font-semibold">
                        {{ \Carbon\Carbon::parse($pemesanan->tanggal)->format('d-m-Y') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm mb-3">
                    <span class="text-slate-500">Jam bermain</span>
                    <span class="font-semibold">
                        {{ substr($pemesanan->jam_mulai, 0, 5) }}
                        - {{ substr($pemesanan->jam_selesai, 0, 5) }}
                    </span>
                </div>

                <div class="flex justify-between text-sm mb-3">
                    <span class="text-slate-500">Metode pembayaran</span>
                    <span class="font-semibold">
                        {{ $pemesanan->metode_pembayaran ?? 'Belum dipilih' }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Status pembayaran</span>
                    <span class="font-semibold">
                        {{ $pemesanan->status_pembayaran ?? 'Belum ada pembayaran' }}
                    </span>
                </div>
            </div>

            <div class="flex justify-between items-center mb-8">
                <span class="font-semibold">Total pembayaran</span>
                <span class="text-xl font-bold text-blue-600">
                    Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                </span>
            </div>

            <div class="bg-blue-50 text-blue-700 p-4 rounded-lg text-sm mb-6">
                Ini merupakan bukti pemesanan lapangan, bukan bukti transfer atau pembayaran yang telah diverifikasi.
            </div>

            <div class="flex gap-3">
                <a href="{{ route('pemesanan.status', ['id_booking' => $pemesanan->id_booking]) }}"
                class="flex-1 text-center bg-slate-100 hover:bg-slate-200 py-3 rounded-lg text-sm font-semibold">
                    Kembali
                </a>

                <a href="{{ route('booking.riwayat') }}"
                class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg text-sm font-semibold">
                    Simpan
                </a>
            </div>

        </div>
    </main>

</body>
</html>
