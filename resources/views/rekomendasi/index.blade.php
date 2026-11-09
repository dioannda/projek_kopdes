@extends('layouts.app')

@section('title', 'Rekomendasi EOQ')

@section('content')
    <h2 class="text-2xl font-bold">Rekomendasi EOQ</h2>

    <p class="text-sm text-stone-600 dark:text-stone-300 mt-2">
        Rumus EOQ: EOQ = √(2 × D × S ÷ H). D = total penjualan 30 hari terakhir (unit);
        S = biaya pemesanan per pesanan (Rp); H = biaya simpan per unit = harga beli × tarif H
        (Rp/unit). S dan tarif H diatur Manajer di halaman Pengaturan. Setiap baris di bawah
        menunjukkan substitusi angkanya, dan hasilnya dibulatkan ke unit terdekat. Barang tanpa
        penjualan 30 hari terakhir berstatus "Menunggu data" dan tidak diberi angka rekomendasi.
    </p>

    <p class="mt-4 mb-4"><strong>Total nilai pemesanan rekomendasi (barang berstatus Dihitung):
            Rp{{ number_format($totalNilai, 0, ',', '.') }}</strong></p>

    <div
        class="overflow-x-auto bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Barang</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Stok</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Min.</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        D (30 hari)</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        S</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        H / unit</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        EOQ</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Perhitungan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Status</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekomendasi as $r)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $r['barang']->nama }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $r['barang']->stok }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $r['barang']->stok_minimum }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $r['D'] }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $r['S'] !== null ? 'Rp' . number_format($r['S'], 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $r['H'] !== null ? 'Rp' . number_format($r['H'], 2, ',', '.') : '-' }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $r['nilai'] !== null ? $r['nilai'] . ' unit' : '-' }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 whitespace-nowrap">
                            @if ($r['status'] === 'ok')
                                √(2 × {{ $r['D'] }} × {{ number_format($r['S'], 0, ',', '.') }} ÷
                                {{ number_format($r['H'], 0, ',', '.') }})
                                = √{{ number_format((2 * $r['D'] * $r['S']) / $r['H'], 2, ',', '.') }}
                                ≈ {{ $r['nilai'] }} unit
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            @if ($r['status'] === 'ok')
                                Dihitung
                            @elseif ($r['status'] === 'menunggu data')
                                Menunggu data
                            @else
                                Parameter belum lengkap
                            @endif
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            @if (Auth::user()->peran === 'gudang')
                                <a href="{{ route('barang-masuk.index', ['barang_id' => $r['barang']->id]) }}"
                                    class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Catat
                                    Masuk</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Belum ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
