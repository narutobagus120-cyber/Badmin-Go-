
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f3f6fb] min-h-screen text-[#0b1b30]">

    <!-- NAVBAR -->
    <nav class="bg-[#07172b] text-white px-5 md:px-10 py-3 flex items-center justify-between">
        <h1 class="font-bold text-lg">
            BADMIN <span class="text-blue-500">GO</span>
        </h1>

        <div class="flex gap-2 text-xs md:text-sm">
            <a href="{{ route('katalog') }}"
               class="bg-blue-600 hover:bg-blue-700 px-3 md:px-5 py-2 rounded-md">
                Beranda
            </a>

            <a href="{{ route('booking.riwayat') }}"
               class="bg-blue-600 hover:bg-blue-700 px-3 md:px-5 py-2 rounded-md">
                Riwayat
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 px-3 md:px-5 py-2 rounded-md">
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="px-4 py-8 md:py-10">

        <h1 class="text-xl md:text-2xl font-bold mb-6 text-center">
            Selesaikan pembayaran
        </h1>

        <!-- PAYMENT CARD -->
        <section class="bg-white border border-slate-200 rounded-xl
                        max-w-xl mx-auto p-5 md:p-7 shadow-sm">

            <h2 class="font-bold text-base mb-5">
                Instruksi pembayaran
            </h2>

            <!-- BOOKING CODE -->
            <p class="text-xs text-slate-500 mb-6">
                Kode booking: {{ $kodeBooking }}
            </p>

            <!-- TOTAL PAYMENT -->
            <p class="text-xs text-slate-500 mb-2">
                Total pembayaran
            </p>

            <h1 class="text-2xl font-bold mb-5">
                Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}
            </h1>

            <!-- PAYMENT INFORMATION -->
            <div class="bg-slate-100 rounded-lg p-4 mb-5">
                <p class="font-semibold text-sm">
                    Transfer bank / pembayaran manual
                </p>
            </div>

            <!-- BOOKING DETAILS -->
            <div class="bg-slate-50 border border-slate-100 rounded-lg p-4 mb-5">
                <p class="text-xs text-slate-600 leading-6">
                    <strong class="text-slate-800">Lapangan:</strong>
                    {{ $pemesanan->nama_lapangan }}<br>

                    <strong class="text-slate-800">Tanggal:</strong>
                    {{ \Carbon\Carbon::parse($pemesanan->tanggal_sewa)->format('d-m-Y') }}<br>

                    <strong class="text-slate-800">Jam:</strong>
                    {{ substr($pemesanan->jam_mulai, 0, 5) }}
                    - {{ substr($pemesanan->jam_selesai, 0, 5) }}<br>

                    <strong class="text-slate-800">Status:</strong>
                    {{ ucfirst($pemesanan->status_booking) }}
                </p>
            </div>

            <!-- PAYMENT FORM -->
            <form action="{{ route('pembayaran.upload', ['id_booking' => $pemesanan->id_booking]) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4">

                @csrf

                <!-- PAYMENT METHOD -->
                <div>
                    <label for="metode_pembayaran"
                           class="block text-xs font-semibold text-slate-700 mb-2">
                        Metode pembayaran
                    </label>

                    <select name="metode_pembayaran"
                            id="metode_pembayaran"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>

                        <option value="">Pilih metode</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>

                <!-- UPLOAD PAYMENT -->
                <div>
                    <label for="bukti_pembayaran"
                           class="block text-xs font-semibold text-slate-700 mb-2">
                        Upload bukti pembayaran (.jpg/.png)
                    </label>

                    <input type="file"
                           name="bukti_pembayaran"
                           id="bukti_pembayaran"
                           accept=".jpg,.jpeg,.png,image/png,image/jpeg"
                           class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                           required>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg py-3 px-4 transition">
                    Kirim bukti pembayaran
                </button>

            </form>

            <!-- BOOKING STATUS -->
            <a href="{{ route('pemesanan.status', ['id_booking' => $pemesanan->id_booking]) }}"
               class="block bg-slate-100 hover:bg-slate-200 text-slate-700
                      font-semibold text-sm rounded-lg py-3 px-4
                      text-center mt-3 transition">
                Lihat status pemesanan
            </a>

        </section>

    </main>

</body>
</html>
