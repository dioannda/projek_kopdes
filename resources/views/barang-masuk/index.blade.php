@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
    <h2 class="text-2xl font-bold">Barang Masuk</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Catat penerimaan barang dan lihat riwayat barang masuk.</p>

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
    @if ($errors->any())
        <div
            class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ $errors->first() }}</div>
    @endif

    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <h3 class="text-lg font-bold mb-3">Catat Penerimaan</h3>
        <form method="POST" action="{{ route('barang-masuk.store') }}">
            @csrf
            <div class="mb-3">
                <label for="id_barang" class="block text-sm font-bold mb-1">Barang</label>
                <select id="id_barang" name="id_barang"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                    <option value="">-- Pilih barang --</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->id }}"
                            {{ old('id_barang', $barangTerpilih) == $b->id ? 'selected' : '' }}>
                            {{ $b->nama }} (stok: {{ $b->stok }})
                        </option>
                    @endforeach
                </select>
                @error('id_barang')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="jumlah" class="block text-sm font-bold mb-1">Jumlah Masuk</label>
                <input id="jumlah" type="number" min="1" name="jumlah" value="{{ old('jumlah') }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('jumlah')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="tanggal" class="block text-sm font-bold mb-1">Tanggal</label>
                <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('tanggal')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Simpan</button>
        </form>
    </div>

    <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 mt-4">
        <h3 class="text-lg font-bold mb-3">Riwayat Penerimaan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Tanggal</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Barang</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Jumlah</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Dicatat Oleh</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $r)
                        <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                {{ $r->tanggal->format('d-m-Y') }}</td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $r->barang->nama }}
                            </td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $r->jumlah }}</td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $r->pengguna->nama }}
                            </td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                <form method="POST" action="{{ route('barang-masuk.destroy', $r) }}" class="inline"
                                    data-konfirmasi="Hapus riwayat ini? Stok {{ $r->barang->nama }} akan dikembalikan {{ $r->jumlah }}.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">Belum ada
                                riwayat penerimaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $riwayat->links() }}
        </div>
    </div>
@endsection
