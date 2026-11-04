@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
    <h2 class="text-2xl font-bold">Ubah Password</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Ganti password akun Anda sendiri.</p>

    @if (session('sukses'))
        <div
            class="bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('sukses') }}</div>
    @endif

    <div
        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
        <form method="POST" action="{{ route('profil.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="password_lama" class="block text-sm font-bold mb-1">Password Lama</label>
                <input id="password_lama" type="password" name="password_lama"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('password_lama')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password_baru" class="block text-sm font-bold mb-1">Password Baru (minimal 8 karakter)</label>
                <input id="password_baru" type="password" name="password_baru"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                @error('password_baru')
                    <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password_baru_confirmation" class="block text-sm font-bold mb-1">Konfirmasi Password
                    Baru</label>
                <input id="password_baru_confirmation" type="password" name="password_baru_confirmation"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
            </div>
            <button type="submit"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Simpan
                Password</button>
        </form>
    </div>
@endsection
