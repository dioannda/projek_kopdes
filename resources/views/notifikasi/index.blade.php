@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <h2 style="margin-top:0;">Notifikasi</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif

    <form method="POST" action="{{ route('notifikasi.baca-semua') }}" style="margin-bottom:16px;">
        @csrf
        <button type="submit" class="btn btn-secondary">Tandai Semua Dibaca</button>
    </form>

    <form method="GET" action="{{ route('notifikasi.index') }}" style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
        <select name="jenis">
            <option value="">Semua jenis</option>
            <option value="stok_kosong" {{ request('jenis') === 'stok_kosong' ? 'selected' : '' }}>Stok Kosong</option>
            <option value="stok_menipis" {{ request('jenis') === 'stok_menipis' ? 'selected' : '' }}>Stok Menipis</option>
            <option value="kedaluwarsa" {{ request('jenis') === 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
        </select>
        <label style="display:flex; align-items:center; gap:6px; font-size:14px;">
            <input type="checkbox" name="belum_dibaca" value="1" {{ request('belum_dibaca') === '1' ? 'checked' : '' }}>
            Hanya yang belum dibaca
        </label>
        <button type="submit" class="btn">Tampilkan</button>
        <a href="{{ route('notifikasi.index') }}" class="btn btn-secondary">Reset</a>
    </form>

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
