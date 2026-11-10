<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SI Persediaan dan Transaksi KDMP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen flex items-center justify-center bg-gradient-to-b from-red-50 via-white to-stone-100 dark:from-stone-950 dark:via-stone-900 dark:to-stone-950 text-stone-900 dark:text-stone-100 font-sans px-4">
    <script>
        if (localStorage.getItem('kdmp-tema') === 'gelap') {
            document.body.classList.add('dark');
        }
    </script>

    <button type="button" id="tombol-tema" onclick="gantiTema()" aria-label="Ganti tema"
        class="fixed top-4 right-4 z-10 w-10 h-10 rounded-full bg-white dark:bg-stone-900 border border-stone-300 dark:border-stone-700 text-stone-700 dark:text-stone-300 shadow-sm hover:border-red-700 dark:hover:border-red-400 hover:text-red-700 dark:hover:text-red-400 transition motion-reduce:transition-none flex items-center justify-center">
        <span id="ikon-bulan"><x-ikon nama="moon" class="w-5 h-5" /></span>
        <span id="ikon-matahari" class="hidden"><x-ikon nama="sun" class="w-5 h-5" /></span>
    </button>

    <div class="w-full max-w-[400px]">
        {{-- overflow-hidden agar garis merah di atas ikut membulat mengikuti sudut card --}}
        <div
            class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-2xl shadow-xl overflow-hidden">

            {{-- Garis merah di bagian atas card --}}
            <div class="h-1.5 bg-red-700 dark:bg-red-500"></div>

            <div class="p-8">
                <img src="{{ asset('images/logo-kopdes.jpeg') }}" alt="Logo Koperasi Desa Merah Putih"
                    class="w-24 h-24 mx-auto mb-4 rounded-full bg-white object-contain p-2 shadow-sm">
                <h1 class="text-xl font-bold text-red-700 dark:text-red-400 text-center mb-2">SI Persediaan dan
                    Transaksi
                    Koperasi Desa Merah Putih</h1>
                <p class="text-center text-stone-500 dark:text-stone-400 text-sm mb-6">Silakan masuk dengan akun Anda
                </p>

                @if ($errors->has('login'))
                    <div
                        class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
                        {{ $errors->first('login') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="form-login">
                    @csrf
                    <label for="username" class="block text-sm font-bold mb-1">Username</label>
                    <div class="relative mb-4">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 dark:text-stone-500 pointer-events-none"><x-ikon
                                nama="user" class="w-5 h-5" /></span>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" autofocus
                            autocomplete="username" placeholder="Masukkan username"
                            @error('username') aria-invalid="true" @enderror
                            class="w-full pl-10 pr-3 py-2.5 border {{ $errors->has('username') ? 'border-red-600' : 'border-stone-300 dark:border-stone-700' }} rounded-lg bg-white dark:bg-stone-800 text-stone-900 dark:text-stone-100 text-[15px] focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition motion-reduce:transition-none">
                    </div>
                    @error('username')
                        <div class="text-red-600 text-[13px] -mt-3 mb-3">{{ $message }}</div>
                    @enderror

                    <label for="password" class="block text-sm font-bold mb-1">Password</label>
                    <div class="relative mb-4">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 dark:text-stone-500 pointer-events-none"><x-ikon
                                nama="lock-closed" class="w-5 h-5" /></span>
                        <input id="password" type="password" name="password" autocomplete="current-password"
                            placeholder="*******" @error('password') aria-invalid="true" @enderror
                            class="w-full pl-10 pr-10 py-2.5 border {{ $errors->has('password') ? 'border-red-600' : 'border-stone-300 dark:border-stone-700' }} rounded-lg bg-white dark:bg-stone-800 text-stone-900 dark:text-stone-100 text-[15px] focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition motion-reduce:transition-none">
                        <button type="button" id="tombol-lihat-password" onclick="gantiLihatPassword()"
                            aria-label="Lihat password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 dark:text-stone-500 hover:text-red-700 dark:hover:text-red-400 transition motion-reduce:transition-none">
                            <span id="ikon-mata-buka"><x-ikon nama="eye" class="w-5 h-5" /></span>
                            <span id="ikon-mata-tutup" class="hidden"><x-ikon nama="eye-slash" class="w-5 h-5" /></span>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-red-600 text-[13px] -mt-3 mb-3">{{ $message }}</div>
                    @enderror

                    <button type="submit" id="tombol-masuk"
                        class="w-full py-3 bg-red-700 hover:bg-red-800 hover:shadow-md text-white font-bold rounded-lg text-base transition motion-reduce:transition-none disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">Masuk</button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-stone-500 dark:text-stone-400 mt-6">© {{ date('Y') }} Koperasi Desa Merah
            Putih Pulo Lor<br>Sistem Informasi Persediaan dan Transaksi</p>
    </div>

    <script>
        function perbaruiTombolTema() {
            var gelap = document.body.classList.contains('dark');
            var bulan = document.getElementById('ikon-bulan');
            var matahari = document.getElementById('ikon-matahari');
            if (bulan) bulan.classList.toggle('hidden', gelap);
            if (matahari) matahari.classList.toggle('hidden', !gelap);
        }

        function gantiTema() {
            var gelap = document.body.classList.toggle('dark');
            localStorage.setItem('kdmp-tema', gelap ? 'gelap' : 'terang');
            perbaruiTombolTema();
        }

        function gantiLihatPassword() {
            var input = document.getElementById('password');
            var tombol = document.getElementById('tombol-lihat-password');
            var buka = document.getElementById('ikon-mata-buka');
            var tutup = document.getElementById('ikon-mata-tutup');
            if (!input) return;
            var lihat = input.type === 'password';
            input.type = lihat ? 'text' : 'password';
            if (buka) buka.classList.toggle('hidden', lihat);
            if (tutup) tutup.classList.toggle('hidden', !lihat);
            if (tombol) tombol.setAttribute('aria-label', lihat ? 'Sembunyikan password' : 'Lihat password');
        }

        document.addEventListener('DOMContentLoaded', function() {
            perbaruiTombolTema();
        });

        document.getElementById('form-login').addEventListener('submit', function() {
            var tombol = document.getElementById('tombol-masuk');
            if (tombol) {
                tombol.disabled = true;
                tombol.innerHTML =
                    '<svg class="w-5 h-5 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>' +
                    '</svg><span>Memproses...</span>';
            }
        });
    </script>
</body>

</html>
