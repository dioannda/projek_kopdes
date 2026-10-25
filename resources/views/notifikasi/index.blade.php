@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <h2 style="margin-top:0;">Notifikasi</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Jenis</th>
                <th>Pesan</th>
                <th>Rekomendasi EOQ</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($notifikasi as $n)
                <tr>
                    <td>{{ $n->tanggal->format('d-m-Y H:i') }}</td>
                    <td>{{ $n->barang->nama }}</td>
                    <td>
                        @if ($n->jenis === 'stok_kosong')
                            Stok Kosong
                        @elseif ($n->jenis === 'stok_menipis')
                            Stok Menipis
                        @else
                            Kedaluwarsa
                        @endif
                    </td>
                    <td>{{ $n->pesan }}</td>
                    <td>{{ $n->rekomendasi_jumlah !== null ? $n->rekomendasi_jumlah . ' unit' : '-' }}</td>
                    <td>{{ $n->status_baca ? 'Dibaca' : 'Belum dibaca' }}</td>
                    <td>
                        <a href="{{ route('notifikasi.show', $n) }}" class="btn">Lihat</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">Belum ada notifikasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">
        {{ $notifikasi->links() }}
    </div>
@endsection
