@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <h2 class="text-2xl font-bold">Riwayat Transaksi</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Daftar transaksi penjualan yang pernah dicatat.</p>

    @if (session('sukses'))
        <div
            class="bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('sukses') }}</div>
    @endif

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
                        Jumlah Item</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Total</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Metode</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Shift</th>
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
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->detailTransaksi->sum('jumlah') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($t->total, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $t->labelShift() }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 whitespace-nowrap">
                            <a href="{{ route('transaksi.show', $t) }}"
                                class="inline-block px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm rounded-md transition">Lihat
                                Struk</a>
                            <form method="POST" action="{{ route('transaksi.batalkan', $t) }}" class="inline"
                                data-konfirmasi="Batalkan transaksi ini? Stok akan dikembalikan.">
                                @csrf
                                <button type="submit"
                                    class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition">Batalkan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transaksi->links() }}
    </div>
@endsection
