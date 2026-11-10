
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f3f6fb] min-h-screen text-[#0b1b30]">

    <nav class="bg-[#07172b] text-white px-8 py-3 flex items-center justify-between">
        <h1 class="font-bold text-lg">
            BADMIN <span class="text-blue-500">GO</span>
        </h1>

        <div class="flex gap-2 text-sm">
            <a href="{{ route('katalog') }}"
               class="bg-blue-600 px-5 py-2 rounded-md">Beranda</a>

            <a href="{{ route('booking.riwayat') }}"
               class="bg-blue-600 px-5 py-2 rounded-md">Riwayat</a>

            <a href="{{ route('login') }}"
               class="bg-blue-600 px-5 py-2 rounded-md">Akun</a>
        </div>
    </nav>

    <main class="px-5 md:px-12 py-7">

        <h1 class="text-2xl font-bold mb-7">
            Selesaikan pembayaran
        </h1>

        <section class="bg-white border border-slate-200 rounded-xl
                        max-w-3xl mx-auto min-h-[400px] p-8 md:p-10">

            <h2 class="font-bold text-base mb-7">
                Instruksi pembayaran
            </h2>

            <p class="text-xs text-slate-500 mb-9">
                Kode booking: {{ $kodeBooking }}
            </p>

            <p class="text-xs text-slate-500 mb-3">
                Total pembayaran
            </p>

            <h1 class="text-2xl font-bold mb-6">
                Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}
            </h1>

            <div class="bg-slate-100 rounded-lg p-5 max-w-sm mb-6">
                <p class="font-semibold text-xs">
                    Transfer bank / pembayaran manual
                </p>
            </div>

            <p class="text-xs text-slate-500 mb-5">
                Lapangan: {{ $pemesanan->nama_lapangan }}<br>
                Tanggal: {{ \Carbon\Carbon::parse($pemesanan->tanggal_sewa)->format('d-m-Y') }}<br>
                Jam: {{ substr($pemesanan->jam_mulai, 0, 5) }}
                - {{ substr($pemesanan->jam_selesai, 0, 5) }}<br>
                Status: {{ ucfirst($pemesanan->status_booking) }}
            </p>

            <a href="{{ route('pemesanan.status', ['id_booking' => $pemesanan->id_booking]) }}"
               class="block bg-blue-600 hover:bg-blue-700 text-white
                      font-semibold text-xs rounded-lg py-3 px-4
                      text-center max-w-sm">
                Lihat status pemesanan
            </a>

        </section>

    </main>

</body>
</html>
