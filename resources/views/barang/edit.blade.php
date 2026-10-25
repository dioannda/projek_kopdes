@extends('layouts.app')

@section('title', 'Ubah Barang')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Ubah Barang: {{ $barang->nama }}</h2>
        <form method="POST" action="{{ route('barang.update', $barang) }}">
            @csrf
            @method('PUT')
            @include('barang._form')
            <button type="submit" class="btn">Perbarui</button>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
