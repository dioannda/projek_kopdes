<div class="form-group">
    <label for="kode">Kode</label>
    <input id="kode" type="text" name="kode" value="{{ old('kode', $barang->kode ?? '') }}">
    @error('kode')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="nama">Nama</label>
    <input id="nama" type="text" name="nama" value="{{ old('nama', $barang->nama ?? '') }}">
    @error('nama')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="kategori">Kategori</label>
    <input id="kategori" type="text" name="kategori" value="{{ old('kategori', $barang->kategori ?? '') }}">
    @error('kategori')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="harga_beli">Harga Beli (Rp)</label>
    <input id="harga_beli" type="number" step="0.01" min="0" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli ?? '') }}">
    @error('harga_beli')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="harga_jual">Harga Jual (Rp)</label>
    <input id="harga_jual" type="number" step="0.01" min="0" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual ?? '') }}">
    @error('harga_jual')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="stok">Stok</label>
    <input id="stok" type="number" min="0" name="stok" value="{{ old('stok', $barang->stok ?? '') }}">
    @error('stok')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="stok_minimum">Stok Minimum (batas pemicu notifikasi stok menipis)</label>
    <input id="stok_minimum" type="number" min="0" name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum ?? '') }}">
    @error('stok_minimum')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="tanggal_kedaluarsa">Tanggal Kedaluwarsa (kosongkan jika tidak ada)</label>
    <input id="tanggal_kedaluarsa" type="date" name="tanggal_kedaluarsa" value="{{ old('tanggal_kedaluarsa', isset($barang) && $barang->tanggal_kedaluarsa ? $barang->tanggal_kedaluarsa->format('Y-m-d') : '') }}">
    @error('tanggal_kedaluarsa')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>
