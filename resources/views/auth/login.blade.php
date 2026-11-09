<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SI Persediaan dan Transaksi KDMP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen flex items-center justify-center bg-stone-100 text-stone-900 dark:bg-stone-950 dark:text-stone-100 font-sans px-4">
    <script>
        if (localStorage.getItem('kdmp-tema') === 'gelap') {
            document.body.classList.add('dark');
        }
    </script>

    <div class="w-full max-w-[400px]">
        <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-2xl shadow-xl p-8">
            <h1 class="text-xl font-bold text-red-700 dark:text-red-400 text-center mb-1">SI Persediaan dan Transaksi
                Koperasi Desa Merah Putih</h1>
            <p class="text-center text-stone-500 dark:text-stone-400 text-sm mb-6">Silakan masuk dengan akun Anda</p>

            @if ($errors->has('login'))
                <div
                    class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ $errors->first('login') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label for="username" class="block text-sm font-bold mb-1">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" autofocus
                    class="w-full px-3 py-2.5 mb-4 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-stone-900 dark:text-stone-100 text-[15px] focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('username')
                    <div class="text-red-600 text-[13px] -mt-3 mb-3">{{ $message }}</div>
                @enderror

                <label for="password" class="block text-sm font-bold mb-1">Password</label>
                <input id="password" type="password" name="password"
                    class="w-full px-3 py-2.5 mb-4 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-stone-900 dark:text-stone-100 text-[15px] focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('password')
                    <div class="text-red-600 text-[13px] -mt-3 mb-3">{{ $message }}</div>
                @enderror

                <button type="submit"
                    class="w-full py-3 bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg text-base transition">Masuk</button>
            </form>
        </div>

        <div class="text-center mt-4">
            <button type="button" id="tombol-tema" onclick="gantiTema()"
                class="text-sm text-stone-500 dark:text-stone-400 hover:text-red-700 dark:hover:text-red-400 transition">
                Mode Gelap</button>
        </div>
    </div>

    <script>
        function perbaruiTombolTema() {
            var tombol = document.getElementById('tombol-tema');
            if (!tombol) return;
            tombol.textContent = document.body.classList.contains('dark') ? ' Mode Terang' : ' Mode Gelap';
        }

        function gantiTema() {
            var gelap = document.body.classList.toggle('dark');
            localStorage.setItem('kdmp-tema', gelap ? 'gelap' : 'terang');
            perbaruiTombolTema();
        }

        document.addEventListener('DOMContentLoaded', function() {
            perbaruiTombolTema();
        });
    </script>
</body>

</html>
