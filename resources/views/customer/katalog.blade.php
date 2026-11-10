
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Lapangan - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#f3f6fb] text-[#14243b]">

    <!-- NAVBAR -->
    <nav class="bg-[#07172b] text-white px-6 md:px-10 py-4 flex justify-between items-center sticky top-0 z-50">

        <a href="{{ url('/landing') }}"
           class="text-xl font-bold tracking-wide">
            BADMIN <span class="text-blue-500">GO.</span>
        </a>

        <div class="flex items-center gap-2 md:gap-3 text-xs font-semibold">

            <a href="{{ url('/landing') }}"
               class="bg-blue-600 hover:bg-blue-700 px-4 md:px-5 py-3 rounded-lg transition">
                Beranda
            </a>

            <a href="{{ route('booking.riwayat') }}"
               class="bg-blue-600 hover:bg-blue-700 px-4 md:px-5 py-3 rounded-lg transition">
                Riwayat
            </a>

            <a href="{{ route('login') }}"
               class="bg-blue-600 hover:bg-blue-700 px-4 md:px-5 py-3 rounded-lg transition">
                Akun
            </a>

        </div>
    </nav>


    <!-- KONTEN UTAMA -->
    <main class="max-w-6xl mx-auto px-5 md:px-8 py-8">

        <!-- JUDUL -->
        <div class="mb-6">

            <h1 class="text-2xl md:text-3xl font-bold mb-5">
                Temukan lapangan favoritmu
            </h1>

            <!-- PENCARIAN -->
            <div class="bg-white border border-slate-200 rounded-xl p-2 flex flex-col sm:flex-row gap-2">

                <input
                    type="text"
                    id="searchLapangan"
                    placeholder="Cari lapangan, lokasi, atau fasilitas"
                    class="flex-1 px-3 py-3 text-sm outline-none text-slate-700"
                >

                <button
                    type="button"
                    id="btnSearch"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-6 py-3 rounded-lg transition">
                    Cari Lapangan
                </button>

            </div>

        </div>


        <!-- DAFTAR LAPANGAN -->
        <div id="daftarLapangan"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            @forelse($lapangan as $item)

                <div
                    class="lapangan-card bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col shadow-sm hover:shadow-md transition"
                    data-search="{{ strtolower($item->nama_lapangan . ' ' . $item->jenis_lapangan . ' ' . $item->fasilitas . ' ' . ($item->lokasi ?? '')) }}"
                >

                    <!-- AREA FOTO -->
                    <div class="h-44 bg-[#d5e3f0] flex items-center justify-center">

                        <div class="text-center text-[#31516e]">

                            <div class="text-3xl mb-2">🏸</div>

                            <p class="text-xs font-bold uppercase tracking-wider">
                                {{ $item->nama_lapangan }}
                            </p>

                        </div>

                    </div>


                    <!-- INFORMASI LAPANGAN -->
                    <div class="p-4 flex-1">

                        <h2 class="font-bold text-base mb-2">
                            {{ $item->nama_lapangan }}
                        </h2>

                        <p class="text-xs text-slate-500 mb-3">
                            {{ $item->jenis_lapangan }}
                            • Rp{{ number_format($item->harga_per_jam, 0, ',', '.') }}/jam
                        </p>

                        <p class="text-xs text-slate-500 leading-relaxed">
                            {{ $item->fasilitas ?: 'Informasi fasilitas belum tersedia' }}
                        </p>

                    </div>


                    <!-- TOMBOL DETAIL -->
                    <div class="px-4 pb-4">

                        <a href="{{ route('booking.detail', $item->id_lapangan) }}"
                           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-3 rounded-lg transition">

                            Lihat detail

                        </a>

                    </div>

                </div>

            @empty

                <div class="col-span-full bg-white border border-slate-200 rounded-xl p-10 text-center">

                    <div class="text-4xl mb-3">🏸</div>

                    <h3 class="font-bold text-lg mb-2">
                        Belum ada lapangan
                    </h3>

                    <p class="text-sm text-slate-500">
                        Saat ini belum ada data lapangan yang tersedia.
                    </p>

                </div>

            @endforelse

        </div>


        <!-- PESAN JIKA PENCARIAN TIDAK DITEMUKAN -->
        <div id="notFound"
             class="hidden bg-white rounded-xl p-10 text-center mt-5">

            <div class="text-4xl mb-3">🔎</div>

            <h3 class="font-bold text-lg mb-2">
                Lapangan tidak ditemukan
            </h3>

            <p class="text-sm text-slate-500">
                Coba gunakan kata kunci pencarian yang lain.
            </p>

        </div>

    </main>


    <!-- JAVASCRIPT PENCARIAN -->
    <script>

        const searchInput = document.getElementById('searchLapangan');
        const searchButton = document.getElementById('btnSearch');
        const cards = document.querySelectorAll('.lapangan-card');
        const notFound = document.getElementById('notFound');

        function cariLapangan() {

            const keyword = searchInput.value.toLowerCase().trim();

            let jumlahDitemukan = 0;

            cards.forEach(card => {

                const informasi = card.dataset.search;

                if (informasi.includes(keyword)) {

                    card.classList.remove('hidden');
                    jumlahDitemukan++;

                } else {

                    card.classList.add('hidden');

                }

            });

            if (jumlahDitemukan === 0 && cards.length > 0) {

                notFound.classList.remove('hidden');

            } else {

                notFound.classList.add('hidden');

            }

        }

        searchButton.addEventListener('click', cariLapangan);

        searchInput.addEventListener('input', cariLapangan);

        searchInput.addEventListener('keydown', function(event) {

            if (event.key === 'Enter') {
                cariLapangan();
            }

        });

    </script>

</body>
</html>
