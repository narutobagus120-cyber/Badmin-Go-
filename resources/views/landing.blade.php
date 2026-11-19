
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badmin Go - Booking Lapangan Badminton</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">BADMIN <span>GO.</span></div>
        <nav>
            @auth
                @if (auth()->user()->role === 'petugas')
                    <a href="{{ route('admin.dashboard') }}" class="btn-login">Dashboard Admin</a>
                @else
                    <a href="{{ route('customer.dashboard') }}" class="btn-login">Dashboard User</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-register">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
                <a href="{{ route('register') }}" class="btn-register">Daftar</a>
            @endauth
        </nav>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <p class="tagline">BOOK YOUR COURT. PLAY YOUR GAME.</p>
            <h1>Waktunya<br>Main Badminton!</h1>

            <p class="description">
                Temukan lapangan favoritmu, pilih jadwal yang tersedia,
                dan booking dalam beberapa langkah mudah.
            </p>

            @auth
                @if (auth()->user()->role === 'petugas')
                    <a href="{{ route('admin.dashboard') }}" class="btn-booking">Dashboard Admin →</a>
                @else
                    <a href="{{ route('customer.dashboard') }}" class="btn-booking">Booking Sekarang →</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-booking">Booking Sekarang →</a>
            @endauth

            <p class="features">
                CEPAT &nbsp; • &nbsp; MUDAH &nbsp; • &nbsp; TANPA RIBET
            </p>
        </div>

        <div class="court-illustration">
            <div class="court">
                <div class="court-line"></div>
                <div class="court-middle"></div>
                <div class="court-net"></div>
            </div>
            <p>BADMINTON COURT</p>
        </div>
    </section>

    <!-- SEARCH -->
    <form action="{{ route('katalog') }}" method="GET" class="search-section">
        <div class="search-title">
            <h3>Cari Lapangan</h3>
            <p>Pilih lapangan favoritmu dan mulai bermain</p>
        </div>

        <button type="submit" class="btn-search">
            Cari Lapangan →
        </button>
    </form>

    <main>

        <!-- WHY SECTION -->
        <section class="why-section">
            <p class="section-tag">KENAPA BADMIN GO?</p>
            <h2>Booking lebih praktis,<br>main jadi lebih fokus.</h2>

            <div class="benefit-container">
                <div class="benefit-card">
                    <span>01</span>
                    <h4>Jadwal transparan</h4>
                    <p>Lihat slot waktu yang tersedia sebelum memesan.</p>
                </div>

                <div class="benefit-card">
                    <span>02</span>
                    <h4>Proses mudah</h4>
                    <p>Pilih lapangan dan jam bermain dalam satu alur.</p>
                </div>

                <div class="benefit-card">
                    <span>03</span>
                    <h4>Booking terorganisir</h4>
                    <p>Pantau status pemesanan dari akunmu.</p>
                </div>
            </div>
        </section>

        <!-- COURT SECTION -->
        <section class="court-section" id="lapangan">
            <p class="section-tag">PILIH TEMPAT BERMAIN</p>
            <h2>Lapangan pilihan</h2>

            <p class="section-description">
                Temukan tempat yang cocok untuk sesi permainan berikutnya.
            </p>

            <div class="court-container">

                <!-- PREMIUM -->
                <div class="court-card"
                     onclick="bukaDetail('premium')"
                     role="button"
                     tabindex="0"
                     onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); bukaDetail('premium'); }">

                    <div class="court-image">COURT 01</div>
                    <h4>Lapangan Premium</h4>
                    <p>Indoor • Vinyl flooring</p>
                    <strong>Mulai Rp50.000 / jam</strong>
                    <p class="detail-link">Lihat Detail →</p>
                </div>

                <!-- STANDARD -->
                <div class="court-card"
                     onclick="bukaDetail('standard')"
                     role="button"
                     tabindex="0"
                     onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); bukaDetail('standard'); }">

                    <div class="court-image">COURT 02</div>
                    <h4>Lapangan Standard</h4>
                    <p>Indoor • Sport flooring</p>
                    <strong>Mulai Rp40.000 / jam</strong>
                    <p class="detail-link">Lihat Detail →</p>
                </div>

                <!-- FAMILY -->
                <div class="court-card"
                     onclick="bukaDetail('family')"
                     role="button"
                     tabindex="0"
                     onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); bukaDetail('family'); }">

                    <div class="court-image">COURT 03</div>
                    <h4>Lapangan Family</h4>
                    <p>Indoor • Area nyaman</p>
                    <strong>Mulai Rp45.000 / jam</strong>
                    <p class="detail-link">Lihat Detail →</p>
                </div>

            </div>
        </section>
    </main>

    <!-- POPUP DETAIL LAPANGAN -->
    <div id="detailLapangan"
         class="modal-detail"
         onclick="klikAreaLuar(event)"
         aria-hidden="true">

        <div class="modal-content"
             role="dialog"
             aria-modal="true"
             aria-labelledby="namaLapangan">

            <button type="button"
                    class="close-modal"
                    onclick="tutupDetail()"
                    aria-label="Tutup popup">
                &times;
            </button>

            <img id="gambarLapangan"
                 src=""
                 alt="Foto Lapangan"
                 class="premium-image">

            <div class="modal-body">

                <span class="premium-label">BADMINTON COURT</span>

                <h2 id="namaLapangan"></h2>

                <h3 id="hargaLapangan"></h3>

                <p id="deskripsiLapangan"
                   class="premium-description"></p>

                <div class="detail-info">
                    <p>
                        <strong>Jenis lapangan</strong>
                        <span id="jenisLapangan"></span>
                    </p>

                    <p>
                        <strong>Jenis lantai</strong>
                        <span id="lantaiLapangan"></span>
                    </p>

                    <p>
                        <strong>Pencahayaan</strong>
                        <span id="pencahayaanLapangan"></span>
                    </p>

                    <p>
                        <strong>Area penonton</strong>
                        <span id="areaPenonton"></span>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        const modal = document.getElementById('detailLapangan');

        const dataLapangan = {
            premium: {
                nama: 'Lapangan Premium',
                harga: 'Rp50.000 / jam',
                gambar: "{{ asset('images/lapanganpremium.jpeg') }}",
                deskripsi: 'Lapangan badminton indoor dengan lantai vinyl, area permainan luas, dan pencahayaan yang nyaman. Cocok untuk latihan maupun bermain bersama teman.',
                jenis: 'Indoor',
                lantai: 'Vinyl flooring',
                pencahayaan: 'Lampu indoor',
                penonton: 'Tersedia'
            },

            standard: {
                nama: 'Lapangan Standard',
                harga: 'Rp40.000 / jam',
                gambar: "{{ asset('images/lapanganstandard.jpeg') }}",
                deskripsi: 'Lapangan badminton indoor dengan lantai sport flooring yang nyaman untuk bermain dan berlatih bersama teman.',
                jenis: 'Indoor',
                lantai: 'Sport flooring',
                pencahayaan: 'Lampu indoor',
                penonton: 'Tersedia'
            },

            family: {
                nama: 'Lapangan Family',
                harga: 'Rp45.000 / jam',
                gambar: "{{ asset('images/lapanganfamily.jpeg') }}",
                deskripsi: 'Lapangan badminton indoor dengan area yang nyaman untuk bermain bersama keluarga maupun teman.',
                jenis: 'Indoor',
                lantai: 'Area nyaman',
                pencahayaan: 'Lampu indoor',
                penonton: 'Tersedia'
            }
        };

        function bukaDetail(jenis) {
            const data = dataLapangan[jenis];

            document.getElementById('gambarLapangan').src = data.gambar;
            document.getElementById('namaLapangan').textContent = data.nama;
            document.getElementById('hargaLapangan').textContent = data.harga;
            document.getElementById('deskripsiLapangan').textContent = data.deskripsi;
            document.getElementById('jenisLapangan').textContent = data.jenis;
            document.getElementById('lantaiLapangan').textContent = data.lantai;
            document.getElementById('pencahayaanLapangan').textContent = data.pencahayaan;
            document.getElementById('areaPenonton').textContent = data.penonton;

            modal.classList.add('aktif');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function tutupDetail() {
            modal.classList.remove('aktif');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function klikAreaLuar(event) {
            if (event.target === modal) {
                tutupDetail();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.classList.contains('aktif')) {
                tutupDetail();
            }
        });
    </script>

</body>
</html>
