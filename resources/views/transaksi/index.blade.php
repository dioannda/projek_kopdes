@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <h2 style="margin-top:0;">Riwayat Transaksi</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Item</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $t)
                <tr>
                    <td>{{ $t->tanggal->format('d-m-Y H:i') }}</td>
                    <td>{{ $t->detailTransaksi->sum('jumlah') }}</td>
                    <td>Rp{{ number_format($t->total, 0, ',', '.') }}</td>
                    <td><a href="{{ route('transaksi.show', $t) }}" class="btn">Lihat Struk</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">Belum ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">
        {{ $transaksi->links() }}
    </div>
@endsection
