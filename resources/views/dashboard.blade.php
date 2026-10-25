@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Selamat datang, {{ $pengguna->nama }}</h2>
        <p>Anda login sebagai <strong style="text-transform:capitalize;">{{ $pengguna->peran }}</strong>.</p>
        <p>Silakan pilih menu di samping. Menu yang bertanda "Segera" akan aktif seiring setiap use case selesai dibangun.</p>
    </div>
@endsection
