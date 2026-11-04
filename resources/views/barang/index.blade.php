@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    <h2 class="text-2xl font-bold">Data Barang</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Daftar barang koperasi beserta stok, batas minimum, dan
        statusnya.</p>

    @if (session('sukses'))
        <div
            class="bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('sukses') }}</div>
    @endif
    @if (session('error'))
        <div
            class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('error') }}</div>
    @endif
    <form method="GET" action="{{ route('barang.index') }}" class="flex gap-2 mb-4 flex-wrap items-center print:hidden">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama/kode..."
            class="flex-1 min-w-[180px] px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
        <select name="kategori"
            class="px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
            <option value="">Semua kategori</option>
            @foreach ($daftarKategori as $kategori)
                <option value="{{ $kategori }}" {{ request('kategori') === $kategori ? 'selected' : '' }}>
                    {{ $kategori }}</option>
            @endforeach
        </select>
        <select name="status"
            class="px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
            <option value="">Semua status</option>
            <option value="menipis" {{ request('status') === 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
            <option value="kosong" {{ request('status') === 'kosong' ? 'selected' : '' }}>Stok Kosong</option>
            <option value="kedaluwarsa" {{ request('status') === 'kedaluwarsa' ? 'selected' : '' }}>Mendekati Kedaluwarsa
            </option>
        </select>
        <button type="submit"
            class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Cari</button>
        <a href="{{ route('barang.index') }}"
            class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Reset</a>
    </form>

    <p class="mb-4 print:hidden"><a href="{{ route('barang.create') }}"
            class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">+
            Tambah Barang</a></p>

    <div
        class="overflow-x-auto bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Kode</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Nama</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Kategori</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Harga Jual</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Harga Beli</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Stok</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Min.</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Kedaluwarsa</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Status</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barang as $b)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->kode }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->nama }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->kategori }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            Rp{{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->stok }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $b->stok_minimum }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $b->tanggal_kedaluarsa ? $b->tanggal_kedaluarsa->format('d-m-Y') : '-' }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            @php
                                $statusBarang = [];
                                if ($b->cekStok() === 'kosong') {
                                    $statusBarang[] = 'Stok Kosong';
                                } elseif ($b->cekStok() === 'menipis') {
                                    $statusBarang[] = 'Stok Menipis';
                                }
                                if ($b->tanggal_kedaluarsa !== null && $b->cekKedaluarsa()) {
                                    $statusBarang[] = $b->tanggal_kedaluarsa->lt(today())
                                        ? 'Sudah kedaluwarsa'
                                        : 'Mendekati kedaluwarsa';
                                }
                            @endphp
                            {{ $statusBarang ? implode(', ', $statusBarang) : 'Aman' }}
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 whitespace-nowrap">
                            <a href="{{ route('barang.edit', $b) }}"
                                class="inline-block px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm rounded-md transition">Ubah</a>
                            <form method="POST" action="{{ route('barang.destroy', $b) }}" class="inline"
                                data-konfirmasi="Hapus barang {{ $b->nama }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition">Hapus</button>
                            </form>
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

    <div class="mt-4">
        {{ $barang->links() }}
    </div>
@endsection
