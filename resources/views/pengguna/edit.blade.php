@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')
    <div class="card">
        <h2 class="page-judul">Ubah Pengguna: {{ $pengguna->nama }}</h2>
        <p class="page-subjudul">Perbarui data dan peran pengguna.</p>
        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
            @csrf
            @method('PUT')
            @include('pengguna._form')
            <button type="submit" class="btn">Perbarui</button>
            <a href="{{ route('pengaturan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
