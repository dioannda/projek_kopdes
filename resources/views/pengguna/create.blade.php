@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <h2 class="text-2xl font-bold">Tambah Pengguna</h2>
        <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Buat akun pengguna baru beserta perannya.</p>
        <form method="POST" action="{{ route('pengguna.store') }}">
            @csrf
            @include('pengguna._form')
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Simpan</button>
            <a href="{{ route('pengaturan.index') }}"
                class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">Batal</a>
        </form>
    </div>
@endsection
