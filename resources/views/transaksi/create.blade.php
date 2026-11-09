@extends('layouts.app')

@section('title', 'Transaksi Penjualan (POS)')

@section('content')
    <h2 class="text-2xl font-bold">Transaksi Penjualan (POS)</h2>
    <p class="text-stone-500 dark:text-stone-400 text-sm mb-5">Pilih barang, atur keranjang, dan selesaikan pembayaran.</p>

    @if (session('sukses'))
        <div
            class="bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('sukses') }}</div>
    @endif
    @if (session('error'))
        <div
            class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div
            class="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('transaksi.store') }}" id="form-pos">
        @csrf
        <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5">
            <h3 class="text-lg font-bold mb-3">Pilih Barang</h3>
            <div class="mb-2">
                <input type="text" id="cari-barang" placeholder="Cari barang..."
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
            </div>
            <div class="flex gap-2 flex-wrap">
                <select id="pilih-barang"
                    class="flex-1 min-w-[220px] px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                    <option value="">-- Pilih barang --</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->id }}" data-nama="{{ $b->nama }}"
                            data-harga="{{ $b->harga_jual }}" data-stok="{{ $b->stok }}">
                            {{ $b->nama }} — Rp{{ number_format($b->harga_jual, 0, ',', '.') }} (stok:
                            {{ $b->stok }})
                        </option>
                    @endforeach
                </select>
                <input type="number" id="jumlah-barang" min="1" value="1"
                    class="w-[90px] px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                <button type="button"
                    class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition"
                    id="tambah-barang">Tambah</button>
            </div>
        </div>

        <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 mt-4">
            <h3 class="text-lg font-bold mb-3">Keranjang</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th
                                class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                                Barang</th>
                            <th
                                class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                                Harga</th>
                            <th
                                class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                                Jumlah</th>
                            <th
                                class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                                Subtotal</th>
                            <th
                                class="px-3 py-2.5 text-left font-bold bg-red-50 dark:bg-stone-800 border-b border-stone-200 dark:border-stone-700">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="keranjang">
                        @if (old('items'))
                            @foreach (old('items') as $i => $item)
                                @php $b = $barang->firstWhere('id', $item['barang_id']); @endphp
                                @if ($b)
                                    <tr data-id="{{ $b->id }}" data-harga="{{ $b->harga_jual }}"
                                        class="hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                            {{ $b->nama }}</td>
                                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                            Rp{{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                            <input type="hidden" name="items[{{ $i }}][barang_id]"
                                                value="{{ $b->id }}">
                                            <input type="number" name="items[{{ $i }}][jumlah]"
                                                value="{{ $item['jumlah'] }}" min="1"
                                                class="input-jumlah w-[80px] px-2 py-1.5 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700">
                                        </td>
                                        <td class="subtotal px-3 py-2 border-t border-stone-200 dark:border-stone-800">
                                            Rp{{ number_format($b->harga_jual * $item['jumlah'], 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800"><button
                                                type="button"
                                                class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition hapus-baris">Hapus</button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <p class="text-lg mt-4"><strong>Total: <span id="total">Rp0</span></strong></p>
        </div>

        <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 mt-4">
            <h3 class="text-lg font-bold mb-3">Pembayaran</h3>
            <div class="mb-3">
                <span class="block text-sm font-bold mb-1">Metode Pembayaran</span>
                <label class="inline-flex items-center gap-2 mr-4">
                    <input type="radio" name="metode_pembayaran" value="cash"
                        {{ old('metode_pembayaran', 'cash') === 'cash' ? 'checked' : '' }} class="accent-red-700">
                    <span class="text-sm"> Tunai (Cash)</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="metode_pembayaran" value="qris"
                        {{ old('metode_pembayaran') === 'qris' ? 'checked' : '' }} class="accent-red-700">
                    <span class="text-sm"> QRIS</span>
                </label>
            </div>
            <div class="mb-3" id="blok-tunai">
                <label for="bayar" class="block text-sm font-bold mb-1">Uang Bayar (Rp)</label>
                <input id="bayar" type="number" step="0.01" min="0" name="bayar"
                    value="{{ old('bayar') }}"
                    class="w-full px-3 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
            </div>
            <div id="blok-qris" class="mb-3 hidden">
                <p class="text-sm font-bold mb-2">Pindai QRIS Koperasi</p>
                <img src="{{ asset('images/qris-statis.png') }}" alt="QRIS Koperasi Desa Merah Putih"
                    class="w-56 h-56 object-contain border border-stone-200 dark:border-stone-700 rounded-lg bg-white p-2">
                <p class="text-sm text-stone-500 dark:text-stone-400 mt-2">Pindai QR di atas dengan e-wallet atau m-banking,
                    masukkan nominal sesuai total belanja, lalu tunjukkan bukti pembayaran ke kasir.</p>
                <p class="text-lg mt-2"><strong>Nominal yang dibayar: <span id="total-qris">Rp0</span></strong></p>
            </div>
            <p class="text-lg" id="baris-kembalian"><strong>Kembalian: <span id="kembalian">Rp0</span></strong></p>
            <button type="submit" id="tombol-bayar"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition mt-4">Proses
                Pembayaran</button>
        </div>
    </form>

    <script>
        const keranjang = document.getElementById('keranjang');
        const totalEl = document.getElementById('total');
        const kembalianEl = document.getElementById('kembalian');
        const bayarInput = document.getElementById('bayar');
        let indeks = keranjang.querySelectorAll('tr').length;

        function rupiah(n) {
            return 'Rp' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function hitung() {
            let total = 0;
            keranjang.querySelectorAll('tr').forEach(row => {
                const harga = parseFloat(row.dataset.harga);
                const jumlah = parseInt(row.querySelector('.input-jumlah').value) || 0;
                const subtotal = harga * jumlah;
                row.querySelector('.subtotal').textContent = rupiah(subtotal);
                total += subtotal;
            });
            totalEl.textContent = rupiah(total);
            document.getElementById('total-qris').textContent = rupiah(total);
            document.getElementById('total-qris').textContent = rupiah(total);
            const bayar = parseFloat(bayarInput.value) || 0;
            const kembali = bayar - total;
            kembalianEl.textContent = rupiah(kembali < 0 ? 0 : kembali);
            return total;
        }

        document.getElementById('cari-barang').addEventListener('input', (e) => {
            const kata = e.target.value.toLowerCase();
            const select = document.getElementById('pilih-barang');
            select.querySelectorAll('option').forEach(opsi => {
                if (!opsi.value) {
                    return;
                }
                opsi.hidden = kata !== '' && !opsi.textContent.toLowerCase().includes(kata);
            });
        });

        function beriTahu(pesan) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: pesan,
                    confirmButtonColor: '#b91c1c'
                });
            } else {
                alert(pesan);
            }
        }

        document.getElementById('tambah-barang').addEventListener('click', () => {
            const select = document.getElementById('pilih-barang');
            const opsi = select.options[select.selectedIndex];
            if (!select.value) {
                beriTahu('Pilih barang dulu.');
                return;
            }
            const id = select.value;
            const nama = opsi.dataset.nama;
            const harga = parseFloat(opsi.dataset.harga);
            const stok = parseInt(opsi.dataset.stok);
            let jumlah = parseInt(document.getElementById('jumlah-barang').value) || 0;
            if (jumlah < 1) {
                beriTahu('Jumlah minimal 1.');
                return;
            }
            const barisLama = keranjang.querySelector('tr[data-id="' + id + '"]');
            if (barisLama) {
                const inputJumlah = barisLama.querySelector('.input-jumlah');
                const jumlahBaru = (parseInt(inputJumlah.value) || 0) + jumlah;
                if (jumlahBaru > stok) {
                    beriTahu('Jumlah melebihi stok (' + stok + ').');
                    return;
                }
                inputJumlah.value = jumlahBaru;
            } else {
                if (jumlah > stok) {
                    beriTahu('Jumlah melebihi stok (' + stok + ').');
                    return;
                }
                const tr = document.createElement('tr');
                tr.dataset.id = id;
                tr.dataset.harga = harga;
                tr.className = 'hover:bg-stone-50 dark:hover:bg-stone-800 transition';
                tr.innerHTML = '<td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">' + nama +
                    '</td>' +
                    '<td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800">' + rupiah(harga) +
                    '</td>' +
                    '<td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800"><input type="hidden" name="items[' +
                    indeks + '][barang_id]" value="' + id + '">' +
                    '<input type="number" name="items[' + indeks + '][jumlah]" value="' + jumlah +
                    '" min="1" class="input-jumlah w-[80px] px-2 py-1.5 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700"></td>' +
                    '<td class="subtotal px-3 py-2 border-t border-stone-200 dark:border-stone-800"></td>' +
                    '<td class="px-3 py-2 border-t border-stone-200 dark:border-stone-800"><button type="button" class="inline-block px-3.5 py-2 bg-red-800 hover:bg-red-900 text-white text-sm rounded-md transition hapus-baris">Hapus</button></td>';
                keranjang.appendChild(tr);
                indeks++;
            }
            hitung();
        });

        keranjang.addEventListener('click', (e) => {
            if (e.target.classList.contains('hapus-baris')) {
                e.target.closest('tr').remove();
                hitung();
            }
        });
        keranjang.addEventListener('input', (e) => {
            if (e.target.classList.contains('input-jumlah')) {
                hitung();
            }
        });
        bayarInput.addEventListener('input', hitung);
        document.getElementById('form-pos').addEventListener('submit', (e) => {
            if (keranjang.querySelectorAll('tr').length === 0) {
                e.preventDefault();
                beriTahu('Keranjang masih kosong.');
                const tombolBayar = document.getElementById('tombol-bayar');

                function aturMetode() {
                    const qris = document.querySelector('input[name="metode_pembayaran"]:checked')?.value ===
                        'qris';
                    document.getElementById('blok-tunai').classList.toggle('hidden', qris);
                    document.getElementById('baris-kembalian').classList.toggle('hidden', qris);
                    document.getElementById('blok-qris').classList.toggle('hidden', !qris);
                    tombolBayar.textContent = qris ? 'Konfirmasi Pembayaran QRIS' : 'Proses Pembayaran';
                }
                document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
                    radio.addEventListener('change', aturMetode);
                });
                aturMetode();
            }
        });
        const tombolBayar = document.getElementById('tombol-bayar');

        function aturMetode() {
            const qris = document.querySelector('input[name="metode_pembayaran"]:checked')?.value === 'qris';
            document.getElementById('blok-tunai').classList.toggle('hidden', qris);
            document.getElementById('baris-kembalian').classList.toggle('hidden', qris);
            document.getElementById('blok-qris').classList.toggle('hidden', !qris);
            tombolBayar.textContent = qris ? 'Konfirmasi Pembayaran QRIS' : 'Proses Pembayaran';
        }
        document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
            radio.addEventListener('change', aturMetode);
        });
        aturMetode();
        hitung();
    </script>
@endsection
