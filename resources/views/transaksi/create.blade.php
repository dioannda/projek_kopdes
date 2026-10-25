@extends('layouts.app')

@section('title', 'Transaksi Penjualan (POS)')

@section('content')
    <h2 style="margin-top:0;">Transaksi Penjualan (POS)</h2>

    @if (session('sukses'))
        <div class="alert">{{ session('sukses') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('transaksi.store') }}" id="form-pos">
        @csrf
        <div class="card">
            <h3 style="margin-top:0;">Pilih Barang</h3>
            <div style="margin-bottom:8px;">
                <input type="text" id="cari-barang" placeholder="Cari barang..." style="width:100%;">
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <select id="pilih-barang" style="flex:1; min-width:220px;">
                    <option value="">-- Pilih barang --</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->id }}" data-nama="{{ $b->nama }}" data-harga="{{ $b->harga_jual }}" data-stok="{{ $b->stok }}">
                            {{ $b->nama }} — Rp{{ number_format($b->harga_jual, 0, ',', '.') }} (stok: {{ $b->stok }})
                        </option>
                    @endforeach
                </select>
                <input type="number" id="jumlah-barang" min="1" value="1" style="width:90px;">
                <button type="button" class="btn" id="tambah-barang">Tambah</button>
            </div>
        </div>

        <div class="card" style="margin-top:16px;">
            <h3 style="margin-top:0;">Keranjang</h3>
            <table class="data">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="keranjang">
                    @if (old('items'))
                        @foreach (old('items') as $i => $item)
                            @php $b = $barang->firstWhere('id', $item['barang_id']); @endphp
                            @if ($b)
                                <tr data-id="{{ $b->id }}" data-harga="{{ $b->harga_jual }}">
                                    <td>{{ $b->nama }}</td>
                                    <td>Rp{{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="hidden" name="items[{{ $i }}][barang_id]" value="{{ $b->id }}">
                                        <input type="number" name="items[{{ $i }}][jumlah]" value="{{ $item['jumlah'] }}" min="1" class="input-jumlah" style="width:80px;">
                                    </td>
                                    <td class="subtotal">Rp{{ number_format($b->harga_jual * $item['jumlah'], 0, ',', '.') }}</td>
                                    <td><button type="button" class="btn btn-danger hapus-baris">Hapus</button></td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
            <p style="font-size:18px;"><strong>Total: <span id="total">Rp0</span></strong></p>
        </div>

        <div class="card" style="margin-top:16px;">
            <h3 style="margin-top:0;">Pembayaran</h3>
            <div class="form-group">
                <label for="bayar">Uang Bayar (Rp)</label>
                <input id="bayar" type="number" step="0.01" min="0" name="bayar" value="{{ old('bayar') }}">
            </div>
            <p style="font-size:18px;"><strong>Kembalian: <span id="kembalian">Rp0</span></strong></p>
            <button type="submit" class="btn">Proses Pembayaran</button>
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
            const bayar = parseFloat(bayarInput.value) || 0;
            const kembali = bayar - total;
            kembalianEl.textContent = rupiah(kembali < 0 ? 0 : kembali);
            return total;
        }

        document.getElementById('cari-barang').addEventListener('input', (e) => {
            const kata = e.target.value.toLowerCase();
            const select = document.getElementById('pilih-barang');
            select.querySelectorAll('option').forEach(opsi => {
                if (!opsi.value) { return; }
                opsi.hidden = kata !== '' && !opsi.textContent.toLowerCase().includes(kata);
            });
        });

        document.getElementById('tambah-barang').addEventListener('click', () => {
            const select = document.getElementById('pilih-barang');
            const opsi = select.options[select.selectedIndex];
            if (!select.value) { alert('Pilih barang dulu.'); return; }
            const id = select.value;
            const nama = opsi.dataset.nama;
            const harga = parseFloat(opsi.dataset.harga);
            const stok = parseInt(opsi.dataset.stok);
            let jumlah = parseInt(document.getElementById('jumlah-barang').value) || 0;
            if (jumlah < 1) { alert('Jumlah minimal 1.'); return; }
            const barisLama = keranjang.querySelector('tr[data-id="' + id + '"]');
            if (barisLama) {
                const inputJumlah = barisLama.querySelector('.input-jumlah');
                const jumlahBaru = (parseInt(inputJumlah.value) || 0) + jumlah;
                if (jumlahBaru > stok) { alert('Jumlah melebihi stok (' + stok + ').'); return; }
                inputJumlah.value = jumlahBaru;
            } else {
                if (jumlah > stok) { alert('Jumlah melebihi stok (' + stok + ').'); return; }
                const tr = document.createElement('tr');
                tr.dataset.id = id;
                tr.dataset.harga = harga;
                tr.innerHTML = '<td>' + nama + '</td>'
                    + '<td>' + rupiah(harga) + '</td>'
                    + '<td><input type="hidden" name="items[' + indeks + '][barang_id]" value="' + id + '">'
                    + '<input type="number" name="items[' + indeks + '][jumlah]" value="' + jumlah + '" min="1" class="input-jumlah" style="width:80px;"></td>'
                    + '<td class="subtotal"></td>'
                    + '<td><button type="button" class="btn btn-danger hapus-baris">Hapus</button></td>';
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
            if (e.target.classList.contains('input-jumlah')) { hitung(); }
        });
        bayarInput.addEventListener('input', hitung);
        document.getElementById('form-pos').addEventListener('submit', (e) => {
            if (keranjang.querySelectorAll('tr').length === 0) {
                e.preventDefault();
                alert('Keranjang masih kosong.');
            }
        });
        hitung();
    </script>
@endsection
