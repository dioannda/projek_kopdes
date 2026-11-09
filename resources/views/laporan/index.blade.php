@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <h2 class="text-2xl font-bold">Laporan Penjualan</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Ringkasan dan rincian penjualan pada rentang tanggal yang
        dipilih.</p>
    <form method="GET" action="{{ route('laporan.index') }}" class="flex gap-2 mb-4 flex-wrap items-end print:hidden">
        <div class="flex-1 min-w-[180px]">
            <label for="mulai" class="block text-sm font-bold mb-1">Dari Tanggal</label>
            <input id="mulai" type="date" name="mulai" value="{{ $mulai }}"
                class="w-full px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
        </div>
        <div class="flex-1 min-w-[180px]">
            <label for="sampai" class="block text-sm font-bold mb-1">Sampai Tanggal</label>
            <input id="sampai" type="date" name="sampai" value="{{ $sampai }}"
                class="w-full px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
        </div>
        <div class="flex-1 min-w-[180px]">
            <label for="kasir" class="block text-sm font-bold mb-1">Kasir</label>
            <select id="kasir" name="kasir"
                class="w-full px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                <option value="0" {{ $kasirDipilih === 0 ? 'selected' : '' }}>Semua Kasir</option>
                @foreach ($kasirList as $k)
                    <option value="{{ $k->id }}" {{ $kasirDipilih === $k->id ? 'selected' : '' }}>{{ $k->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label for="shift" class="block text-sm font-bold mb-1">Shift</label>
            <select id="shift" name="shift"
                class="w-full px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                <option value="0" {{ $shiftDipilih === 0 ? 'selected' : '' }}>Semua Shift</option>
                <option value="1" {{ $shiftDipilih === 1 ? 'selected' : '' }}>Shift 1</option>
                <option value="2" {{ $shiftDipilih === 2 ? 'selected' : '' }}>Shift 2</option>
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label for="periode" class="block text-sm font-bold mb-1">Periode Rekap</label>
            <select id="periode" name="periode"
                class="w-full px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @foreach ($periodeList as $nilai => $label)
                    <option value="{{ $nilai }}" {{ $periode === $nilai ? 'selected' : '' }}>{{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Tampilkan</button>
            <a href="{{ route('laporan.index') }}"
                class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Reset</a>
        </div>
    </form>

    <div class="flex gap-4 mb-4 flex-wrap">
        <div
            class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 flex-1 min-w-[180px] hover:-translate-y-0.5 hover:shadow-md transition">
            <strong>Jumlah Transaksi</strong>
            <p class="text-xl mt-2">{{ $totalTransaksi }}</p>
        </div>
        <div
            class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 flex-1 min-w-[180px] hover:-translate-y-0.5 hover:shadow-md transition">
            <strong>Total Pendapatan</strong>
            <p class="text-xl mt-2">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
    </div>

    <h3 class="text-lg font-bold mb-3 mt-6">Rekapitulasi {{ $periodeList[$periode] }}</h3>
    <div
        class="overflow-x-auto bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm mb-4">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Periode</th>
                    <th
                        class="px-3 py-2.5 text-right font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Jumlah Transaksi</th>
                    <th
                        class="px-3 py-2.5 text-right font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Tunai</th>
                    <th
                        class="px-3 py-2.5 text-right font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        QRIS</th>
                    <th
                        class="px-3 py-2.5 text-right font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap as $r)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 whitespace-nowrap">
                            {{ $r['label'] }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">
                            {{ $r['jumlah_transaksi'] }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">
                            Rp{{ number_format($r['total_tunai'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">
                            Rp{{ number_format($r['total_qris'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">
                            Rp{{ number_format($r['total_pendapatan'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Tidak ada transaksi pada rentang ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="font-bold bg-red-50 dark:bg-stone-800">
                    <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">Total</td>
                    <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">{{ $totalTransaksi }}
                    </td>
                    <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">
                        Rp{{ number_format($rekap->sum('total_tunai'), 0, ',', '.') }}</td>
                    <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">
                        Rp{{ number_format($rekap->sum('total_qris'), 0, ',', '.') }}</td>
                    <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-right">
                        Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div
        class="overflow-x-auto bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Tanggal</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Kasir</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Shift</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Jumlah Item</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Total</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $t)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->tanggal->format('d-m-Y H:i') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $t->kasir->nama }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->labelShift() }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->detailTransaksi->sum('jumlah') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->total, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800"><a
                                href="{{ route('transaksi.show', $t) }}"
                                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Lihat
                                Struk</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Tidak ada transaksi pada rentang tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transaksi->links() }}
    </div>

    <div class="mt-4 print:hidden">
        <a href="{{ route('laporan.cetak', ['mulai' => $mulai, 'sampai' => $sampai, 'kasir' => $kasirDipilih, 'shift' => $shiftDipilih, 'periode' => $periode]) }}"
            target="_blank"
            class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Cetak
            Laporan Shift</a>
    </div>

    <h3 class="text-lg font-bold mb-3 mt-6">Stok Terkini</h3>
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
                        Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stok as $b)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->nama }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->stok }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->stok_minimum }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
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
                        <td colspan="4" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Belum ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
