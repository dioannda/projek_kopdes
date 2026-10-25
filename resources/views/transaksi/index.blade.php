@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <h2 style="margin-top:0;">Riwayat Transaksi</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif

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
                    <td style="white-space:nowrap;">
                        <a href="{{ route('transaksi.show', $t) }}" class="btn">Lihat Struk</a>
                        <form method="POST" action="{{ route('transaksi.batalkan', $t) }}" style="display:inline;" onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
                            @csrf
                            <button type="submit" class="btn btn-danger">Batalkan</button>
                        </form>
                    </td>
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
