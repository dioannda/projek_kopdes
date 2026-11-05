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
        class="bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800 shadow-sm px-4 md:px-6 py-3 flex items-center gap-2 md:gap-3 print:hidden">
        <button type="button" id="tombol-sidebar"
            class="md:hidden shrink-0 w-10 h-10 rounded-lg bg-white dark:bg-stone-900 border border-stone-300 dark:border-stone-700 text-xl hover:bg-red-50 dark:hover:bg-stone-800 transition print:hidden">☰</button>
        <div class="flex-1 min-w-0 truncate text-base md:text-lg font-bold text-red-700 dark:text-red-400">
            <span class="hidden sm:inline">SI Persediaan dan Transaksi Koperasi Desa Merah Putih</span>
            <span class="sm:hidden">KDMP</span>
        </div>
        <div class="flex items-center gap-2 md:gap-3 text-sm ml-auto">
            @auth
                <span class="hidden lg:inline">{{ Auth::user()->nama }}</span>
                <span
                    class="hidden md:inline-flex bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-3 py-1 rounded-full capitalize">{{ Auth::user()->peran }}</span>
                <button type="button" id="tombol-tema" onclick="gantiTema()"
                    class="shrink-0 bg-white dark:bg-stone-900 text-red-700 dark:text-red-400 border border-red-700 dark:border-red-400 px-3 py-1.5 rounded-md font-bold hover:bg-red-50 dark:hover:bg-stone-800 transition">🌙
                    Gelap</button>
                <div class="relative shrink-0">
                    <button type="button" id="tombol-menu-profil"
                        class="w-10 h-10 rounded-full bg-white dark:bg-stone-900 border border-stone-300 dark:border-stone-700 text-xl font-bold hover:bg-red-50 dark:hover:bg-stone-800 transition">⋮</button>
                    <div id="menu-profil"
                        class="hidden absolute right-0 mt-2 w-52 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-lg z-50 py-1">
                        <div class="px-4 py-3 border-b border-stone-200 dark:border-stone-800">
                            <p class="font-bold">{{ Auth::user()->nama }}</p>
                            <p class="text-xs text-stone-500 dark:text-stone-400 capitalize">{{ Auth::user()->peran }}</p>
                        </div>
                        <a href="{{ route('profil.edit') }}"
                            class="block px-4 py-2.5 hover:bg-red-50 dark:hover:bg-stone-800 transition">🔑 Ubah
                            Password</a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2.5 hover:bg-red-50 dark:hover:bg-stone-800 transition">🚪
                                Keluar</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </header>

    <div class="flex flex-1 min-w-0">
        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 hidden md:hidden print:hidden"></div>
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-40 w-60 shrink-0 bg-white dark:bg-stone-900 border-r border-stone-200 dark:border-stone-800 py-4 print:hidden -translate-x-full transition-transform duration-200 md:static md:translate-x-0 overflow-y-auto">
            @php
                // Menu per peran (UC-01): setiap peran hanya melihat
                // menunya sendiri. Manajer dikelompokkan per bagian;
                // kasir dan gudang tetap satu kelompok tanpa judul.
                $menu = [
                    'kasir' => [
                        [
                            'judul' => 'Utama',
                            'items' => [['label' => 'Dashboard', 'route' => 'dashboard', 'ikon' => '🏠']],
                        ],
                        [
                            'judul' => 'Penjualan',
                            'items' => [
                                ['label' => 'Transaksi Penjualan (POS)', 'route' => 'transaksi.create', 'ikon' => '🧾'],
                                ['label' => 'Riwayat Transaksi', 'route' => 'transaksi.index', 'ikon' => '📋'],
                                ['label' => 'Rekap Shift Saya', 'route' => 'transaksi.rekap', 'ikon' => '🕐'],
                            ],
                        ],
                        [
                            'judul' => 'Akun',
                            'items' => [['label' => 'Ubah Password', 'route' => 'profil.edit', 'ikon' => '🔑']],
                        ],
                    ],
                    'gudang' => [
                        [
                            'judul' => 'Utama',
                            'items' => [['label' => 'Dashboard', 'route' => 'dashboard', 'ikon' => '🏠']],
                        ],
                        [
                            'judul' => 'Persediaan',
                            'items' => [
                                ['label' => 'Data Barang', 'route' => 'barang.index', 'ikon' => '📦'],
                                ['label' => 'Barang Masuk', 'route' => 'barang-masuk.index', 'ikon' => '📥'],
                                ['label' => 'Notifikasi', 'route' => 'notifikasi.index', 'ikon' => '🔔'],
                                ['label' => 'Rekomendasi EOQ', 'route' => 'rekomendasi.index', 'ikon' => '📊'],
                            ],
                        ],
                        [
                            'judul' => 'Akun',
                            'items' => [['label' => 'Ubah Password', 'route' => 'profil.edit', 'ikon' => '🔑']],
                        ],
                    ],
                    'manajer' => [
                        [
                            'judul' => 'Utama',
                            'items' => [['label' => 'Dashboard', 'route' => 'dashboard', 'ikon' => '🏠']],
                        ],
                        [
                            'judul' => 'Persediaan',
                            'items' => [
                                ['label' => 'Data Barang', 'route' => 'barang.index', 'ikon' => '📦'],
                                ['label' => 'Notifikasi', 'route' => 'notifikasi.index', 'ikon' => '🔔'],
                                ['label' => 'Rekomendasi EOQ', 'route' => 'rekomendasi.index', 'ikon' => '📊'],
                            ],
                        ],
                        [
                            'judul' => 'Laporan & Pengaturan',
                            'items' => [
                                ['label' => 'Laporan', 'route' => 'laporan.index', 'ikon' => '📈'],
                                ['label' => 'Pengaturan', 'route' => 'pengaturan.index', 'ikon' => '⚙️'],
                            ],
                        ],
                        [
                            'judul' => 'Akun',
                            'items' => [['label' => 'Ubah Password', 'route' => 'profil.edit', 'ikon' => '🔑']],
                        ],
                    ],
                ];
                $grupMenu = $menu[Auth::user()->peran ?? ''] ?? [];
                $belumDibaca = in_array(Auth::user()->peran ?? '', ['gudang', 'manajer'], true)
                    ? \App\Models\Notifikasi::where('status_baca', false)->count()
                    : 0;
            @endphp
            @foreach ($grupMenu as $grup)
                @if ($grup['judul'])
                    <button type="button"
                        class="w-full flex items-center justify-between px-5 pt-4 pb-1 text-xs font-bold tracking-wider text-stone-400 uppercase hover:text-red-700 dark:hover:text-red-400 transition"
                        data-grup-tombol="{{ $grup['judul'] }}">
                        <span>{{ $grup['judul'] }}</span>
                        <span class="chevron transition-transform">▾</span>
                    </button>
                @endif
                <div data-grup-isi="{{ $grup['judul'] }}">
                    @foreach ($grup['items'] as $item)
                        @if ($item['route'])
                            <a href="{{ route($item['route']) }}"
                                class="flex items-center gap-2 mx-3 my-0.5 px-4 py-2.5 rounded-lg text-[15px] transition {{ request()->routeIs($item['route']) ? 'bg-red-700 text-white font-bold' : 'text-stone-900 dark:text-stone-100 hover:bg-red-50 dark:hover:bg-stone-800' }}">
                                <span class="shrink-0">{{ $item['ikon'] }}</span>
                                <span class="flex-1">{{ $item['label'] }}</span>
                                @if ($item['route'] === 'notifikasi.index' && $belumDibaca > 0)
                                    <span
                                        class="ml-auto bg-red-600 text-white text-[11px] px-2 py-0.5 rounded-full">{{ $belumDibaca }}</span>
                                @endif
                            </a>
                        @else
                            <span
                                class="block mx-3 my-0.5 px-4 py-2.5 rounded-lg text-[15px] text-stone-400">{{ $item['label'] }}
                                <span
                                    class="float-right bg-stone-200 text-stone-500 text-[11px] px-2 py-0.5 rounded-full">Segera</span></span>
                        @endif
                    @endforeach
                </div>
            @endforeach

        </aside>

        <main class="flex-1 p-4 md:p-6 print:p-0 min-w-0">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Drawer sidebar di HP.
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebar-overlay');

        function tutupSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        function bukaSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }
        document.getElementById('tombol-sidebar').addEventListener('click', function() {
            if (sidebar.classList.contains('-translate-x-full')) {
                bukaSidebar();
            } else {
                tutupSidebar();
            }
        });
        overlay.addEventListener('click', tutupSidebar);
        sidebar.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    tutupSidebar();
                }
            });
        });

        // Grup sidebar bisa dilipat; pilihannya tersimpan.
        var grupTutup = [];
        try {
            grupTutup = JSON.parse(localStorage.getItem('kdmp-grup-tutup') || '[]');
        } catch (e) {
            grupTutup = [];
        }

        function terapkanGrup() {
            document.querySelectorAll('[data-grup-isi]').forEach(function(isi) {
                var nama = isi.getAttribute('data-grup-isi');
                var tutup = grupTutup.indexOf(nama) !== -1;
                isi.classList.toggle('hidden', tutup);
                var tombol = document.querySelector('[data-grup-tombol="' + nama + '"]');
                if (tombol) {
                    var chev = tombol.querySelector('.chevron');
                    if (chev) {
                        chev.style.transform = tutup ? 'rotate(-90deg)' : 'rotate(0deg)';
                    }
                }
            });
        }
        document.querySelectorAll('[data-grup-tombol]').forEach(function(tombol) {
            tombol.addEventListener('click', function() {
                var nama = tombol.getAttribute('data-grup-tombol');
                var i = grupTutup.indexOf(nama);
                if (i === -1) {
                    grupTutup.push(nama);
                } else {
                    grupTutup.splice(i, 1);
                }
                localStorage.setItem('kdmp-grup-tutup', JSON.stringify(grupTutup));
                terapkanGrup();
            });
        });

        // Menu titik tiga profil.
        var tombolProfil = document.getElementById('tombol-menu-profil');
        var menuProfil = document.getElementById('menu-profil');
        if (tombolProfil && menuProfil) {
            tombolProfil.addEventListener('click', function(e) {
                e.stopPropagation();
                menuProfil.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!menuProfil.contains(e.target) && !tombolProfil.contains(e.target)) {
                    menuProfil.classList.add('hidden');
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    menuProfil.classList.add('hidden');
                    tutupSidebar();
                }
            });
        }

        // Konfirmasi SweetAlert untuk semua form data-konfirmasi.
        // Kalau CDN gagal dimuat (offline), jatuh ke confirm() bawaan.
        document.addEventListener('DOMContentLoaded', function() {
            perbaruiTombolTema();
            terapkanGrup();
            document.querySelectorAll('form[data-konfirmasi]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var pesan = form.getAttribute('data-konfirmasi');
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: pesan,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#b91c1c',
                            cancelButtonColor: '#57534e',
                        }).then(function(r) {
                            if (r.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else if (confirm(pesan)) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>
