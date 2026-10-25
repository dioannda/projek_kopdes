<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\DetailTransaksi;
use App\Models\Notifikasi;
use App\Models\ParameterSistem;
use App\Models\Pengguna;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KopdesDemoSeeder extends Seeder
{
    /**
     * Data DEMONSTRASI buatan sendiri (bukan data riil koperasi).
     * Tujuannya agar sistem dapat didemonstrasikan end-to-end:
     * transaksi -> pemotongan stok -> rekomendasi EOQ -> notifikasi.
     * Saat sidang, data ini harus dinyatakan sebagai data demo, bukan
     * data penjualan sesungguhnya Koperasi Desa Merah Putih Pulo Lor.
     *
     * Desain data demo:
     * - Transaksi hanya diisi untuk barang sembako, sehingga D terisi
     *   dan rekomendasi EOQ bisa didemonstrasikan.
     * - Barang non-sembako sengaja tanpa transaksi, sehingga cabang
     *   D = 0 ("menunggu data") ikut terdemonstrasikan.
     * - Satu barang stok 0 (Gas LPG 3 kg) untuk demo notifikasi kosong,
     *   satu barang stok di bawah minimum untuk demo notifikasi menipis,
     *   dan telur dengan kedaluwarsa dekat untuk demo peringatan kedaluwarsa.
     */
    public function run(): void
    {
        // 1. Pengguna demo — password semua akun: 'password'
        $manajer = Pengguna::updateOrCreate(
            ['username' => 'manajer'],
            ['nama' => 'Budi Santoso', 'password' => Hash::make('password'), 'peran' => 'manajer']
        );
        $gudang = Pengguna::updateOrCreate(
            ['username' => 'gudang'],
            ['nama' => 'Siti Rahma', 'password' => Hash::make('password'), 'peran' => 'gudang']
        );
        $kasir = Pengguna::updateOrCreate(
            ['username' => 'kasir'],
            ['nama' => 'Ahmad Fauzi', 'password' => Hash::make('password'), 'peran' => 'kasir']
        );

        // 2. Parameter EOQ demo — dalam rentang hasil wawancara:
        //    S Rp150.000-Rp300.000 per pesanan; H 1-3% dari nilai barang per bulan.
        ParameterSistem::updateOrCreate(
            ['nama_parameter' => 'S'],
            ['nilai' => 200000, 'keterangan' => 'Biaya pemesanan per pesanan (Rp) - nilai demo dalam rentang wawancara']
        );
        ParameterSistem::updateOrCreate(
            ['nama_parameter' => 'H'],
            ['nilai' => 0.02, 'keterangan' => 'Tarif penyimpanan per unit per bulan (2% dari harga beli) - nilai demo dalam rentang wawancara']
        );

        // 3. Barang demo (variasi menyerupai dagangan koperasi desa)
        $barang = [];
        foreach ($this->dataBarang() as $data) {
            $barang[$data['kode']] = Barang::updateOrCreate(
                ['kode' => $data['kode']],
                $data
            );
        }

        // 4. Transaksi demo 30 hari terakhir (hanya barang sembako)
        if (Transaksi::count() === 0) {
            $this->seedTransaksi($barang, $kasir->id);
        }

        // 5. Riwayat barang masuk demo
        if (BarangMasuk::count() === 0) {
            BarangMasuk::create([
                'id_barang' => $barang['BRG001']->id,
                'id_pengguna' => $gudang->id,
                'tanggal' => Carbon::now()->subDays(20),
                'jumlah' => 50,
            ]);
            BarangMasuk::create([
                'id_barang' => $barang['BRG004']->id,
                'id_pengguna' => $gudang->id,
                'tanggal' => Carbon::now()->subDays(15),
                'jumlah' => 100,
            ]);
            BarangMasuk::create([
                'id_barang' => $barang['BRG011']->id,
                'id_pengguna' => $gudang->id,
                'tanggal' => Carbon::now()->subDays(10),
                'jumlah' => 200,
            ]);
        }

        // 6. Notifikasi demo (satu per jenis ENUM)
        if (Notifikasi::count() === 0) {
            // Stok menipis: Telur 1 Tray (stok 4 < minimum 5).
            // Rekomendasi dihitung dengan rumus EOQ dari data demo:
            // D = 1, S = 200000, H = 52000 x 0,02 = 1040
            // EOQ = akar(2 x 1 x 200000 / 1040) = 19,6 -> 20.
            $tray = $barang['BRG010'];
            Notifikasi::create([
                'id_barang' => $tray->id,
                'jenis' => 'stok_menipis',
                'pesan' => 'Stok Telur Ayam Ras 1 Tray menipis (sisa 4, minimum 5).',
                'rekomendasi_jumlah' => (int) round(sqrt((2 * 1 * 200000) / (52000 * 0.02))),
                'status_baca' => false,
                'tanggal' => Carbon::now(),
            ]);

            // Stok kosong: Gas LPG 3 kg (stok 0) — tanpa rekomendasi EOQ.
            Notifikasi::create([
                'id_barang' => $barang['BRG017']->id,
                'jenis' => 'stok_kosong',
                'pesan' => 'Stok Gas LPG 3 kg kosong dan tidak dapat ditransaksikan.',
                'rekomendasi_jumlah' => null,
                'status_baca' => false,
                'tanggal' => Carbon::now(),
            ]);

            // Kedaluwarsa: Telur 1 kg mendekati tanggal kedaluwarsa.
            Notifikasi::create([
                'id_barang' => $barang['BRG009']->id,
                'jenis' => 'kedaluwarsa',
                'pesan' => 'Telur Ayam Ras 1 kg mendekati tanggal kedaluwarsa (10 hari lagi).',
                'rekomendasi_jumlah' => null,
                'status_baca' => false,
                'tanggal' => Carbon::now(),
            ]);
        }
    }

    /**
     * Daftar barang demo: [kode, nama, kategori, harga_beli, harga_jual,
     * stok, stok_minimum, hari_sampai_kedaluwarsa (null = tanpa kedaluwarsa)].
     */
    private function dataBarang(): array
    {
        $now = Carbon::now();
        $exp = fn (?int $days) => $days === null ? null : $now->copy()->addDays($days)->toDateString();

        $rows = [
            // Sembako
            ['BRG001', 'Beras Premium 5 kg', 'Beras', 65000, 72000, 120, 20, null],
            ['BRG002', 'Beras Medium 5 kg', 'Beras', 58000, 64000, 85, 20, null],
            ['BRG003', 'Beras Premium 10 kg', 'Beras', 128000, 140000, 40, 10, null],
            ['BRG004', 'Minyak Goreng Kemasan 1 L', 'Minyak Goreng', 16500, 18000, 150, 30, 365],
            ['BRG005', 'Minyak Goreng Kemasan 2 L', 'Minyak Goreng', 32000, 35000, 90, 20, 365],
            ['BRG006', 'Minyak Goreng Curah 1 L', 'Minyak Goreng', 14500, 16000, 60, 15, 180],
            ['BRG007', 'Gula Pasir 1 kg', 'Gula', 16500, 18000, 200, 40, null],
            ['BRG008', 'Gula Pasir 500 g', 'Gula', 9000, 10000, 120, 25, null],
            ['BRG009', 'Telur Ayam Ras 1 kg', 'Telur', 28000, 31000, 45, 10, 10],
            ['BRG010', 'Telur Ayam Ras 1 Tray', 'Telur', 52000, 58000, 4, 5, 10],
            ['BRG011', 'Mi Instan Goreng', 'Mi Instan', 3000, 3500, 300, 50, 120],
            ['BRG012', 'Mi Instan Kuah Soto', 'Mi Instan', 3000, 3500, 250, 50, 120],
            ['BRG013', 'Mi Instan Kuah Ayam Bawang', 'Mi Instan', 3000, 3500, 180, 40, 120],
            ['BRG014', 'Mi Instan Goreng Spesial', 'Mi Instan', 3200, 3700, 150, 40, 120],
            // Non-sembako (variasi dagangan koperasi; tanpa transaksi demo)
            ['BRG015', 'Biskuit Kelapa 300 g', 'Biskuit', 12000, 14000, 80, 10, 180],
            ['BRG016', 'Sirup Cocopandan 460 ml', 'Sirup', 18000, 21000, 60, 10, 365],
            ['BRG017', 'Gas LPG 3 kg', 'Gas LPG', 18500, 22000, 0, 10, null],
            ['BRG018', 'Kopi Sachet 1 Renceng', 'Minuman', 11000, 13000, 100, 20, 180],
            ['BRG019', 'Sabun Cuci Bubuk 800 g', 'Kebutuhan Rumah Tangga', 24000, 27000, 70, 15, null],
            ['BRG020', 'Kecap Manis 600 ml', 'Bumbu Dapur', 18000, 20000, 50, 10, 365],
        ];

        return array_map(fn ($r) => [
            'kode' => $r[0],
            'nama' => $r[1],
            'kategori' => $r[2],
            'harga_beli' => $r[3],
            'harga_jual' => $r[4],
            'stok' => $r[5],
            'stok_minimum' => $r[6],
            'tanggal_kedaluarsa' => $exp($r[7]),
        ], $rows);
    }

    /**
     * Transaksi demo: [berapa hari yang lalu, [[kode_barang, jumlah], ...]].
     * Total transaksi dihitung dari detailnya (harga_satuan = harga_jual).
     */
    private function seedTransaksi(array $barang, int $idKasir): void
    {
        $daftar = [
            [28, [['BRG001', 3], ['BRG004', 2], ['BRG007', 2]]],
            [26, [['BRG011', 5], ['BRG004', 3], ['BRG009', 1]]],
            [24, [['BRG002', 2], ['BRG005', 1], ['BRG012', 4]]],
            [22, [['BRG001', 4], ['BRG007', 3], ['BRG013', 3]]],
            [20, [['BRG004', 4], ['BRG011', 6], ['BRG008', 2]]],
            [18, [['BRG001', 2], ['BRG009', 2], ['BRG014', 3]]],
            [16, [['BRG005', 2], ['BRG007', 4], ['BRG012', 5]]],
            [14, [['BRG002', 3], ['BRG004', 2], ['BRG011', 4]]],
            [12, [['BRG001', 5], ['BRG006', 2], ['BRG013', 4]]],
            [10, [['BRG007', 3], ['BRG004', 3], ['BRG010', 1]]],
            [8, [['BRG011', 5], ['BRG012', 3], ['BRG009', 1]]],
            [6, [['BRG002', 2], ['BRG005', 1], ['BRG014', 2]]],
            [4, [['BRG001', 3], ['BRG007', 2], ['BRG004', 2]]],
            [2, [['BRG011', 4], ['BRG008', 3], ['BRG013', 2]]],
            [1, [['BRG004', 2], ['BRG009', 2], ['BRG012', 4]]],
        ];

        foreach ($daftar as [$hariLalu, $detail]) {
            $total = 0;
            $baris = [];
            foreach ($detail as [$kode, $jumlah]) {
                $harga = $barang[$kode]->harga_jual;
                $subtotal = $jumlah * $harga;
                $total += $subtotal;
                $baris[] = [
                    'kode' => $kode,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $subtotal,
                ];
            }

            $transaksi = Transaksi::create([
                'id_kasir' => $idKasir,
                'tanggal' => Carbon::now()->subDays($hariLalu),
                'total' => $total,
            ]);

            foreach ($baris as $b) {
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id,
                    'id_barang' => $barang[$b['kode']]->id,
                    'jumlah' => $b['jumlah'],
                    'harga_satuan' => $b['harga_satuan'],
                    'subtotal' => $b['subtotal'],
                ]);
            }
        }
    }
}
