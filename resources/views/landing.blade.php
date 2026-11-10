<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badmin Go - Booking Lapangan Badminton</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

    <header class="navbar">
        <div class="logo">BADMIN <span>GO.</span></div>

        <nav>
            <!-- Diarahkan ke halaman login -->
            <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            <!-- Diarahkan ke halaman register -->
            <a href="{{ route('register') }}" class="btn-register">Daftar</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <p class="tagline">BOOK YOUR COURT. PLAY YOUR GAME.</p>
            <h1>Waktunya<br>Main Badminton!</h1>
            <p class="description">
                Temukan lapangan favoritmu, pilih jadwal yang tersedia, dan booking
                dalam beberapa langkah mudah.
            </p>
            <!-- Diarahkan ke halaman login saat ditekan -->
            <a href="{{ route('login') }}" class="btn-booking">Booking Sekarang →</a>
            <p class="features">CEPAT &nbsp; • &nbsp; MUDAH &nbsp; • &nbsp; TANPA RIBET</p>
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

    <section class="search-section">
        <div class="search-title">
            <h3>Cari Lapangan</h3>
            <p>Pilih lokasi dan jadwal bermainmu</p>
        </div>

        <div class="search-field">
            <small>LOKASI</small>
            <input type="text" placeholder="Pilih area / lokasi">
        </div>

        <div class="search-field">
            <small>TANGGAL</small>
            <input type="date">
        </div>

        <button class="btn-search">Cari Lapangan →</button>
    </section>

    <main>
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

        <section class="court-section" id="lapangan">
            <p class="section-tag">PILIH TEMPAT BERMAIN</p>
            <h2>Lapangan pilihan</h2>
            <p class="section-description">
                Temukan tempat yang cocok untuk sesi permainan berikutnya.
            </p>

            <div class="court-container">
                <div class="court-card">
                    <div class="court-image">COURT 01</div>
                    <h4>Lapangan Premium</h4>
                    <p>Indoor • Vinyl flooring</p>
                    <strong>Mulai Rp50.000 / jam</strong>
                </div>

                <div class="court-card">
                    <div class="court-image">COURT 02</div>
                    <h4>Lapangan Standard</h4>
                    <p>Indoor • Sport flooring</p>
                    <strong>Mulai Rp40.000 / jam</strong>
                </div>

                <div class="court-card">
                    <div class="court-image">COURT 03</div>
                    <h4>Lapangan Family</h4>
                    <p>Indoor • Area nyaman</p>
                    <strong>Mulai Rp45.000 / jam</strong>
                </div>
            </div>
        </section>
    </main>

</body>
</html>
