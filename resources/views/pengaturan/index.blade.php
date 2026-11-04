@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <h2 class="text-2xl font-bold">Pengaturan</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Parameter EOQ dan pengelolaan pengguna (khusus manajer).</p>
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

    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <h3 class="text-lg font-bold mb-3">Parameter EOQ</h3>
        <form method="POST" action="{{ route('pengaturan.updateParameter') }}">
            @csrf
            <div class="mb-3">
                <label for="S" class="block text-sm font-bold mb-1">Biaya Pemesanan (S) per pesanan (Rp)</label>
                <input id="S" type="number" step="0.01" min="0" name="S"
                    value="{{ old('S', $nilaiS) }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('S')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="H" class="block text-sm font-bold mb-1">Tarif Penyimpanan (H) per bulan</label>
                <input id="H" type="number" step="0.01" min="0" max="1" name="H"
                    value="{{ old('H', $tarifH) }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                <p class="text-[13px] text-stone-500 dark:text-stone-400 mt-1">Diisi sebagai desimal, contoh 0,02 = 2% dari
                    harga beli per
                    bulan. H per unit = harga beli x tarif ini.</p>
                @error('H')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Simpan
                Parameter</button>
        </form>
    </div>

    <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 mt-4">
        <h3 class="text-lg font-bold mb-3">Kelola Pengguna</h3>
        <p class="mb-4"><a href="{{ route('pengguna.create') }}"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">+
                Tambah Pengguna</a></p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Nama</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Username</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Peran</th>
                        <th
                            class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengguna as $p)
                        <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $p->nama }}</td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $p->username }}</td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $p->peran }}</td>
                            <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 whitespace-nowrap">
                                <a href="{{ route('pengguna.edit', $p) }}"
                                    class="inline-block px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm rounded-md transition">Ubah</a>
                                <form method="POST" action="{{ route('pengguna.destroy', $p) }}" class="inline"
                                    data-konfirmasi="Hapus pengguna {{ $p->nama }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">Belum ada
                                pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
