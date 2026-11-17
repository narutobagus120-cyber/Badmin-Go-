<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen text-slate-900">

    <nav class="bg-[#07172c] text-white px-8 py-3 flex items-center justify-between">
        <h1 class="font-bold text-lg">
            BADMIN <span class="text-blue-500">GO</span>
        </h1>

        <div class="flex gap-3 text-sm">
            <a href="{{ route('katalog') }}" class="bg-blue-600 px-5 py-2 rounded-md">Beranda</a>
            <a href="{{ route('booking.riwayat') }}" class="bg-blue-600 px-5 py-2 rounded-md">Riwayat</a>
            <a href="{{ route('login') }}" class="bg-blue-600 px-5 py-2 rounded-md">Akun</a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-6">

        <h2 class="text-2xl font-bold mb-5">
            Lengkapi pemesananmu
        </h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-5">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- FORM PEMESANAN -->
            <div class="md:col-span-2 bg-white border border-slate-200 rounded-xl p-6">

                <form id="bookingForm"
                      action="{{ route('booking.simpan') }}"
                      method="POST">

                    @csrf

                    <!-- DATA UNTUK CONTROLLER -->
                    <input type="hidden"
                           name="id_lapangan"
                           value="{{ $lapangan->id_lapangan }}">

                    <input type="hidden"
                           name="tanggal"
                           value="{{ $tanggal }}">

                    <input type="hidden"
                           name="jam_mulai"
                           value="{{ $jam_mulai }}">

                    <div class="space-y-5">

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Nama pemesan
                            </label>
                            <input type="text"
                                   value="{{ $user->nama_user }}"
                                   readonly
                                   class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Nomor telepon
                            </label>
                            <input type="tel"
                                   value="{{ $user->no_telepon }}"
                                   readonly
                                   class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Lapangan
                            </label>
                            <input type="text"
                                   value="{{ $lapangan->nama_lapangan }}"
                                   readonly
                                   class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Tanggal booking
                            </label>
                            <input type="date"
                                   value="{{ $tanggal }}"
                                   readonly
                                   class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Jam mulai
                            </label>
                            <input type="text"
                                   id="jamMulai"
                                   value="{{ substr($jam_mulai, 0, 5) }}"
                                   readonly
                                   class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm">
                        </div>

                        <!-- PILIH DURASI -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Durasi booking
                            </label>

                            <select name="durasi"
                                    id="durasi"
                                    required
                                    class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm">

                                @php
                                    $jamMulai = (int) substr($jam_mulai, 0, 2);
                                    $maksDurasi = 22 - $jamMulai;
                                @endphp

                                @for ($i = 1; $i <= $maksDurasi; $i++)
                                    <option value="{{ $i }}"
                                        {{ old('durasi', $durasi ?? 1) == $i ? 'selected' : '' }}>
                                        {{ $i }} jam
                                    </option>
                                @endfor

                            </select>

                            <p class="text-xs text-slate-500 mt-2">
                                Durasi bermain dapat dipilih sesuai kebutuhan.
                            </p>
                        </div>

                        <!-- JAM SELESAI -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Jam selesai
                            </label>

                            <input type="text"
                                   id="jamSelesai"
                                   readonly
                                   class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm">
                        </div>

                    </div>
                </form>
            </div>

            <!-- RINGKASAN PESANAN -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 h-fit">

                <h3 class="font-bold text-lg mb-6">
                    Ringkasan pesanan
                </h3>

                <p class="text-sm mb-3">
                    {{ $lapangan->nama_lapangan }}
                </p>

                <div class="flex justify-between text-sm mb-3">
                    <span class="text-slate-500">Harga per jam</span>
                    <span>
                        Rp{{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm mb-3">
                    <span class="text-slate-500">Durasi</span>
                    <span id="durasiText">1 jam</span>
                </div>

                <div class="flex justify-between text-sm mb-5">
                    <span class="text-slate-500">Jam booking</span>
                    <span id="jamText"></span>
                </div>

                <hr class="mb-5">

                <p class="text-xs text-slate-500 mb-2">
                    Total pembayaran
                </p>

                <p class="text-2xl font-bold mb-8" id="totalHarga">
                    Rp{{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}
                </p>

                <button type="submit"
                        form="bookingForm"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg">
                    Lanjut ke pembayaran
                </button>

            </div>

        </div>
    </main>

    <script>
        const durasiSelect = document.getElementById('durasi');
        const jamMulaiInput = document.getElementById('jamMulai');
        const jamSelesaiInput = document.getElementById('jamSelesai');

        const durasiText = document.getElementById('durasiText');
        const jamText = document.getElementById('jamText');
        const totalHarga = document.getElementById('totalHarga');

        const hargaPerJam = {{ (float) $lapangan->harga_per_jam }};

        function updateRingkasan() {
            const durasi = parseInt(durasiSelect.value);
            const jamMulai = jamMulaiInput.value;

            const jam = parseInt(jamMulai.split(':')[0]);
            const menit = jamMulai.split(':')[1];

            const jamAkhir = jam + durasi;

            const jamSelesai =
                String(jamAkhir).padStart(2, '0') + ':' + menit;

            jamSelesaiInput.value = jamSelesai;

            durasiText.textContent = durasi + ' jam';

            jamText.textContent = jamMulai + ' - ' + jamSelesai;

            const total = hargaPerJam * durasi;

            totalHarga.textContent = 'Rp' +
                new Intl.NumberFormat('id-ID').format(total);
        }

        durasiSelect.addEventListener('change', updateRingkasan);

        updateRingkasan();
    </script>

</body>
</html>