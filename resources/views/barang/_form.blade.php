<div class="mb-3">
    <label for="kode" class="block text-sm font-bold mb-1">Kode</label>
    <input id="kode" type="text" name="kode" value="{{ old('kode', $barang->kode ?? '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('kode')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="nama" class="block text-sm font-bold mb-1">Nama</label>
    <input id="nama" type="text" name="nama" value="{{ old('nama', $barang->nama ?? '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('nama')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="kategori" class="block text-sm font-bold mb-1">Kategori</label>
    <input id="kategori" type="text" name="kategori" value="{{ old('kategori', $barang->kategori ?? '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('kategori')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="harga_beli" class="block text-sm font-bold mb-1">Harga Beli (Rp)</label>
    <input id="harga_beli" type="number" step="0.01" min="0" name="harga_beli"
        value="{{ old('harga_beli', $barang->harga_beli ?? '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('harga_beli')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="harga_jual" class="block text-sm font-bold mb-1">Harga Jual (Rp)</label>
    <input id="harga_jual" type="number" step="0.01" min="0" name="harga_jual"
        value="{{ old('harga_jual', $barang->harga_jual ?? '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('harga_jual')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="stok" class="block text-sm font-bold mb-1">Stok</label>
    <input id="stok" type="number" min="0" name="stok" value="{{ old('stok', $barang->stok ?? '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('stok')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="stok_minimum" class="block text-sm font-bold mb-1">Stok Minimum (batas pemicu notifikasi stok
        menipis)</label>
    <input id="stok_minimum" type="number" min="0" name="stok_minimum"
        value="{{ old('stok_minimum', $barang->stok_minimum ?? '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('stok_minimum')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="tanggal_kedaluarsa" class="block text-sm font-bold mb-1">Tanggal Kedaluwarsa (kosongkan jika tidak
        ada)</label>
    <input id="tanggal_kedaluarsa" type="date" name="tanggal_kedaluarsa"
        value="{{ old('tanggal_kedaluarsa', isset($barang) && $barang->tanggal_kedaluarsa ? $barang->tanggal_kedaluarsa->format('Y-m-d') : '') }}"
        class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('tanggal_kedaluarsa')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>
