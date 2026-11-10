```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lapangan - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen text-slate-900">

    <nav class="bg-[#07172c] text-white px-8 py-3 flex justify-between items-center">
        <h1 class="font-bold text-lg">
            BADMIN <span class="text-blue-500">GO</span>
        </h1>

        <div class="flex gap-3 text-sm">
            <a href="{{ route('katalog') }}" class="bg-blue-600 px-5 py-2 rounded-md">Beranda</a>
            <a href="{{ route('booking.riwayat') }}" class="bg-blue-600 px-5 py-2 rounded-md">Riwayat</a>
            <a href="#" class="bg-blue-600 px-5 py-2 rounded-md">Akun</a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-6">

        <h2 class="text-2xl font-bold mb-5">
            {{ $lapangan->nama_lapangan }} • {{ $lapangan->jenis_lapangan }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-7">

            <div class="bg-[#d6e4f1] rounded-xl min-h-[350px] flex items-center justify-center">
                <span class="text-slate-500">Foto Lapangan</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6">

                <h3 class="font-bold text-lg mb-3">Informasi Lapangan</h3>

                <p class="text-sm text-slate-500 mb-8">
                    {{ $lapangan->fasilitas }}
                </p>

                <form action="{{ route('booking.form') }}" method="POST">
                    @csrf

                    <input type="hidden" name="id_lapangan"
                           value="{{ $lapangan->id_lapangan }}">

                    <!-- PILIH TANGGAL -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3">
                            Pilih tanggal bermain
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            min="{{ now()->format('Y-m-d') }}"
                            max="{{ now()->addDays(30)->format('Y-m-d') }}"
                            value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                            required
                            class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm"
                        >

                        <p class="text-xs text-slate-500 mt-2">
                            Pemesanan tersedia hingga 30 hari ke depan.
                        </p>
                    </div>

                    <!-- PILIH JAM -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3">
                            Pilih jam mulai
                        </label>

                        <select
                            name="jam_mulai"
                            required
                            class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm"
                        >
                            <option value="">-- Pilih jam mulai --</option>

                            @for ($jam = 8; $jam <= 21; $jam++)
                                <option
                                    value="{{ sprintf('%02d:00', $jam) }}"
                                    {{ old('jam_mulai') == sprintf('%02d:00', $jam) ? 'selected' : '' }}
                                >
                                    {{ sprintf('%02d:00', $jam) }}
                                </option>
                            @endfor
                        </select>

                        <p class="text-xs text-slate-500 mt-2">
                            Jam operasional lapangan: 08.00–22.00.
                        </p>
                    </div>

                    <!-- PILIH DURASI -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3">
                            Durasi bermain
                        </label>

                        <select
                            name="durasi"
                            required
                            class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm"
                        >
                            <option value="">-- Pilih durasi --</option>

                            @for ($i = 1; $i <= 14; $i++)
                                <option value="{{ $i }}"
                                    {{ old('durasi', 1) == $i ? 'selected' : '' }}>
                                    {{ $i }} jam
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- HARGA -->
                    <p class="text-xl font-bold mb-8">
                        Rp{{ number_format($lapangan->harga_per_jam, 0, ',', '.') }} / jam
                    </p>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg"
                    >
                        Pesan Sekarang
                    </button>

                </form>
            </div>
        </div>
    </main>
</body>
</html>
```