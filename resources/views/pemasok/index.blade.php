@extends('layouts.app')

@section('title', 'Data Pemasok')

@section('content')
    <h2 class="text-2xl font-bold">Data Pemasok</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Daftar pemasok koperasi. Pemasok juga dapat masuk lewat impor data (sheet Data Pemasok).</p>

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
        <h3 class="text-lg font-bold mb-3">Tambah Pemasok</h3>
        <form method="POST" action="{{ route('pemasok.store') }}">
            @csrf
            <div class="mb-3">
                <label for="nama" class="block text-sm font-bold mb-1">Nama Pemasok</label>
                <input id="nama" type="text" name="nama" value="{{ old('nama') }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('nama')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="telepon" class="block text-sm font-bold mb-1">Telepon</label>
                <input id="telepon" type="text" name="telepon" value="{{ old('telepon') }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('telepon')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="alamat" class="block text-sm font-bold mb-1">Alamat</label>
                <input id="alamat" type="text" name="alamat" value="{{ old('alamat') }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('alamat')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="keterangan" class="block text-sm font-bold mb-1">Keterangan</label>
                <input id="keterangan" type="text" name="keterangan" value="{{ old('keterangan') }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('keterangan')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Simpan</button>
        </form>
    </div>

    <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 mt-4">
        <h3 class="text-lg font-bold mb-3">Daftar Pemasok</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Nama</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Telepon</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Alamat</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Keterangan</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemasok as $p)
                        <tr class="border-b border-stone-100 dark:border-stone-800">
                            <td class="px-3 py-2.5">{{ $p->nama }}</td>
                            <td class="px-3 py-2.5">{{ $p->telepon ?? '-' }}</td>
                            <td class="px-3 py-2.5">{{ $p->alamat ?? '-' }}</td>
                            <td class="px-3 py-2.5">{{ $p->keterangan ?? '-' }}</td>
                            <td class="px-3 py-2.5">
                                <form method="POST" action="{{ route('pemasok.destroy', $p) }}" class="inline"
                                    data-konfirmasi="Hapus pemasok {{ $p->nama }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-stone-500 dark:text-stone-400">Belum ada
                                pemasok. Tambahkan lewat form di atas atau impor data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pemasok->links('vendor.pagination.kopdes') }}</div>
    </div>
@endsection
