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
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 px-5 py-2 rounded-md">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="bg-blue-600 px-5 py-2 rounded-md">Akun</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-6">

        <h2 class="text-2xl font-bold mb-5">
            {{ $lapangan->nama_lapangan }} • {{ $lapangan->jenis_lapangan }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-7">

            <div class="bg-[#d6e4f1] rounded-xl min-h-[350px] overflow-hidden flex items-center justify-center">
                @if ($lapangan->fotoPath())
                    <img
                        src="{{ asset($lapangan->fotoPath()) }}"
                        alt="Foto {{ $lapangan->nama_lapangan }}"
                        class="w-full h-full min-h-[350px] object-cover"
                    >
                @else
                    <span class="text-slate-500">Foto lapangan belum tersedia</span>
                @endif
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6">

                <h3 class="font-bold text-lg mb-3">Informasi Lapangan</h3>

                <p class="text-sm text-slate-500 mb-8">
                    {{ $lapangan->fasilitas }}
                </p>

                <form action="{{ route('booking.form') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3">
                            Pilih jadwal tersedia
                        </label>

                        <select
                            name="id_jadwal"
                            required
                            class="w-full bg-slate-100 border rounded-lg px-4 py-3 text-sm"
                            @disabled($jadwal->isEmpty())
                        >
                            @forelse ($jadwal as $slot)
                                <option value="{{ $slot->id_jadwal }}"
                                    {{ old('id_jadwal') == $slot->id_jadwal ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($slot->tanggal)->format('d-m-Y') }}
                                    • {{ substr($slot->jam_mulai, 0, 5) }}–{{ substr($slot->jam_selesai, 0, 5) }}
                                </option>
                            @empty
                                <option value="">Belum ada jadwal tersedia</option>
                            @endforelse
                        </select>

                        <p class="text-xs text-slate-500 mt-2">
                            Jadwal tersedia dapat dipesan hingga 30 hari ke depan.
                        </p>
                    </div>

                    <!-- HARGA -->
                    <p class="text-xl font-bold mb-8">
                        Rp{{ number_format($lapangan->harga_per_jam, 0, ',', '.') }} / jam
                    </p>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg"
                        @disabled($jadwal->isEmpty())
                    >
                        Pesan Sekarang
                    </button>

                </form>
            </div>
        </div>
    </main>
</body>
</html>