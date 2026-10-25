@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
    <h2 style="margin-top:0;">Barang Masuk</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <h3 style="margin-top:0;">Catat Penerimaan</h3>
        <form method="POST" action="{{ route('barang-masuk.store') }}">
            @csrf
            <div class="form-group">
                <label for="id_barang">Barang</label>
                <select id="id_barang" name="id_barang" style="width:100%;">
                    <option value="">-- Pilih barang --</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->id }}" {{ old('id_barang', $barangTerpilih) == $b->id ? 'selected' : '' }}>
                            {{ $b->nama }} (stok: {{ $b->stok }})
                        </option>
                    @endforeach
                </select>
                @error('id_barang')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="jumlah">Jumlah Masuk</label>
                <input id="jumlah" type="number" min="1" name="jumlah" value="{{ old('jumlah') }}">
                @error('jumlah')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}">
                @error('tanggal')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>

    <div class="card" style="margin-top:16px;">
        <h3 style="margin-top:0;">Riwayat Penerimaan</h3>
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Dicatat Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $r)
                    <tr>
                        <td>{{ $r->tanggal->format('d-m-Y') }}</td>
                        <td>{{ $r->barang->nama }}</td>
                        <td>{{ $r->jumlah }}</td>
                        <td>{{ $r->pengguna->nama }}</td>
                        <td>
                            <form method="POST" action="{{ route('barang-masuk.destroy', $r) }}" style="display:inline;" onsubmit="return confirm('Hapus riwayat ini? Stok {{ $r->barang->nama }} akan dikembalikan {{ $r->jumlah }}.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">Belum ada riwayat penerimaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $riwayat->links() }}
        </div>
    </div>
@endsection
