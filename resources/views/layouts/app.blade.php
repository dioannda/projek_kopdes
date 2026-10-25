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
        .alert-error { background: #fdecea; border-color: #f5c2bd; }
        table.data { width: 100%; border-collapse: collapse; background: #fff; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; font-size: 14px; }
        table.data th { background: #e8f3ec; }
        .btn { display: inline-block; background: #1b6b3a; color: #fff; padding: 8px 14px; border-radius: 4px; text-decoration: none; font-size: 14px; border: none; cursor: pointer; }
        .btn-danger { background: #b3261e; }
        .btn-secondary { background: #666; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-weight: bold; font-size: 14px; margin-bottom: 4px; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .field-error { color: #a33; font-size: 13px; }
        @media (max-width: 700px) {
            .wrap { flex-direction: column; }
            .sidebar { width: 100%; }
        }
        @media print {
            .header, .sidebar, .btn { display: none !important; }
            .wrap { display: block; }
            .content { padding: 0; }
            .card { max-width: none; border: none; padding: 0; }
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
                // Menu per peran (UC-01): setiap peran hanya melihat
                // menunya sendiri.
                $menu = [
                    'kasir' => [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Transaksi Penjualan (POS)', 'route' => 'transaksi.create'],
                        ['label' => 'Riwayat Transaksi', 'route' => 'transaksi.index'],
                        ['label' => 'Ubah Password', 'route' => 'profil.edit'],
                    ],
                    'gudang' => [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Data Barang', 'route' => 'barang.index'],
                        ['label' => 'Barang Masuk', 'route' => 'barang-masuk.index'],
                        ['label' => 'Notifikasi', 'route' => 'notifikasi.index'],
                        ['label' => 'Rekomendasi EOQ', 'route' => 'rekomendasi.index'],
                        ['label' => 'Ubah Password', 'route' => 'profil.edit'],
                    ],
                    'manajer' => [
                        ['label' => 'Dashboard', 'route' => 'dashboard'],
                        ['label' => 'Data Barang', 'route' => 'barang.index'],
                        ['label' => 'Notifikasi', 'route' => 'notifikasi.index'],
                        ['label' => 'Rekomendasi EOQ', 'route' => 'rekomendasi.index'],
                        ['label' => 'Laporan', 'route' => 'laporan.index'],
                        ['label' => 'Pengaturan', 'route' => 'pengaturan.index'],
                        ['label' => 'Ubah Password', 'route' => 'profil.edit'],
                    ],
                ];
                $items = $menu[Auth::user()->peran ?? ''] ?? [];
                $belumDibaca = in_array(Auth::user()->peran ?? '', ['gudang', 'manajer'], true)
                    ? \App\Models\Notifikasi::where('status_baca', false)->count()
                    : 0;
            @endphp
            @foreach ($items as $item)
                @if ($item['route'])
                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">{{ $item['label'] }}@if ($item['route'] === 'notifikasi.index' && $belumDibaca > 0) <span class="badge">{{ $belumDibaca }}</span>@endif</a>
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
