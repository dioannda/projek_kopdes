@extends('layouts.app')

@section('title', 'Struk Penjualan')

@section('content')
    <h2 style="margin-top:0;">Struk Penjualan</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif

    <div class="card">
        <p style="margin:4px 0;">Tanggal: {{ $transaksi->tanggal->format('d-m-Y H:i') }}</p>
        <p style="margin:4px 0;">Kasir: {{ $transaksi->kasir->nama }}</p>
        <table class="data" style="margin-top:12px;">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi->detailTransaksi as $d)
                    <tr>
                        <td>{{ $d->barang->nama }}</td>
                        <td>Rp{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td>{{ $d->jumlah }}</td>
                        <td>Rp{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:18px;"><strong>Total: Rp{{ number_format($transaksi->total, 0, ',', '.') }}</strong></p>
        <p>Uang Bayar: {{ session('bayar') !== null ? 'Rp' . number_format(session('bayar'), 0, ',', '.') : '-' }}</p>
        <p>Kembalian: {{ session('kembalian') !== null ? 'Rp' . number_format(session('kembalian'), 0, ',', '.') : '-' }}</p>
        <button type="button" class="btn" onclick="window.print()">Cetak Struk</button>
        @if (Auth::user()->peran === 'kasir')
            <a href="{{ route('transaksi.create') }}" class="btn btn-secondary">Transaksi Baru</a>
            <form method="POST" action="{{ route('transaksi.batalkan', $transaksi) }}" style="display:inline;" onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
                @csrf
                <button type="submit" class="btn btn-danger">Batalkan Transaksi</button>
            </form>
        @endif
    </div>

    @if (session('sukses'))
        {{-- Struk baru dari penjualan langsung membuka dialog cetak
             (perilaku POS); struk lama yang dibuka ulang tidak. --}}
        <script>
            window.addEventListener('load', () => window.print());
        </script>
    @endif
@endsection
