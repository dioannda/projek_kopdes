@extends('layouts.app')

@section('title', 'Tutup Buku Shift')

@section('content')
    <h2 class="text-2xl font-bold">Tutup Buku Shift</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Persetujuan penyerahan uang kasir ke manajer per shift.
        Yang menunggu ditampilkan dulu, lalu riwayat yang sudah disetujui.</p>

    <h3 class="text-lg font-bold mb-2">Menunggu Persetujuan ({{ $menunggu->total() }})</h3>
    <div
        class="overflow-x-auto bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm mb-6">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Tanggal</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Shift</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Kasir</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Saldo Awal</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Total Pendapatan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Diserahkan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Sisa</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Keterangan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($menunggu as $t)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->tanggal->format('d-m-Y') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->labelShift() }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $t->kasir->nama }}
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->saldo_awal, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->total_pendapatan, 0, ',', '.') }}<br><span
                                class="text-xs text-stone-500 dark:text-stone-400">Tunai
                                Rp{{ number_format($t->total_tunai, 0, ',', '.') }} · QRIS
                                Rp{{ number_format($t->total_qris, 0, ',', '.') }}</span></td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->jumlah_diserahkan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->saldo_lanjutan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $t->keterangan }}
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            <div class="flex gap-2 flex-wrap">
                                <a href="{{ route('laporan.index', ['mulai' => $t->tanggal->format('Y-m-d'), 'sampai' => $t->tanggal->format('Y-m-d'), 'kasir' => $t->id_kasir, 'shift' => $t->shift]) }}"
                                    class="inline-block px-3 py-1.5 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Lihat
                                    Laporan</a>
                                <form method="POST" action="{{ route('tutup-buku.setujui', $t) }}" class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="inline-block px-3 py-1.5 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Setujui</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Tidak ada tutup buku yang menunggu persetujuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mb-6">
        {{ $menunggu->links() }}
    </div>

    <h3 class="text-lg font-bold mb-2">Riwayat Disetujui</h3>
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
                        Shift</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Kasir</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Saldo Awal</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Total Pendapatan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Diserahkan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Sisa</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Keterangan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Disetujui</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $t)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->tanggal->format('d-m-Y') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->labelShift() }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $t->kasir->nama }}
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->saldo_awal, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->total_pendapatan, 0, ',', '.') }}<br><span
                                class="text-xs text-stone-500 dark:text-stone-400">Tunai
                                Rp{{ number_format($t->total_tunai, 0, ',', '.') }} · QRIS
                                Rp{{ number_format($t->total_qris, 0, ',', '.') }}</span></td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->jumlah_diserahkan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->saldo_lanjutan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $t->keterangan }}
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->manajer->nama ?? '-' }}<br><span
                                class="text-xs text-stone-500 dark:text-stone-400">{{ $t->disetujui_pada ? $t->disetujui_pada->format('d-m-Y H:i') : '' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Belum ada tutup buku yang disetujui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $riwayat->links() }}
    </div>
@endsection
