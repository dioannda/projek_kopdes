<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — SI Persediaan dan Transaksi KDMP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-stone-100 text-stone-900 dark:bg-stone-950 dark:text-stone-100 font-sans">
    <script>
        if (localStorage.getItem('kdmp-tema') === 'gelap') {
            document.body.classList.add('dark');
        }
    </script>

    <header
        class="bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800 shadow-sm px-6 py-3 flex justify-between items-center print:hidden">
        <div class="text-lg font-bold text-red-700 dark:text-red-400">SI Persediaan dan Transaksi Koperasi Desa Merah
            Putih</div>
        <div class="flex items-center gap-3 text-sm">
            @auth
                <span>{{ Auth::user()->nama }}</span>
                <span
                    class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-3 py-1 rounded-full capitalize">{{ Auth::user()->peran }}</span>
                <button type="button" id="tombol-tema" onclick="gantiTema()"
                    class="bg-white dark:bg-stone-900 text-red-700 dark:text-red-400 border border-red-700 dark:border-red-400 px-3 py-1.5 rounded-md font-bold hover:bg-red-50 dark:hover:bg-stone-800 transition">🌙
                    Gelap</button>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit"
                        class="bg-white dark:bg-stone-900 text-red-700 dark:text-red-400 border border-red-700 dark:border-red-400 px-3 py-1.5 rounded-md font-bold hover:bg-red-50 dark:hover:bg-stone-800 transition">Keluar</button>
                </form>
            @endauth
        </div>
    </header>

    <div class="flex flex-1">
        <aside
            class="w-60 shrink-0 bg-white dark:bg-stone-900 border-r border-stone-200 dark:border-stone-800 py-4 print:hidden">
            @php
                // Menu per peran (UC-01): setiap peran hanya melihat
                // menunya sendiri. Manajer dikelompokkan per bagian;
                // kasir dan gudang tetap satu kelompok tanpa judul.
                $menu = [
                    'kasir' => [
                        ['judul' => 'Utama', 'items' => [['label' => 'Dashboard', 'route' => 'dashboard']]],
                        [
                            'judul' => 'Penjualan',
                            'items' => [
                                ['label' => 'Transaksi Penjualan (POS)', 'route' => 'transaksi.create'],
                                ['label' => 'Riwayat Transaksi', 'route' => 'transaksi.index'],
                            ],
                        ],
                        ['judul' => 'Akun', 'items' => [['label' => 'Ubah Password', 'route' => 'profil.edit']]],
                    ],
                    'gudang' => [
                        ['judul' => 'Utama', 'items' => [['label' => 'Dashboard', 'route' => 'dashboard']]],
                        [
                            'judul' => 'Persediaan',
                            'items' => [
                                ['label' => 'Data Barang', 'route' => 'barang.index'],
                                ['label' => 'Barang Masuk', 'route' => 'barang-masuk.index'],
                                ['label' => 'Notifikasi', 'route' => 'notifikasi.index'],
                                ['label' => 'Rekomendasi EOQ', 'route' => 'rekomendasi.index'],
                            ],
                        ],
                        ['judul' => 'Akun', 'items' => [['label' => 'Ubah Password', 'route' => 'profil.edit']]],
                    ],
                    'manajer' => [
                        ['judul' => 'Utama', 'items' => [['label' => 'Dashboard', 'route' => 'dashboard']]],
                        [
                            'judul' => 'Persediaan',
                            'items' => [
                                ['label' => 'Data Barang', 'route' => 'barang.index'],
                                ['label' => 'Notifikasi', 'route' => 'notifikasi.index'],
                                ['label' => 'Rekomendasi EOQ', 'route' => 'rekomendasi.index'],
                            ],
                        ],
                        [
                            'judul' => 'Laporan & Pengaturan',
                            'items' => [
                                ['label' => 'Laporan', 'route' => 'laporan.index'],
                                ['label' => 'Pengaturan', 'route' => 'pengaturan.index'],
                            ],
                        ],
                        ['judul' => 'Akun', 'items' => [['label' => 'Ubah Password', 'route' => 'profil.edit']]],
                    ],
                ];
                $grupMenu = $menu[Auth::user()->peran ?? ''] ?? [];
                $belumDibaca = in_array(Auth::user()->peran ?? '', ['gudang', 'manajer'], true)
                    ? \App\Models\Notifikasi::where('status_baca', false)->count()
                    : 0;
            @endphp
            @foreach ($grupMenu as $grup)
                @if ($grup['judul'])
                    <span
                        class="block px-5 pt-4 pb-1 text-xs font-bold tracking-wider text-stone-400 uppercase">{{ $grup['judul'] }}</span>
                @endif
                @foreach ($grup['items'] as $item)
                    @if ($item['route'])
                        <a href="{{ route($item['route']) }}"
                            class="block mx-3 my-0.5 px-4 py-2.5 rounded-lg text-[15px] transition {{ request()->routeIs($item['route']) ? 'bg-red-700 text-white font-bold' : 'text-stone-900 dark:text-stone-100 hover:bg-red-50 dark:hover:bg-stone-800' }}">{{ $item['label'] }}
                            @if ($item['route'] === 'notifikasi.index' && $belumDibaca > 0)
                                <span
                                    class="float-right bg-red-600 text-white text-[11px] px-2 py-0.5 rounded-full">{{ $belumDibaca }}</span>
                            @endif
                        </a>
                    @else
                        <span
                            class="block mx-3 my-0.5 px-4 py-2.5 rounded-lg text-[15px] text-stone-400">{{ $item['label'] }}
                            <span
                                class="float-right bg-stone-200 text-stone-500 text-[11px] px-2 py-0.5 rounded-full">Segera</span></span>
                    @endif
                @endforeach
            @endforeach
        </aside>

        <main class="flex-1 p-6 print:p-0">
            @yield('content')
        </main>
    </div>

    <div id="modal-konfirmasi" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-stone-900 rounded-xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold mb-3">Konfirmasi</h3>
            <p id="modal-pesan" class="text-stone-600 dark:text-stone-300 mb-5"></p>
            <div class="flex gap-2 justify-end">
                <button type="button"
                    class="px-4 py-2 rounded-md bg-stone-500 text-white hover:bg-stone-600 transition"
                    onclick="tutupModal()">Batal</button>
                <button type="button" class="px-4 py-2 rounded-md bg-red-800 text-white hover:bg-red-900 transition"
                    id="modal-ya">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
        function perbaruiTombolTema() {
            var tombol = document.getElementById('tombol-tema');
            if (!tombol) return;
            tombol.textContent = document.body.classList.contains('dark') ? '☀️ Terang' : '🌙 Gelap';
        }

        function gantiTema() {
            var gelap = document.body.classList.toggle('dark');
            localStorage.setItem('kdmp-tema', gelap ? 'gelap' : 'terang');
            perbaruiTombolTema();
        }

        var formTertunda = null;

        function bukaModal(pesan, form) {
            document.getElementById('modal-pesan').textContent = pesan;
            document.getElementById('modal-konfirmasi').classList.remove('hidden');
            document.getElementById('modal-konfirmasi').classList.add('flex');
            formTertunda = form;
        }

        function tutupModal() {
            document.getElementById('modal-konfirmasi').classList.add('hidden');
            document.getElementById('modal-konfirmasi').classList.remove('flex');
            formTertunda = null;
        }
        document.getElementById('modal-ya').addEventListener('click', function() {
            if (formTertunda) {
                formTertunda.submit();
            }
            tutupModal();
        });
        document.getElementById('modal-konfirmasi').addEventListener('click', function(e) {
            if (e.target === this) {
                tutupModal();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            perbaruiTombolTema();
            document.querySelectorAll('form[data-konfirmasi]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    bukaModal(form.getAttribute('data-konfirmasi'), form);
                });
            });
        });
    </script>
</body>

</html>
