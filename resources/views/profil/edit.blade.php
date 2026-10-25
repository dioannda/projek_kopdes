@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
    <h2 style="margin-top:0;">Ubah Password</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('profil.update') }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="password_lama">Password Lama</label>
                <input id="password_lama" type="password" name="password_lama">
                @error('password_lama')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_baru">Password Baru (minimal 8 karakter)</label>
                <input id="password_baru" type="password" name="password_baru">
                @error('password_baru')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_baru_confirmation">Konfirmasi Password Baru</label>
                <input id="password_baru_confirmation" type="password" name="password_baru_confirmation">
            </div>
            <button type="submit" class="btn">Simpan Password</button>
        </form>
    </div>
@endsection
