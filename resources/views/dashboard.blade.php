@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Selamat datang, {{ $pengguna->nama }}</h2>
        <p>Anda login sebagai <strong style="text-transform:capitalize;">{{ $pengguna->peran }}</strong>.</p>
    </div>

    <div style="display:flex; gap:16px; margin-top:16px; flex-wrap:wrap;">
        @foreach ($stats as $label => $nilai)
            <div class="card" style="flex:1; min-width:160px;">
                <strong>{{ $label }}</strong>
                <p style="font-size:20px; margin:8px 0 0;">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>
@endsection
