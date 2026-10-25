<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — SI Persediaan dan Transaksi KDMP</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f4f6f4; color: #222; }
        .header { background: #1b6b3a; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        .header .brand { font-size: 18px; font-weight: bold; }
        .header .user { font-size: 14px; display: flex; gap: 12px; align-items: center; }
        .header .user .peran { background: rgba(255,255,255,.25); padding: 3px 10px; border-radius: 12px; text-transform: capitalize; }
        .header button { background: #fff; color: #1b6b3a; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .wrap { display: flex; min-height: calc(100vh - 52px); }
        .sidebar { width: 230px; background: #fff; border-right: 1px solid #ddd; padding: 16px 0; flex-shrink: 0; }
        .sidebar a, .sidebar span.item { display: block; padding: 10px 20px; color: #222; text-decoration: none; font-size: 15px; }
        .sidebar a:hover { background: #e8f3ec; }
        .sidebar a.active { background: #1b6b3a; color: #fff; font-weight: bold; }
        .sidebar span.item { color: #999; }
        .sidebar span.item .badge { float: right; background: #eee; color: #888; font-size: 11px; padding: 2px 7px; border-radius: 10px; }
        .content { flex: 1; padding: 24px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; max-width: 720px; }
        .alert { background: #e8f3ec; border: 1px solid #b7dcc4; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
        @media (max-width: 700px) {
            .wrap { flex-direction: column; }
            .sidebar { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">SI Persediaan dan Transaksi Koperasi Desa Merah Putih</div>
        <div class="user">
            @auth
                <span>{{ Auth::user()->nama }}</span>
                <span class="peran">{{ Auth::user()->peran }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit">Keluar</button>
                </form>
            @endauth
        </div>
    </div>

    <div class="wrap">
        <div class="sidebar">
            @php
                // Menu per peran (UC-01). Item yang halamannya belum dibangun
                // (UC berikutnya) tampil nonaktif dengan label "Segera" dan
                // diaktifkan saat UC tersebut selesai.
                $menu = [
                    'kasir' => [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Transaksi Penjualan (POS)', 'route' => null],
                    ],
                    'gudang' => [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Data Barang', 'route' => null],
                        ['label' => 'Barang Masuk', 'route' => null],
                        ['label' => 'Notifikasi', 'route' => null],
                        ['label' => 'Rekomendasi EOQ', 'route' => null],
                    ],
                    'manajer' => [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Data Barang', 'route' => null],
                        ['label' => 'Notifikasi', 'route' => null],
                        ['label' => 'Rekomendasi EOQ', 'route' => null],
                        ['label' => 'Laporan', 'route' => null],
                        ['label' => 'Pengaturan', 'route' => null],
                    ],
                ];
                $items = $menu[Auth::user()->peran ?? ''] ?? [];
            @endphp
            @foreach ($items as $item)
                @if ($item['route'])
                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">{{ $item['label'] }}</a>
                @else
                    <span class="item">{{ $item['label'] }} <span class="badge">Segera</span></span>
                @endif
            @endforeach
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>
</body>
</html>
