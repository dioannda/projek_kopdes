@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    <h2 style="margin-top:0;">Data Barang</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('barang.index') }}" style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama/kode..." style="flex:1; min-width:180px;">
        <select name="kategori">
            <option value="">Semua kategori</option>
            @foreach ($daftarKategori as $kategori)
                <option value="{{ $kategori }}" {{ request('kategori') === $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">Semua status</option>
            <option value="menipis" {{ request('status') === 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
            <option value="kosong" {{ request('status') === 'kosong' ? 'selected' : '' }}>Stok Kosong</option>
            <option value="kedaluwarsa" {{ request('status') === 'kedaluwarsa' ? 'selected' : '' }}>Mendekati Kedaluwarsa</option>
        </select>
        <button type="submit" class="btn">Cari</button>
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <p><a href="{{ route('barang.create') }}" class="btn">+ Tambah Barang</a></p>

    <table class="data">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>Harga Beli</th>
                <th>Stok</th>
                <th>Min.</th>
                <th>Kedaluwarsa</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barang as $b)
                <tr>
                    <td>{{ $b->kode }}</td>
                    <td>{{ $b->nama }}</td>
                    <td>{{ $b->kategori }}</td>
                    <td>Rp{{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                    <td>{{ $b->stok }}</td>
                    <td>{{ $b->stok_minimum }}</td>
                    <td>{{ $b->tanggal_kedaluarsa ? $b->tanggal_kedaluarsa->format('d-m-Y') : '-' }}</td>
                    <td>
                        @if ($b->tanggal_kedaluarsa === null)
                            -
                        @elseif ($b->cekKedaluarsa())
                            {{ $b->tanggal_kedaluarsa->lt(today()) ? 'Sudah kedaluwarsa' : 'Mendekati kedaluwarsa' }}
                        @else
                            Aman
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('barang.edit', $b) }}" class="btn">Ubah</a>
                        <form method="POST" action="{{ route('barang.destroy', $b) }}" style="display:inline;" onsubmit="return confirm('Hapus barang {{ $b->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">Belum ada data barang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">
        {{ $barang->links() }}
    </div>
@endsection
