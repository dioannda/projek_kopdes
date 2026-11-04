@extends('layouts.app')

@section('title', 'Detail Notifikasi')

@section('content')
    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <h2 class="text-2xl font-bold">Detail Notifikasi</h2>
        <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Rincian pemberitahuan dan barang yang terkait.</p>
        <div class="space-y-2">
            <p><strong>Tanggal:</strong> {{ $notifikasi->tanggal->format('d-m-Y H:i') }}</p>
            <p><strong>Barang:</strong> {{ $notifikasi->barang->nama }}</p>
            <p><strong>Jenis:</strong>
                @if ($notifikasi->jenis === 'stok_kosong')
                    Stok Kosong
                @elseif ($notifikasi->jenis === 'stok_menipis')
                    Stok Menipis
                @else
                    Kedaluwarsa
                @endif
            </p>
            <p><strong>Pesan:</strong> {{ $notifikasi->pesan }}</p>
            <p><strong>Rekomendasi EOQ:</strong>
                {{ $notifikasi->rekomendasi_jumlah !== null ? $notifikasi->rekomendasi_jumlah . ' unit' : '-' }}</p>
            <p><strong>Status:</strong> Dibaca</p>
        </div>
        <div class="mt-4 flex gap-2 flex-wrap">
            <a href="{{ route('barang.edit', $notifikasi->barang) }}"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Lihat
                Barang</a>
            <a href="{{ route('notifikasi.index') }}"
                class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Kembali
                ke Daftar</a>
        </div>
    </div>
@endsection
