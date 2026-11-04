<div class="mb-3">
    <label for="nama" class="block text-sm font-bold mb-1">Nama</label>
    <input id="nama" type="text" name="nama" value="{{ old('nama', $pengguna->nama ?? '') }}" class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('nama')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="username" class="block text-sm font-bold mb-1">Username</label>
    <input id="username" type="text" name="username" value="{{ old('username', $pengguna->username ?? '') }}" class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @error('username')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="peran" class="block text-sm font-bold mb-1">Peran</label>
    <select id="peran" name="peran" class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
        <option value="">-- Pilih peran --</option>
        @foreach (['kasir', 'gudang', 'manajer'] as $peran)
            <option value="{{ $peran }}" {{ old('peran', $pengguna->peran ?? '') === $peran ? 'selected' : '' }}>{{ $peran }}</option>
        @endforeach
    </select>
    @error('peran')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="password" class="block text-sm font-bold mb-1">Password</label>
    <input id="password" type="password" name="password" class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
    @if (isset($pengguna))
        <p class="text-[13px] text-stone-500 dark:text-stone-400 mt-1">Kosongkan jika password tidak diubah.</p>
    @endif
    @error('password')
        <div class="text-red-600 dark:text-red-400 text-[13px] mt-1">{{ $message }}</div>
    @enderror
</div>
