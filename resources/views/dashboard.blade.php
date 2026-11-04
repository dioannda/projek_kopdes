@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if ($pengguna->peran === 'manajer')
        <h2 class="page-judul">Dashboard Manajer</h2>
        <p class="page-subjudul">Selamat datang, {{ $pengguna->nama }}. Ringkasan kondisi persediaan dan penjualan hari ini
            untuk pengambilan keputusan.</p>

        @php
            $aksen = [
                'Total barang' => '#b91c1c',
                'Stok menipis' => '#d97706',
                'Stok kosong' => '#7f1d1d',
                'Mendekati kedaluwarsa' => '#6b7280',
                'Notifikasi belum dibaca' => '#6b7280',
                'Transaksi hari ini' => '#b91c1c',
                'Pendapatan hari ini' => '#7f1d1d',
            ];
            $grupPenjualan = ['Transaksi hari ini', 'Pendapatan hari ini'];
        @endphp

        <div class="grup-judul">Kondisi Persediaan</div>
        <div class="stat-grid">
            @foreach ($stats as $stat)
                @if (!in_array($stat['label'], $grupPenjualan, true))
                    <div class="stat-card" style="border-left-color: {{ $aksen[$stat['label']] ?? '#b91c1c' }};">
                        <a href="{{ $stat['url'] }}">
                            <span class="stat-label">{{ $stat['label'] }}</span>
                            <p class="stat-nilai">{{ $stat['nilai'] }}</p>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="grup-judul">Penjualan Hari Ini</div>
        <div class="stat-grid">
            @foreach ($stats as $stat)
                @if (in_array($stat['label'], $grupPenjualan, true))
                    <div class="stat-card" style="border-left-color: {{ $aksen[$stat['label']] ?? '#b91c1c' }};">
                        <a href="{{ $stat['url'] }}">
                            <span class="stat-label">{{ $stat['label'] }}</span>
                            <p class="stat-nilai">{{ $stat['nilai'] }}</p>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    @elseif ($pengguna->peran === 'gudang')
        <h2 class="page-judul">Dashboard Gudang</h2>
        <p class="page-subjudul">Selamat datang, {{ $pengguna->nama }}. Ringkasan kondisi stok gudang dan notifikasi yang
            belum dibaca.</p>

        @php
            $aksenGudang = [
                'Total barang' => '#b91c1c',
                'Stok menipis' => '#d97706',
                'Stok kosong' => '#7f1d1d',
                'Mendekati kedaluwarsa' => '#6b7280',
                'Notifikasi belum dibaca' => '#6b7280',
            ];
            $grupNotifikasi = ['Notifikasi belum dibaca'];
        @endphp

        <div class="grup-judul">Kondisi Stok</div>
        <div class="stat-grid">
            @foreach ($stats as $stat)
                @if (!in_array($stat['label'], $grupNotifikasi, true))
                    <div class="stat-card" style="border-left-color: {{ $aksenGudang[$stat['label']] ?? '#b91c1c' }};">
                        <a href="{{ $stat['url'] }}">
                            <span class="stat-label">{{ $stat['label'] }}</span>
                            <p class="stat-nilai">{{ $stat['nilai'] }}</p>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="grup-judul">Notifikasi</div>
        <div class="stat-grid">
            @foreach ($stats as $stat)
                @if (in_array($stat['label'], $grupNotifikasi, true))
                    <div class="stat-card" style="border-left-color: {{ $aksenGudang[$stat['label']] ?? '#b91c1c' }};">
                        <a href="{{ $stat['url'] }}">
                            <span class="stat-label">{{ $stat['label'] }}</span>
                            <p class="stat-nilai">{{ $stat['nilai'] }}</p>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    @elseif ($pengguna->peran === 'kasir')
        <h2 class="page-judul">Dashboard Kasir</h2>
        <p class="page-subjudul">Selamat datang, {{ $pengguna->nama }}. Ringkasan penjualan Anda hari ini.</p>

        @php
            $aksenKasir = [
                'Transaksi hari ini' => '#b91c1c',
                'Pendapatan hari ini' => '#7f1d1d',
            ];
        @endphp

        <div class="grup-judul">Penjualan Hari Ini</div>
        <div class="stat-grid">
            @foreach ($stats as $stat)
                <div class="stat-card" style="border-left-color: {{ $aksenKasir[$stat['label']] ?? '#b91c1c' }};">
                    <a href="{{ $stat['url'] }}">
                        <span class="stat-label">{{ $stat['label'] }}</span>
                        <p class="stat-nilai">{{ $stat['nilai'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div
            class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
            <h2 class="text-2xl font-bold">Selamat datang, {{ $pengguna->nama }}</h2>
            <p class="mt-2">Anda login sebagai <strong class="capitalize">{{ $pengguna->peran }}</strong>.</p>
        </div>

        <div class="flex gap-4 mt-4 flex-wrap">
            @foreach ($stats as $stat)
                <div
                    class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 flex-1 min-w-[160px]">
                    <a href="{{ $stat['url'] }}" class="block">
                        <strong>{{ $stat['label'] }}</strong>
                        <p class="text-xl mt-2">{{ $stat['nilai'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
