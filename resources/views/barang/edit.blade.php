@extends('layouts.app')

@section('title', 'Ubah Barang')

@section('content')
    <div class="card">
        <h2 class="page-judul">Ubah Barang: {{ $barang->nama }}</h2>
        <p class="page-subjudul">Perbarui data barang, stok minimum, dan tanggal kedaluwarsa.</p>
        <form method="POST" action="{{ route('barang.update', $barang) }}">
            @csrf
            @method('PUT')
            @include('barang._form')
            <button type="submit" class="btn">Perbarui</button>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
