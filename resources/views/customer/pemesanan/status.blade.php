
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pemesanan - BADMIN GO</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f3f6fa;
            color: #0b1b30;
        }

        .navbar {
            min-height: 49px;
            background: #071629;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .brand {
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
        }

        .brand span {
            color: #1976f3;
            margin-left: 4px;
        }

        .nav-menu {
            display: flex;
            gap: 7px;
        }

        .nav-menu a {
            background: #1976f3;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: bold;
            transition: .3s;
        }

        .nav-menu a:hover {
            background: #075bc9;
        }

        .main {
            min-height: calc(100vh - 49px);
            padding: 22px 38px;
        }

        .page-title {
            font-size: 19px;
            font-weight: bold;
            margin-bottom: 33px;
        }

        .status-card {
            background: white;
            max-width: 560px;
            min-height: 298px;
            margin: 0 auto;
            border: 1px solid #e0e6ed;
            border-radius: 12px;
            text-align: center;
            padding: 29px 20px;
        }

        .success-icon {
            width: 84px;
            height: 84px;
            background: #e7f7ef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .success-icon svg {
            width: 27px;
            height: 27px;
            stroke: #119c65;
            stroke-width: 4;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .status-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 19px;
        }

        .booking-info {
            color: #8290a5;
            font-size: 10px;
            margin-bottom: 48px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 16px;
            border: 1px solid #e0e6ed;
            background: #f3f6fa;
            color: #0b1b30;
            border-radius: 7px;
            text-decoration: none;
            font-size: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: .3s;
        }

        .btn:hover {
            background: #e3eaf3;
        }

        .btn-primary {
            background: #1976f3;
            color: white;
            border-color: #1976f3;
        }

        .btn-primary:hover {
            background: #075bc9;
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 10px 15px;
                height: auto;
                flex-wrap: wrap;
                gap: 12px;
            }

            .nav-menu {
                gap: 5px;
            }

            .nav-menu a {
                padding: 8px 12px;
                font-size: 9px;
            }

            .main {
                padding: 25px 15px;
            }

            .page-title {
                font-size: 18px;
            }

            .status-card {
                min-height: 290px;
                padding: 30px 15px;
            }

            .button-group {
                gap: 10px;
            }

            .btn {
                font-size: 9px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="{{ url('/') }}" class="brand">
            BADMIN <span>GO</span>
        </a>

        <div class="nav-menu">
            <a href="{{ url('/') }}">Beranda</a>
            <a href="{{ route('booking.riwayat') }}">Riwayat</a>
            <a href="#">Akun</a>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="main">

        <h2 class="page-title">Status Pemesanan</h2>

        <div class="status-card">

            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M5 12.5L10 17L20 7"></path>
                </svg>
            </div>

            <!-- STATUS PEMBAYARAN -->
            <h3 class="status-title">
                @if ($pemesanan->status_pembayaran === 'diterima')
                    Pembayaran berhasil diverifikasi
                @elseif ($pemesanan->status_pembayaran === 'ditolak')
                    Pembayaran ditolak
                @elseif ($pemesanan->status_pembayaran)
                    Menunggu verifikasi pembayaran
                @else
                    Menunggu pembayaran
                @endif
            </h3>

            <!-- INFORMASI BOOKING -->
            <p class="booking-info">
                BG-{{ $pemesanan->id_booking }}
                • {{ $pemesanan->nama_lapangan }}
                • {{ \Carbon\Carbon::parse($pemesanan->tanggal)->format('d M Y') }}
            </p>

            <!-- BUTTONS -->
            <div class="button-group">

                <a href="{{ url('/landing') }}" class="btn">
                    Kembali ke Beranda
                </a>

                <a href="{{ route('pemesanan.bukti', ['id_booking' => $pemesanan->id_booking]) }}"
                   class="btn btn-primary">
                    Lihat Bukti Pemesanan
                </a>

            </div>

        </div>

    </main>

</body>
</html>
