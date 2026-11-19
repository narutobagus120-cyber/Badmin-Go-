<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f3f6fb] text-[#14243b]">
    <div class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-10 hidden w-56 flex-col bg-[#07172b] px-3 py-5 text-white md:flex">
            <a href="#dashboard" class="px-6 text-lg font-bold">BADMIN <span class="text-blue-500">GO</span></a>
            <p class="mb-4 mt-12 px-5 text-[10px] font-bold uppercase tracking-wide text-blue-200">Menu Admin</p>
            <nav class="space-y-1 text-xs">
                <a href="#dashboard" class="block rounded-lg bg-blue-600 px-4 py-3 font-semibold">Dashboard</a>
                <a href="#manajemen-lapangan" class="block rounded-lg px-4 py-3 hover:bg-slate-800">Manajemen Lapangan</a>
                <a href="#data-pelanggan" class="block rounded-lg px-4 py-3 hover:bg-slate-800">Data Pelanggan</a>
                <a href="#manajemen-booking" class="block rounded-lg px-4 py-3 hover:bg-slate-800">Manajemen Booking</a>
                <a href="#laporan-transaksi" class="block rounded-lg px-4 py-3 hover:bg-slate-800">Laporan Transaksi</a>
                <a href="#manajemen-jadwal" class="block rounded-lg px-4 py-3 hover:bg-slate-800">Jadwal Tersedia</a>
            </nav>
            <form action="{{ route('logout') }}" method="POST" class="mt-auto px-2">
                @csrf
                <button type="submit" class="w-full rounded-lg px-4 py-3 text-left text-xs hover:bg-slate-800">Keluar</button>
            </form>
        </aside>

        <main class="space-y-12 px-5 py-6 md:ml-56 md:px-8 md:py-8">
            <section id="dashboard" class="scroll-mt-6">
                <h1 class="mb-6 text-2xl font-bold">Ringkasan operasional hari ini</h1>

                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-xl border border-slate-200 bg-white p-5">
                        <p class="text-xs text-slate-500">Total booking</p>
                        <p class="mt-2 text-2xl font-bold">{{ $jumlahBooking }}</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-white p-5">
                        <p class="text-xs text-slate-500">Lapangan aktif</p>
                        <p class="mt-2 text-2xl font-bold">{{ $jumlahLapanganAktif }}</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-white p-5">
                        <p class="text-xs text-slate-500">Pelanggan terdaftar</p>
                        <p class="mt-2 text-2xl font-bold">{{ $jumlahPelanggan }}</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-white p-5">
                        <p class="text-xs text-slate-500">Pendapatan hari ini</p>
                        <p class="mt-2 text-2xl font-bold">Rp{{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                    </article>
                </div>

                <div class="mt-6 grid gap-5 xl:grid-cols-[2fr_1fr]">
                    <article class="rounded-xl border border-slate-200 bg-white p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="font-bold">Booking terbaru</h2>
                            <a href="#manajemen-booking" class="text-xs font-semibold text-blue-600">Lihat semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[600px] text-left text-xs">
                                <thead class="text-slate-500">
                                    <tr>
                                        <th class="py-3">Kode booking</th>
                                        <th class="py-3">Pelanggan</th>
                                        <th class="py-3">Lapangan</th>
                                        <th class="py-3">Jadwal</th>
                                        <th class="py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pemesananTerbaru as $item)
                                        <tr class="border-t border-slate-100">
                                            <td class="py-4">BG-{{ date('Y') }}-{{ str_pad($item->id_booking, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="py-4">{{ $item->nama_user }}</td>
                                            <td class="py-4">{{ $item->nama_lapangan }}</td>
                                            <td class="py-4">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m') }} • {{ substr($item->jam_mulai, 0, 5) }}</td>
                                            <td class="py-4">{{ ucfirst($item->status_booking) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Belum ada booking.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-white p-5">
                        <h2 class="mb-4 font-bold">Aktivitas terbaru</h2>
                        <ul class="space-y-3 text-xs text-slate-500">
                            <li>{{ $transaksi->where('status_pembayaran', 'menunggu')->count() }} pembayaran menunggu verifikasi</li>
                            <li>{{ $booking->where('status_booking', 'menunggu')->count() }} booking menunggu konfirmasi</li>
                            <li>{{ $jadwal->where('status_jadwal', 'tersedia')->count() }} slot jadwal tersedia</li>
                        </ul>
                    </article>
                </div>
            </section>

            <section id="manajemen-lapangan" class="scroll-mt-6">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-2xl font-bold">Kelola lapangan dan tarif</h2>
                    <a href="#tambah-lapangan" class="rounded-lg bg-blue-600 px-5 py-3 text-xs font-semibold text-white hover:bg-blue-700">Tambah lapangan</a>
                </div>

                <form id="tambah-lapangan" action="{{ route('admin.lapangan.store') }}" method="POST" class="mb-5 grid gap-3 rounded-xl border border-slate-200 bg-white p-5 md:grid-cols-6">
                    @csrf
                    <input name="nama_lapangan" required maxlength="50" placeholder="Nama lapangan" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input name="jenis_lapangan" required maxlength="50" placeholder="Tipe / jenis" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input name="fasilitas" maxlength="5000" placeholder="Spesifikasi / fasilitas" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input name="harga_per_jam" type="number" min="1" step="1000" required placeholder="Harga per jam" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <select name="status_lapangan" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <option value="tersedia">Aktif</option>
                        <option value="tidak tersedia">Nonaktif</option>
                    </select>
                    <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Simpan lapangan</button>
                </form>

                <div class="mb-4 rounded-xl border border-slate-200 bg-white p-3">
                    <input data-lapangan-search placeholder="Cari lapangan" class="w-full rounded-lg px-2 py-2 text-sm outline-none">
                </div>
                <div class="space-y-4">
                    @forelse ($lapangan as $item)
                        <form data-search-row action="{{ route('admin.lapangan.update', $item->id_lapangan) }}" method="POST" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-5 md:grid-cols-6">
                            @csrf
                            @method('PATCH')
                            <label class="text-xs text-slate-500">Nama
                                <input name="nama_lapangan" value="{{ $item->nama_lapangan }}" required maxlength="50" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900">
                            </label>
                            <label class="text-xs text-slate-500">Tipe
                                <input name="jenis_lapangan" value="{{ $item->jenis_lapangan }}" required maxlength="50" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900">
                            </label>
                            <label class="text-xs text-slate-500 md:col-span-2">Spesifikasi / fasilitas
                                <input name="fasilitas" value="{{ $item->fasilitas }}" maxlength="5000" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900">
                            </label>
                            <label class="text-xs text-slate-500">Harga / jam
                                <input name="harga_per_jam" type="number" min="1" step="1000" value="{{ $item->harga_per_jam }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900">
                            </label>
                            <div class="flex items-end gap-2">
                                <select name="status_lapangan" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-2 py-2 text-sm">
                                    <option value="tersedia" @selected($item->status_lapangan === 'tersedia')>Aktif</option>
                                    <option value="tidak tersedia" @selected($item->status_lapangan === 'tidak tersedia')>Nonaktif</option>
                                </select>
                                <button class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white">Simpan</button>
                            </div>
                        </form>
                    @empty
                        <div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">Belum ada lapangan.</div>
                    @endforelse
                </div>
            </section>

            <section id="manajemen-jadwal" class="scroll-mt-6">
                <h2 class="mb-5 text-2xl font-bold">Kelola jadwal tersedia</h2>
                <form action="{{ route('admin.jadwal.store') }}" method="POST" class="mb-5 grid gap-3 rounded-xl border border-slate-200 bg-white p-5 md:grid-cols-5">
                    @csrf
                    <select name="id_lapangan" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Pilih lapangan</option>
                        @foreach ($lapangan->where('status_lapangan', 'tersedia') as $court)
                            <option value="{{ $court->id_lapangan }}">{{ $court->nama_lapangan }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="tanggal" min="{{ now()->toDateString() }}" max="{{ now()->addDays(30)->toDateString() }}" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input type="time" name="jam_mulai" min="08:00" max="21:00" step="3600" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <input type="time" name="jam_selesai" min="09:00" max="22:00" step="3600" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Tambah jadwal</button>
                </form>

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white p-5">
                    <table class="w-full min-w-[850px] text-left text-xs">
                        <thead class="text-slate-500">
                            <tr>
                                <th class="pb-3">Lapangan</th>
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Jam</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwal as $slot)
                                <tr class="border-t border-slate-100">
                                    @if ($slot->status_jadwal === 'tersedia')
                                        <td colspan="5" class="py-3">
                                            <form action="{{ route('admin.jadwal.update', $slot->id_jadwal) }}" method="POST" class="grid items-center gap-2 md:grid-cols-[1.2fr_1fr_1fr_1fr_auto_auto]">
                                                @csrf
                                                @method('PATCH')
                                                <select name="id_lapangan" required class="rounded border border-slate-200 px-2 py-2">
                                                    @foreach ($lapangan->where('status_lapangan', 'tersedia') as $court)
                                                        <option value="{{ $court->id_lapangan }}" @selected($court->id_lapangan === $slot->id_lapangan)>{{ $court->nama_lapangan }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="date" name="tanggal" value="{{ \Carbon\Carbon::parse($slot->tanggal)->format('Y-m-d') }}" min="{{ now()->toDateString() }}" max="{{ now()->addDays(30)->toDateString() }}" required class="rounded border border-slate-200 px-2 py-2">
                                                <input type="time" name="jam_mulai" value="{{ substr($slot->jam_mulai, 0, 5) }}" min="08:00" max="21:00" step="3600" required class="rounded border border-slate-200 px-2 py-2">
                                                <input type="time" name="jam_selesai" value="{{ substr($slot->jam_selesai, 0, 5) }}" min="09:00" max="22:00" step="3600" required class="rounded border border-slate-200 px-2 py-2">
                                                <button class="rounded bg-blue-600 px-3 py-2 font-semibold text-white">Simpan</button>
                                            </form>
                                            <form action="{{ route('admin.jadwal.delete', $slot->id_jadwal) }}" method="POST" class="mt-2 text-right" onsubmit="return confirm('Hapus slot jadwal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded bg-red-50 px-3 py-2 font-semibold text-red-700">Hapus slot</button>
                                            </form>
                                        </td>
                                    @else
                                        <td class="py-4">{{ $slot->nama_lapangan }}</td>
                                        <td class="py-4">{{ \Carbon\Carbon::parse($slot->tanggal)->format('d-m-Y') }}</td>
                                        <td class="py-4">{{ substr($slot->jam_mulai, 0, 5) }}–{{ substr($slot->jam_selesai, 0, 5) }}</td>
                                        <td class="py-4">Dipesan</td>
                                        <td class="py-4 text-slate-500">Jadwal booking terkunci</td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-center text-slate-500">Belum ada jadwal dalam 30 hari ke depan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="data-pelanggan" class="scroll-mt-6">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-2xl font-bold">Daftar pelanggan terdaftar</h2>
                    <div class="flex gap-2 rounded-xl border border-slate-200 bg-white p-2">
                        <input data-table-search="pelangganTable" placeholder="Cari pelanggan" class="min-w-40 px-2 text-sm outline-none">
                        <a href="{{ route('admin.export', 'pelanggan') }}" class="rounded-lg bg-blue-600 px-5 py-3 text-xs font-semibold text-white">Export data</a>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white p-5">
                    <table id="pelangganTable" class="w-full min-w-[700px] text-left text-xs">
                        <thead class="text-slate-500">
                            <tr><th class="pb-3">Nama pelanggan</th><th class="pb-3">Email</th><th class="pb-3">Telepon</th><th class="pb-3">Booking</th><th class="pb-3">Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($pelanggan as $item)
                                <tr data-search-row class="border-t border-slate-100">
                                    <td class="py-4">{{ $item->nama_user }}</td><td class="py-4">{{ $item->email }}</td><td class="py-4">{{ $item->no_telepon }}</td><td class="py-4">{{ $item->jumlah_booking }}</td><td class="py-4">Aktif</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-center text-slate-500">Belum ada pelanggan terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="manajemen-booking" class="scroll-mt-6">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-2xl font-bold">Kelola semua pemesanan</h2>
                    <div class="flex gap-2 rounded-xl border border-slate-200 bg-white p-2">
                        <input data-table-search="bookingTable" placeholder="Cari booking" class="min-w-40 px-2 text-sm outline-none">
                        <a href="{{ route('admin.export', 'booking') }}" class="rounded-lg bg-blue-600 px-5 py-3 text-xs font-semibold text-white">Export data</a>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white p-5">
                    <table id="bookingTable" class="w-full min-w-[900px] text-left text-xs">
                        <thead class="text-slate-500">
                            <tr><th class="pb-3">Kode booking</th><th class="pb-3">Pelanggan</th><th class="pb-3">Lapangan</th><th class="pb-3">Jadwal</th><th class="pb-3">Total</th><th class="pb-3">Status</th><th class="pb-3">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($booking as $item)
                                <tr data-search-row class="border-t border-slate-100 align-top">
                                    <td class="py-4">BG-{{ date('Y') }}-{{ str_pad($item->id_booking, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-4">{{ $item->nama_user }}</td>
                                    <td class="py-4">{{ $item->nama_lapangan }}</td>
                                    <td class="py-4">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}<br>{{ substr($item->jam_mulai, 0, 5) }}–{{ substr($item->jam_selesai, 0, 5) }}</td>
                                    <td class="py-4">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td class="py-4">{{ ucfirst($item->status_booking) }}</td>
                                    <td class="space-y-2 py-3">
                                        @if ($item->id_pembayaran && ! in_array($item->status_pembayaran, ['berhasil', 'ditolak'], true))
                                            <form action="{{ route('admin.pembayaran.verifikasi', ['id_pembayaran' => $item->id_pembayaran, 'status' => 'berhasil']) }}" method="POST">
                                                @csrf
                                                <button class="rounded bg-green-600 px-3 py-2 font-semibold text-white">Terima pembayaran</button>
                                            </form>
                                            <form action="{{ route('admin.pembayaran.verifikasi', ['id_pembayaran' => $item->id_pembayaran, 'status' => 'ditolak']) }}" method="POST">
                                                @csrf
                                                <button class="rounded bg-red-600 px-3 py-2 font-semibold text-white">Tolak pembayaran</button>
                                            </form>
                                        @elseif ($item->id_pembayaran)
                                            <span class="text-slate-500">{{ ucfirst($item->status_pembayaran) }}</span>
                                        @else
                                            <span class="text-slate-500">Menunggu pembayaran</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-6 text-center text-slate-500">Belum ada booking.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="laporan-transaksi" class="scroll-mt-6 pb-8">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-2xl font-bold">Laporan transaksi dan pendapatan</h2>
                    <div class="flex gap-2 rounded-xl border border-slate-200 bg-white p-2">
                        <input data-table-search="transaksiTable" placeholder="Cari transaksi" class="min-w-40 px-2 text-sm outline-none">
                        <a href="{{ route('admin.export', 'transaksi') }}" class="rounded-lg bg-blue-600 px-5 py-3 text-xs font-semibold text-white">Unduh laporan</a>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="mb-4 font-bold">Ringkasan laporan</h3>
                    <table id="transaksiTable" class="w-full min-w-[800px] text-left text-xs">
                        <thead class="text-slate-500">
                            <tr><th class="pb-3">Tanggal</th><th class="pb-3">Kode transaksi</th><th class="pb-3">Pelanggan</th><th class="pb-3">Metode</th><th class="pb-3">Nominal</th><th class="pb-3">Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksi as $item)
                                <tr data-search-row class="border-t border-slate-100">
                                    <td class="py-4">{{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d M Y') }}</td>
                                    <td class="py-4">TRX-{{ str_pad($item->id_pembayaran, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-4">{{ $item->nama_user }}</td>
                                    <td class="py-4">{{ $item->metode_pembayaran }}</td>
                                    <td class="py-4">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td class="py-4">{{ ucfirst($item->status_pembayaran) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-slate-500">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script>
        document.querySelectorAll('[data-table-search]').forEach(function (input) {
            const table = document.getElementById(input.dataset.tableSearch);
            const rows = table ? table.querySelectorAll('[data-search-row]') : [];

            input.addEventListener('input', function () {
                const query = input.value.toLocaleLowerCase().trim();

                rows.forEach(function (row) {
                    row.classList.toggle('hidden', !row.textContent.toLocaleLowerCase().includes(query));
                });
            });
        });

        const lapanganSearch = document.querySelector('[data-lapangan-search]');
        if (lapanganSearch) {
            const rows = document.querySelectorAll('#manajemen-lapangan [data-search-row]');
            lapanganSearch.addEventListener('input', function () {
                const query = lapanganSearch.value.toLocaleLowerCase().trim();
                rows.forEach(function (row) {
                    row.classList.toggle('hidden', !row.textContent.toLocaleLowerCase().includes(query));
                });
            });
        }
    </script>
</body>
</html>
