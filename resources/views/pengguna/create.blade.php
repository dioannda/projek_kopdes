@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Tambah Pengguna</h2>
        <form method="POST" action="{{ route('pengguna.store') }}">
            @csrf
            @include('pengguna._form')
            <button type="submit" class="btn">Simpan</button>
            <a href="{{ route('pengaturan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
