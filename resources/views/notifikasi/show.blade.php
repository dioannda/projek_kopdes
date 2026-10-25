@extends('layouts.app')

@section('title', 'Detail Notifikasi')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Detail Notifikasi</h2>
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
        <p><strong>Rekomendasi EOQ:</strong> {{ $notifikasi->rekomendasi_jumlah !== null ? $notifikasi->rekomendasi_jumlah . ' unit' : '-' }}</p>
        <p><strong>Status:</strong> Dibaca</p>
        <a href="{{ route('barang.edit', $notifikasi->barang) }}" class="btn">Lihat Barang</a>
        <a href="{{ route('notifikasi.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
    </div>
@endsection
