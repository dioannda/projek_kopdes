<div class="form-group">
    <label for="nama">Nama</label>
    <input id="nama" type="text" name="nama" value="{{ old('nama', $pengguna->nama ?? '') }}">
    @error('nama')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="username">Username</label>
    <input id="username" type="text" name="username" value="{{ old('username', $pengguna->username ?? '') }}">
    @error('username')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="peran">Peran</label>
    <select id="peran" name="peran" style="width:100%;">
        <option value="">-- Pilih peran --</option>
        @foreach (['kasir', 'gudang', 'manajer'] as $peran)
            <option value="{{ $peran }}" {{ old('peran', $pengguna->peran ?? '') === $peran ? 'selected' : '' }}>{{ $peran }}</option>
        @endforeach
    </select>
    @error('peran')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="password">Password</label>
    <input id="password" type="password" name="password">
    @if (isset($pengguna))
        <p style="font-size:13px; margin:4px 0 0;">Kosongkan jika password tidak diubah.</p>
    @endif
    @error('password')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>
