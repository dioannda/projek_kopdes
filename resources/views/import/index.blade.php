@extends('layouts.app')

@section('title', 'Import Data')

@section('content')
    <h2 class="text-2xl font-bold">Import Data</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Impor rekap historis koperasi dari file Excel ke basis data.
        Hanya Manajer yang dapat mengimpor.</p>

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
    @if (session('impor_galat'))
        <div
            class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
            <p class="font-bold mb-1">Baris yang dilewati:</p>
            <ul class="list-disc pl-5">
                @foreach (session('impor_galat') as $galat)
                    <li>{{ $galat }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <h3 class="text-lg font-bold mb-3">Unggah File Excel</h3>
        <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="block text-sm font-bold mb-1">File Excel (.xlsx, maksimal 2 MB)</label>
                <input id="file" type="file" name="file" accept=".xlsx"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('file')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Impor</button>
        </form>
    </div>
@endsection
