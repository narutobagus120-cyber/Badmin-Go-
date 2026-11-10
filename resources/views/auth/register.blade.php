
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#07172b] text-white">

    <div class="min-h-screen flex flex-col">

        <!-- NAVBAR -->
        <header class="flex justify-between items-center px-6 md:px-12 py-4">
            <a href="{{ url('/landing') }}"
               class="font-bold text-xl tracking-wide">
                BADMIN <span class="text-blue-500">GO.</span>
            </a>

            <a href="{{ url('/landing') }}"
               class="bg-blue-600 hover:bg-blue-700 px-6 py-2.5 rounded-lg text-xs font-semibold transition">
                Akun
            </a>
        </header>

        <!-- REGISTER CONTENT -->
        <main class="flex-1 flex items-center justify-center px-5 py-8">

            <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10">

                <!-- PANEL KIRI -->
                <section class="bg-[#102a49] rounded-2xl p-8 md:p-9 min-h-[420px] md:min-h-[455px] flex flex-col">

                    <h1 class="text-3xl font-bold mb-3">
                        BADMIN GO
                    </h1>

                    <p class="text-slate-300 text-sm leading-relaxed">
                        Booking lapangan badminton jadi lebih mudah.
                    </p>

                    <p class="text-white text-xs mt-14">
                        Pilih lapangan. Atur jadwal. Siap bermain.
                    </p>

                </section>

                <!-- PANEL FORM REGISTER -->
                <section class="bg-white text-[#14243b] rounded-2xl p-7 md:p-10 min-h-[455px]">

                    <h2 class="text-2xl font-bold mb-2">
                        Buat akun baru
                    </h2>

                    <p class="text-slate-500 text-xs mb-7">
                        Selamat datang di BADMIN GO
                    </p>

                    <!-- PESAN ERROR -->
                    @if($errors->any())
                        <div class="bg-red-50 text-red-600 border border-red-200 rounded-lg p-3 text-xs mb-5">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- FORM REGISTER -->
                    <form action="{{ route('register') }}" method="POST" class="space-y-4">

                        @csrf

                        <!-- NAMA LENGKAP -->
                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Nama lengkap
                            </label>

                            <input
                                type="text"
                                name="nama_user"
                                value="{{ old('nama_user') }}"
                                required
                                maxlength="100"
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap"
                                class="w-full px-3 py-3 rounded-lg bg-[#f3f6fa] border border-slate-200 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                            >
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Email aktif
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                maxlength="100"
                                autocomplete="email"
                                placeholder="Masukkan email aktif"
                                class="w-full px-3 py-3 rounded-lg bg-[#f3f6fa] border border-slate-200 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                            >
                        </div>

                        <!-- NOMOR TELEPON -->
                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Nomor telepon
                            </label>

                            <input
                                type="tel"
                                name="no_telepon"
                                value="{{ old('no_telepon') }}"
                                required
                                maxlength="15"
                                autocomplete="tel"
                                placeholder="Masukkan nomor telepon"
                                class="w-full px-3 py-3 rounded-lg bg-[#f3f6fa] border border-slate-200 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                            >
                        </div>

                        <!-- PASSWORD -->
                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Kata sandi
                            </label>

                            <input
                                type="password"
                                name="password"
                                required
                                minlength="6"
                                autocomplete="new-password"
                                placeholder="Masukkan kata sandi"
                                class="w-full px-3 py-3 rounded-lg bg-[#f3f6fa] border border-slate-200 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                            >
                        </div>

                        <!-- TOMBOL DAFTAR -->
                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-3 rounded-lg transition duration-200 mt-3">
                            Daftar Akun
                        </button>

                    </form>

                    <!-- LINK LOGIN -->
                    <p class="text-xs text-slate-500 mt-4">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                           class="text-blue-600 font-semibold hover:underline">
                            Masuk sekarang
                        </a>
                    </p>

                </section>

            </div>
        </main>

        <!-- FOOTER -->
        <footer class="text-center text-slate-500 text-xs py-4">
            © {{ date('Y') }} BADMIN GO. All Rights Reserved.
        </footer>

    </div>

</body>
</html>
