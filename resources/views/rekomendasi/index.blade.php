@extends('layouts.app')

@section('title', 'Rekomendasi EOQ')

@section('content')
    <h2 style="margin-top:0;">Rekomendasi EOQ</h2>

    <p style="font-size:14px;">
        EOQ dihitung otomatis setiap kali halaman ini dibuka (EOQ = akar(2DS/H)).
        D = total penjualan 30 hari terakhir; S = biaya pemesanan; H per unit = harga beli x tarif.
        S dan tarif H diatur Manajer di halaman Pengaturan. Barang tanpa penjualan 30 hari terakhir
        berstatus "Menunggu data" dan tidak diberi angka rekomendasi.
    </p>

    <table class="data">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Stok</th>
                <th>Min.</th>
                <th>D (30 hari)</th>
                <th>S</th>
                <th>H / unit</th>
                <th>EOQ</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekomendasi as $r)
                <tr>
                    <td>{{ $r['barang']->nama }}</td>
                    <td>{{ $r['barang']->stok }}</td>
                    <td>{{ $r['barang']->stok_minimum }}</td>
                    <td>{{ $r['D'] }}</td>
                    <td>{{ $r['S'] !== null ? 'Rp' . number_format($r['S'], 0, ',', '.') : '-' }}</td>
                    <td>{{ $r['H'] !== null ? 'Rp' . number_format($r['H'], 2, ',', '.') : '-' }}</td>
                    <td>{{ $r['nilai'] !== null ? $r['nilai'] . ' unit' : '-' }}</td>
                    <td>
                        @if ($r['status'] === 'ok')
                            Dihitung
                        @elseif ($r['status'] === 'menunggu data')
                            Menunggu data
                        @else
                            Parameter belum lengkap
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Belum ada data barang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
