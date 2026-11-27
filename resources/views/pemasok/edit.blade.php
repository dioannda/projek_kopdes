@extends('layouts.app')

@section('title', 'Ubah Pemasok')

@section('content')
    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <h2 class="text-2xl font-bold">Ubah Pemasok: {{ $pemasok->nama }}</h2>
        <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Perbarui nama, telepon, alamat, dan keterangan pemasok.
            Riwayat pembelian yang sudah terhubung dengan pemasok ini tidak berubah.</p>
        <form method="POST" action="{{ route('pemasok.update', $pemasok) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nama" class="block text-sm font-bold mb-1">Nama Pemasok</label>
                <input id="nama" type="text" name="nama" value="{{ old('nama', $pemasok->nama) }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('nama')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="telepon" class="block text-sm font-bold mb-1">Telepon</label>
                <input id="telepon" type="text" name="telepon" value="{{ old('telepon', $pemasok->telepon) }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('telepon')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="alamat" class="block text-sm font-bold mb-1">Alamat</label>
                <input id="alamat" type="text" name="alamat" value="{{ old('alamat', $pemasok->alamat) }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('alamat')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="keterangan" class="block text-sm font-bold mb-1">Keterangan</label>
                <input id="keterangan" type="text" name="keterangan" value="{{ old('keterangan', $pemasok->keterangan) }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('keterangan')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Perbarui</button>
            <a href="{{ route('pemasok.index') }}"
                class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Batal</a>
        </form>
    </div>
@endsection
