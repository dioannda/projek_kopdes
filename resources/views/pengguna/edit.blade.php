@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Ubah Pengguna: {{ $pengguna->nama }}</h2>
        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
            @csrf
            @method('PUT')
            @include('pengguna._form')
            <button type="submit" class="btn">Perbarui</button>
            <a href="{{ route('pengaturan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
