@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <h2 style="margin-top:0;">Laporan Penjualan</h2>

    <form method="GET" action="{{ route('laporan.index') }}" style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:end;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="mulai">Dari Tanggal</label>
            <input id="mulai" type="date" name="mulai" value="{{ $mulai }}">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="sampai">Sampai Tanggal</label>
            <input id="sampai" type="date" name="sampai" value="{{ $sampai }}">
        </div>
        <button type="submit" class="btn">Tampilkan</button>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div style="display:flex; gap:16px; margin-bottom:16px; flex-wrap:wrap;">
        <div class="card" style="flex:1; min-width:180px;">
            <strong>Jumlah Transaksi</strong>
            <p style="font-size:20px; margin:8px 0 0;">{{ $totalTransaksi }}</p>
        </div>
        <div class="card" style="flex:1; min-width:180px;">
            <strong>Total Pendapatan</strong>
            <p style="font-size:20px; margin:8px 0 0;">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Jumlah Item</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $t)
                <tr>
                    <td>{{ $t->tanggal->format('d-m-Y H:i') }}</td>
                    <td>{{ $t->kasir->nama }}</td>
                    <td>{{ $t->detailTransaksi->sum('jumlah') }}</td>
                    <td>Rp{{ number_format($t->total, 0, ',', '.') }}</td>
                    <td><a href="{{ route('transaksi.show', $t) }}" class="btn">Lihat Struk</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Tidak ada transaksi pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">
        {{ $transaksi->links() }}
    </div>

    <div style="margin-top:16px;">
        <button type="button" class="btn" onclick="window.print()">Cetak Laporan</button>
    </div>

    <h3 style="margin-top:24px;">Stok Terkini</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Stok</th>
                <th>Min.</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stok as $b)
                <tr>
                    <td>{{ $b->nama }}</td>
                    <td>{{ $b->stok }}</td>
                    <td>{{ $b->stok_minimum }}</td>
                    <td>
                        @if ($b->cekStok() === 'kosong')
                            Kosong
                        @elseif ($b->cekStok() === 'menipis')
                            Menipis
                        @else
                            Aman
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">Belum ada data barang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
