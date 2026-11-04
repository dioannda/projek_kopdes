@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <h2 class="text-2xl font-bold">Notifikasi</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Pemberitahuan stok menipis, stok kosong, dan mendekati
        kedaluwarsa.</p>

    @if (session('sukses'))
        <div
            class="bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('sukses') }}</div>
    @endif

    <form method="POST" action="{{ route('notifikasi.baca-semua') }}" class="mb-4 print:hidden">
        @csrf
        <button type="submit"
            class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Tandai
            Semua Dibaca</button>
    </form>

    <form method="GET" action="{{ route('notifikasi.index') }}" class="flex gap-2 mb-4 flex-wrap items-center print:hidden">
        <select name="jenis"
            class="px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
            <option value="">Semua jenis</option>
            <option value="stok_kosong" {{ request('jenis') === 'stok_kosong' ? 'selected' : '' }}>Stok Kosong</option>
            <option value="stok_menipis" {{ request('jenis') === 'stok_menipis' ? 'selected' : '' }}>Stok Menipis</option>
            <option value="kedaluwarsa" {{ request('jenis') === 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
        </select>
        <label class="flex items-center gap-1.5 text-sm">
            <input type="checkbox" name="belum_dibaca" value="1" class="accent-red-700"
                {{ request('belum_dibaca') === '1' ? 'checked' : '' }}>
            Hanya yang belum dibaca
        </label>
        <button type="submit"
            class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Tampilkan</button>
        <a href="{{ route('notifikasi.index') }}"
            class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Reset</a>
    </form>

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
                        Barang</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Jenis</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Pesan</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Rekomendasi EOQ</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Status</th>
                    <th
                        class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifikasi as $n)
                    <tr class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $n->tanggal->format('d-m-Y H:i') }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $n->barang->nama }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            @if ($n->jenis === 'stok_kosong')
                                Stok Kosong
                            @elseif ($n->jenis === 'stok_menipis')
                                Stok Menipis
                            @else
                                Kedaluwarsa
                            @endif
                        </td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">{{ $n->pesan }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $n->rekomendasi_jumlah !== null ? $n->rekomendasi_jumlah . ' unit' : '-' }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            {{ $n->status_baca ? 'Dibaca' : 'Belum dibaca' }}</td>
                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                            <a href="{{ route('notifikasi.show', $n) }}"
                                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-2 border-t border-stone-200 dark:border-stone-800 text-center">
                            Belum ada notifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $notifikasi->links() }}
    </div>
@endsection
