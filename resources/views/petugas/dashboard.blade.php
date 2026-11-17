<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">
    <nav class="bg-[#07172b] text-white px-8 py-3 flex items-center justify-between">
        <h1 class="font-bold text-lg">BADMIN <span class="text-blue-500">GO</span></h1>
        <div class="flex gap-3 text-sm">
            <a href="{{ route('katalog') }}" class="bg-blue-600 px-4 py-2 rounded-md">Katalog</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-slate-700 px-4 py-2 rounded-md">Keluar</button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold">Verifikasi pembayaran</h2>
                <p class="text-sm text-slate-500">Kelola bukti pembayaran dari pelanggan.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Lapangan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Bukti</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembayaran as $item)
                        <tr class="border-t border-slate-200 align-top">
                            <td class="px-4 py-3 font-medium">{{ $item->nama_user }}</td>
                            <td class="px-4 py-3">{{ $item->nama_lapangan }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($item->tanggal_sewa)->format('d-m-Y') }}<br>
                                {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}
                            </td>
                            <td class="px-4 py-3">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $item->metode_pembayaran }}</td>
                            <td class="px-4 py-3">
                                @if ($item->bukti_pembayaran)
                                    <a href="{{ Storage::disk('public')->url($item->bukti_pembayaran) }}" target="_blank" class="text-blue-600 underline">
                                        Lihat bukti
                                    </a>
                                @else
                                    <span class="text-slate-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($item->status_pembayaran === 'berhasil')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">Berhasil</span>
                                @elseif ($item->status_pembayaran === 'ditolak')
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs">Ditolak</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-y-2">
                                @if ($item->status_pembayaran !== 'berhasil' && $item->status_pembayaran !== 'ditolak')
                                    <form action="{{ route('petugas.verifikasi', ['id_pembayaran' => $item->id_pembayaran, 'status' => 'berhasil']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-xs font-semibold">
                                            Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('petugas.verifikasi', ['id_pembayaran' => $item->id_pembayaran, 'status' => 'ditolak']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-xs font-semibold">
                                            Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-500 text-xs">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada pembayaran yang perlu diverifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
