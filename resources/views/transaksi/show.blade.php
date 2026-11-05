@extends('layouts.app')

@section('title', 'Struk Penjualan')

@section('content')
    <h2 class="text-2xl font-bold">Struk Penjualan</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Rincian transaksi dan barang yang terjual.</p>

    @if (session('sukses'))
        <div
            class="bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('sukses') }}</div>
    @endif

    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <p class="my-1">Tanggal: {{ $transaksi->tanggal->format('d-m-Y H:i') }}</p>
        <p class="my-1">Kasir: {{ $transaksi->kasir->nama }}</p>
        <p class="my-1">Metode Pembayaran: {{ $transaksi->metode_pembayaran === 'qris' ? 'QRIS' : 'Tunai (Cash)' }}</p>
        <div class="overflow-x-auto mt-3">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Barang</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Harga</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Jumlah</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi->detailTransaksi as $d)
                        <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $d->barang->nama }}
                            </td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                Rp{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $d->jumlah }}</td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                Rp{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-lg mt-4"><strong>Total: Rp{{ number_format($transaksi->total, 0, ',', '.') }}</strong></p>
        <p class="mt-2">Uang Bayar:
            {{ session('bayar') !== null ? 'Rp' . number_format(session('bayar'), 0, ',', '.') : '-' }}</p>
        <p class="mt-1">Kembalian:
            {{ session('kembalian') !== null ? 'Rp' . number_format(session('kembalian'), 0, ',', '.') : '-' }}
        </p>
        <div class="mt-4 flex gap-2 flex-wrap print:hidden">
            <button type="button"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition"
                onclick="window.print()">Cetak Struk</button>
            @if (Auth::user()->peran === 'kasir')
                <a href="{{ route('transaksi.create') }}"
                    class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Transaksi
                    Baru</a>
                <form method="POST" action="{{ route('transaksi.batalkan', $transaksi) }}" class="inline"
                    data-konfirmasi="Batalkan transaksi ini? Stok akan dikembalikan.">
                    @csrf
                    <button type="submit"
                        class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition">Batalkan
                        Transaksi</button>
                </form>
            @endif
        </div>
    </div>

    @if (session('sukses'))
        {{-- Struk baru dari penjualan langsung membuka dialog cetak
             (perilaku POS); struk lama yang dibuka ulang tidak. --}}
        <script>
            window.addEventListener('load', () => window.print());
        </script>
    @endif
@endsection
