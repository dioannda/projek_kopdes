@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if ($pengguna->peran === 'manajer')
        <h2 class="text-2xl font-bold">Dashboard Manajer</h2>
        <p class="text-stone-500 dark:text-stone-400 mb-6">Selamat datang, {{ $pengguna->nama }}. Ringkasan kondisi persediaan dan penjualan hari ini untuk pengambilan keputusan.</p>

        @php
            $gaya = [
                'Total barang' => ['bg-red-100', 'text-red-700', '📦'],
                'Stok menipis' => ['bg-amber-100', 'text-amber-700', '⚠️'],
                'Stok kosong' => ['bg-red-200', 'text-red-900', '⛔'],
                'Mendekati kedaluwarsa' => ['bg-stone-100', 'text-stone-600', '🕐'],
                'Notifikasi belum dibaca' => ['bg-orange-100', 'text-orange-700', '🔔'],
                'Transaksi hari ini' => ['bg-orange-100', 'text-orange-700', '🧾'],
                'Pendapatan hari ini' => ['bg-amber-100', 'text-amber-800', '💰'],
            ];
            $grupPenjualan = ['Transaksi hari ini', 'Pendapatan hari ini'];
        @endphp

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mb-3">Kondisi Persediaan</div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (!in_array($stat['label'], $grupPenjualan, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}" class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition">
                        <span class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl">{{ $g[2] }}</span>
                        <span>
                            <span class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mt-6 mb-3">Penjualan Hari Ini</div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (in_array($stat['label'], $grupPenjualan, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}" class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition">
                        <span class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl">{{ $g[2] }}</span>
                        <span>
                            <span class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    @elseif ($pengguna->peran === 'gudang')
        <h2 class="text-2xl font-bold">Dashboard Gudang</h2>
        <p class="text-stone-500 dark:text-stone-400 mb-6">Selamat datang, {{ $pengguna->nama }}. Ringkasan kondisi stok gudang dan notifikasi yang belum dibaca.</p>

        @php
            $gaya = [
                'Total barang' => ['bg-red-100', 'text-red-700', '📦'],
                'Stok menipis' => ['bg-amber-100', 'text-amber-700', '⚠️'],
                'Stok kosong' => ['bg-red-200', 'text-red-900', '⛔'],
                'Mendekati kedaluwarsa' => ['bg-stone-100', 'text-stone-600', '🕐'],
                'Notifikasi belum dibaca' => ['bg-orange-100', 'text-orange-700', '🔔'],
            ];
            $grupNotifikasi = ['Notifikasi belum dibaca'];
        @endphp

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mb-3">Kondisi Stok</div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (!in_array($stat['label'], $grupNotifikasi, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}" class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition">
                        <span class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl">{{ $g[2] }}</span>
                        <span>
                            <span class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mt-6 mb-3">Notifikasi</div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (in_array($stat['label'], $grupNotifikasi, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}" class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition">
                        <span class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl">{{ $g[2] }}</span>
                        <span>
                            <span class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    @elseif ($pengguna->peran === 'kasir')
        <h2 class="text-2xl font-bold">Dashboard Kasir</h2>
        <p class="text-stone-500 dark:text-stone-400 mb-6">Selamat datang, {{ $pengguna->nama }}. Ringkasan penjualan Anda hari ini.</p>

        @php
            $gaya = [
                'Transaksi hari ini' => ['bg-orange-100', 'text-orange-700', '🧾'],
                'Pendapatan hari ini' => ['bg-amber-100', 'text-amber-800', '💰'],
            ];
        @endphp

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mb-3">Penjualan Hari Ini</div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                <a href="{{ $stat['url'] }}" class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition">
                    <span class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl">{{ $g[2] }}</span>
                    <span>
                        <span class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                        <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
            <h2 class="text-2xl font-bold">Selamat datang, {{ $pengguna->nama }}</h2>
            <p class="mt-2">Anda login sebagai <strong class="capitalize">{{ $pengguna->peran }}</strong>.</p>
        </div>

        <div class="flex gap-4 mt-4 flex-wrap">
            @foreach ($stats as $stat)
                <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 flex-1 min-w-[160px]">
                    <a href="{{ $stat['url'] }}" class="block">
                        <strong>{{ $stat['label'] }}</strong>
                        <p class="text-xl mt-2">{{ $stat['nilai'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
