
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - BADMIN GO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#07172b] text-white">

    <div class="min-h-screen flex flex-col">

        <!-- NAVBAR -->
        <header class="flex justify-between items-center px-6 md:px-12 py-5">
            <a href="{{ url('/landing') }}"
               class="font-bold text-xl tracking-wide">
                BADMIN <span class="text-blue-500">GO.</span>
            </a>

            <a href="{{ url('/landing') }}"
               class="bg-blue-600 hover:bg-blue-700 px-6 py-2.5 rounded-lg text-xs font-semibold transition">
                Kembali
            </a>
        </header>

        <!-- LOGIN CONTENT -->
        <main class="flex-1 flex items-center justify-center px-5 py-10">

            <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10">

                <!-- PANEL KIRI -->
                <section class="bg-[#102a49] rounded-2xl p-8 md:p-10 min-h-[400px] md:min-h-[470px] flex flex-col justify-between">

                    <div>
                        <h1 class="text-3xl font-bold mb-4">
                            BADMIN GO
                        </h1>

                        <p class="text-slate-300 text-sm leading-relaxed">
                            Booking lapangan badminton jadi lebih mudah.
                        </p>
                    </div>

                    <p class="text-slate-200 text-sm">
                        Pilih lapangan. Atur jadwal. Siap bermain.
                    </p>

                </section>

                <!-- PANEL LOGIN -->
                <section class="bg-[#102a49] border border-slate-700 rounded-2xl p-8 md:p-10 min-h-[470px] flex flex-col justify-center">

                    <h2 class="text-2xl font-bold mb-2">
                        Masuk ke akun BADMIN GO
                    </h2>

                    <p class="text-slate-400 text-xs mb-8">
                        Masuk sebagai pelanggan atau admin
                    </p>

                    <!-- PESAN SUKSES -->
                    @if(session('success'))
                        <div class="bg-green-900/40 text-green-300 border border-green-700 rounded-lg p-3 text-xs mb-5">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-amber-900/40 text-amber-300 border border-amber-700 rounded-lg p-3 text-xs mb-5">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- PESAN ERROR -->
                    @if($errors->any())
                        <div class="bg-red-900/40 text-red-300 border border-red-700 rounded-lg p-3 text-xs mb-5">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- FORM LOGIN -->
                    <form action="{{ route('login') }}" method="POST" class="space-y-6">

                        @csrf

                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="Masukkan email kamu"
                                class="w-full px-4 py-3 rounded-lg bg-[#07172b] border border-slate-600 text-white text-sm placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Kata sandi
                            </label>

                            <input
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                class="w-full px-4 py-3 rounded-lg bg-[#07172b] border border-slate-600 text-white text-sm placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3.5 rounded-lg transition duration-200">
                            Masuk
                        </button>

                    </form>

                    <p class="text-xs text-slate-400 mt-6">
                        Belum punya akun?
                        <a href="{{ route('register') }}"
                           class="text-blue-400 font-semibold hover:underline">
                            Daftar sekarang
                        </a>
                    </p>

                </section>

            </div>
        </main>

        <!-- FOOTER -->
        <footer class="text-center text-slate-500 text-xs py-5">
            © {{ date('Y') }} BADMIN GO. All Rights Reserved.
        </footer>

    </div>

</body>
</html>
