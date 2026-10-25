<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SI Persediaan dan Transaksi KDMP</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f4f6f4; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .box { background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 32px; width: 100%; max-width: 400px; }
        h1 { font-size: 20px; margin: 0 0 4px; color: #1b6b3a; text-align: center; }
        p.sub { text-align: center; color: #666; font-size: 14px; margin: 0 0 24px; }
        label { display: block; font-size: 14px; margin-bottom: 4px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; }
        button { width: 100%; padding: 11px; background: #1b6b3a; color: #fff; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background: #155a2f; }
        .error { background: #fdecea; border: 1px solid #f5c2bd; color: #a33; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .field-error { color: #a33; font-size: 13px; margin-top: -12px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>SI Persediaan dan Transaksi Koperasi Desa Merah Putih</h1>
        <p class="sub">Silakan masuk dengan akun Anda</p>

        @if ($errors->has('login'))
            <div class="error">{{ $errors->first('login') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" autofocus>
            @error('username')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password">
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <button type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>
