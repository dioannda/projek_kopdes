@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <h2 style="margin-top:0;">Pengaturan</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h3 style="margin-top:0;">Parameter EOQ</h3>
        <form method="POST" action="{{ route('pengaturan.updateParameter') }}">
            @csrf
            <div class="form-group">
                <label for="S">Biaya Pemesanan (S) per pesanan (Rp)</label>
                <input id="S" type="number" step="0.01" min="0" name="S" value="{{ old('S', $nilaiS) }}">
                @error('S')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="H">Tarif Penyimpanan (H) per bulan</label>
                <input id="H" type="number" step="0.01" min="0" max="1" name="H" value="{{ old('H', $tarifH) }}">
                <p style="font-size:13px; margin:4px 0 0;">Diisi sebagai desimal, contoh 0,02 = 2% dari harga beli per bulan. H per unit = harga beli x tarif ini.</p>
                @error('H')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn">Simpan Parameter</button>
        </form>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3 style="margin-top:0;">Kelola Pengguna</h3>
        <p><a href="{{ route('pengguna.create') }}" class="btn">+ Tambah Pengguna</a></p>
        <table class="data">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Peran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengguna as $p)
                    <tr>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->username }}</td>
                        <td>{{ $p->peran }}</td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('pengguna.edit', $p) }}" class="btn">Ubah</a>
                            <form method="POST" action="{{ route('pengguna.destroy', $p) }}" style="display:inline;" onsubmit="return confirm('Hapus pengguna {{ $p->nama }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
