@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Tambah Barang</h2>
        <form method="POST" action="{{ route('barang.store') }}">
            @csrf
            @include('barang._form')
            <button type="submit" class="btn">Simpan</button>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
