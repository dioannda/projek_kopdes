<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\DetailTransaksi;
use App\Models\Pemasok;
use App\Models\Pengguna;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportController extends Controller
{
    /**
     * Halaman Impor Data (hanya Manajer): form unggah file Excel
     * rekap historis koperasi. Format file mengikuti template
     * (5 sheet: petunjuk, data barang, pembelian, penjualan, pemasok).
     */
    public function index(): View
    {
        return view('import.index');
    }

    /**
     * Memproses file Excel yang diunggah.
     *
     * Impor didefinisikan sebagai REKAP HISTORIS yang TIDAK menggerakkan
     * stok: kolom stok di sheet Data Barang adalah stok_awal (opname awal
     * periode) yang langsung menjadi stok barang, sedangkan pembelian dan
     * penjualan yang diimpor hanya tercatat sebagai riwayat (tidak
     * menambah atau mengurangi stok). Penjualan dikelompokkan lewat
     * kolom id_transaksi; shift dihitung otomatis dari jam transaksi.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:2048'],
        ], [
            'file.required' => 'Pilih dulu file Excel (.xlsx) yang akan diimpor.',
            'file.mimes' => 'File harus berformat Excel (.xlsx).',
            'file.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        } catch (\Throwable) {
            return back()->with('error', 'File tidak dapat dibaca sebagai Excel (.xlsx). Pastikan file mengikuti template impor.');
        }

        $hasil = ['barang' => 0, 'pemasok' => 0, 'pembelian' => 0, 'transaksi' => 0, 'detail' => 0];
        $galat = [];

        try {
            DB::transaction(function () use ($spreadsheet, &$hasil, &$galat) {
                $this->imporPemasok($spreadsheet, $hasil, $galat);
                $this->imporBarang($spreadsheet, $hasil, $galat);
                $this->imporPembelian($spreadsheet, $hasil, $galat);
                $this->imporPenjualan($spreadsheet, $hasil, $galat);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Impor gagal dan tidak ada data yang masuk: ' . $e->getMessage());
        }

        $pesan = 'Impor selesai: ' . $hasil['barang'] . ' barang, ' . $hasil['pemasok'] . ' pemasok, '
            . $hasil['pembelian'] . ' pembelian, ' . $hasil['transaksi'] . ' transaksi penjualan ('
            . $hasil['detail'] . ' baris detail).';
        if ($galat) {
            $pesan .= ' Ada ' . count($galat) . ' baris yang dilewati, lihat daftar di bawah.';
        }

        return redirect()->route('import.index')
            ->with('sukses', $pesan)
            ->with('impor_galat', $galat);
    }

    /**
     * Sheet 4_Data_Pemasok -> tabel pemasok.
     * Nama pemasok unik; baris yang sama diperbarui, bukan digandakan.
     */
    private function imporPemasok($spreadsheet, array &$hasil, array &$galat): void
    {
        $sheet = $spreadsheet->getSheetByName('4_Data_Pemasok');
        if (! $sheet) {
            $galat[] = 'Sheet 4_Data_Pemasok tidak ditemukan, pemasok tidak diimpor.';

            return;
        }

        foreach ($this->baris($sheet) as $nomor => $r) {
            $nama = $this->teks($r[0] ?? null);
            if ($nama === '' || str_starts_with(strtolower($nama), 'contoh')) {
                continue;
            }

            Pemasok::updateOrCreate(
                ['nama' => $nama],
                [
                    'telepon' => $this->teks($r[1] ?? null) ?: null,
                    'alamat' => $this->teks($r[2] ?? null) ?: null,
                    'keterangan' => $this->teks($r[3] ?? null) ?: null,
                ]
            );
            $hasil['pemasok']++;
        }
    }

    /**
     * Sheet 1_Data_Barang -> tabel barang.
     * Kode barang unik; stok di file adalah stok_awal (opname awal
     * periode) yang langsung menjadi stok barang di sistem.
     */
    private function imporBarang($spreadsheet, array &$hasil, array &$galat): void
    {
        $sheet = $spreadsheet->getSheetByName('1_Data_Barang');
        if (! $sheet) {
            $galat[] = 'Sheet 1_Data_Barang tidak ditemukan, data barang tidak diimpor.';

            return;
        }

        foreach ($this->baris($sheet) as $nomor => $r) {
            $kode = $this->teks($r[0] ?? null);
            $nama = $this->teks($r[1] ?? null);
            if ($kode === '' || $nama === '') {
                if ($kode !== '' || $nama !== '') {
                    $galat[] = 'Data Barang baris ' . $nomor . ': kode atau nama kosong, dilewati.';
                }
                continue;
            }

            $hargaBeli = $this->angka($r[3] ?? null);
            $hargaJual = $this->angka($r[4] ?? null);
            $stokAwal = (int) $this->angka($r[5] ?? null);
            $stokMinimum = (int) $this->angka($r[6] ?? null);
            if ($hargaBeli < 0 || $hargaJual < 0 || $stokAwal < 0 || $stokMinimum < 0) {
                $galat[] = 'Data Barang baris ' . $nomor . ' (' . $kode . '): harga atau stok negatif, dilewati.';
                continue;
            }

            $kedaluarsa = $this->parseTanggal($r[7] ?? null);

            Barang::updateOrCreate(
                ['kode' => $kode],
                [
                    'nama' => $nama,
                    'kategori' => $this->teks($r[2] ?? null),
                    'harga_beli' => $hargaBeli,
                    'harga_jual' => $hargaJual,
                    'stok' => $stokAwal,
                    'stok_minimum' => $stokMinimum,
                    'tanggal_kedaluarsa' => $kedaluarsa ? $kedaluarsa->toDateString() : null,
                ]
            );
            $hasil['barang']++;
        }
    }

    /**
     * Sheet 2_Pembelian -> tabel barang_masuk.
     * Dicatat langsung (bukan lewat catatMasuk) supaya stok TIDAK
     * bertambah: ini rekap historis, stoknya sudah tercermin di
     * stok_awal sheet Data Barang.
     */
    private function imporPembelian($spreadsheet, array &$hasil, array &$galat): void
    {
        $sheet = $spreadsheet->getSheetByName('2_Pembelian');
        if (! $sheet) {
            $galat[] = 'Sheet 2_Pembelian tidak ditemukan, pembelian tidak diimpor.';

            return;
        }

        foreach ($this->baris($sheet) as $nomor => $r) {
            $kode = $this->teks($r[0] ?? null);
            $barang = $kode === '' ? null : Barang::where('kode', $kode)->first();
            if (! $barang) {
                $galat[] = 'Pembelian baris ' . $nomor . ': kode barang ' . ($kode === '' ? '(kosong)' : $kode) . ' tidak ditemukan, dilewati.';
                continue;
            }

            $tanggal = $this->parseTanggal($r[1] ?? null);
            $jumlah = (int) $this->angka($r[2] ?? null);
            if (! $tanggal || $jumlah < 1) {
                $galat[] = 'Pembelian baris ' . $nomor . ' (' . $kode . '): tanggal tidak valid atau jumlah kurang dari 1, dilewati.';
                continue;
            }

            $pencatat = $this->penggunaDariUsername($this->teks($r[3] ?? null));
            if (! $pencatat) {
                $galat[] = 'Pembelian baris ' . $nomor . ' (' . $kode . '): username pencatat tidak ditemukan, dilewati.';
                continue;
            }

            $idPemasok = null;
            $namaPemasok = $this->teks($r[4] ?? null);
            if ($namaPemasok !== '' && strtolower($namaPemasok) !== 'contoh') {
                $pemasok = Pemasok::where('nama', $namaPemasok)->first();
                $idPemasok = $pemasok ? $pemasok->id : null;
            }

            BarangMasuk::create([
                'id_barang' => $barang->id,
                'id_pengguna' => $pencatat->id,
                'tanggal' => $tanggal,
                'jumlah' => $jumlah,
                'id_pemasok' => $idPemasok,
            ]);
            $hasil['pembelian']++;
        }
    }

    /**
     * Sheet 3_Penjualan -> tabel transaksi + detail_transaksi.
     * Baris dikelompokkan lewat id_transaksi (satu id = satu transaksi).
     * Dicatat langsung (bukan lewat POS) supaya stok TIDAK berkurang:
     * ini rekap historis, stoknya sudah tercermin di stok_awal sheet
     * Data Barang. Total dihitung dari jumlah x harga_satuan, shift
     * dihitung otomatis dari jam, uang bayar = total, kembalian 0.
     */
    private function imporPenjualan($spreadsheet, array &$hasil, array &$galat): void
    {
        $sheet = $spreadsheet->getSheetByName('3_Penjualan');
        if (! $sheet) {
            $galat[] = 'Sheet 3_Penjualan tidak ditemukan, penjualan tidak diimpor.';

            return;
        }

        $kelompok = [];
        foreach ($this->baris($sheet) as $nomor => $r) {
            $id = $this->teks($r[0] ?? null);
            if ($id === '') {
                continue;
            }
            $kelompok[$id][] = ['nomor' => $nomor, 'r' => $r];
        }

        foreach ($kelompok as $id => $barisKelompok) {
            $pertama = $barisKelompok[0]['r'];
            $tanggal = $this->parseTanggal($pertama[1] ?? null);
            if (! $tanggal) {
                $galat[] = 'Penjualan ' . $id . ': tanggal tidak valid, transaksi dilewati.';
                continue;
            }

            $kasir = $this->penggunaDariUsername($this->teks($pertama[6] ?? null));
            if (! $kasir) {
                $galat[] = 'Penjualan ' . $id . ': username kasir tidak ditemukan, transaksi dilewati.';
                continue;
            }

            $metode = strtolower($this->teks($pertama[5] ?? null)) === 'qris' ? 'qris' : 'cash';

            $detail = [];
            $total = 0;
            foreach ($barisKelompok as $item) {
                $r = $item['r'];
                $nomor = $item['nomor'];
                $kode = $this->teks($r[2] ?? null);
                $barang = $kode === '' ? null : Barang::where('kode', $kode)->first();
                if (! $barang) {
                    $galat[] = 'Penjualan ' . $id . ' baris ' . $nomor . ': kode barang ' . ($kode === '' ? '(kosong)' : $kode) . ' tidak ditemukan, baris dilewati.';
                    continue;
                }
                $jumlah = (int) $this->angka($r[3] ?? null);
                $harga = $this->angka($r[4] ?? null);
                if ($jumlah < 1 || $harga < 0) {
                    $galat[] = 'Penjualan ' . $id . ' baris ' . $nomor . ' (' . $kode . '): jumlah kurang dari 1 atau harga negatif, baris dilewati.';
                    continue;
                }
                $detail[] = [
                    'id_barang' => $barang->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $jumlah * $harga,
                ];
                $total += $jumlah * $harga;
            }

            if (! $detail) {
                $galat[] = 'Penjualan ' . $id . ': tidak ada baris detail yang valid, transaksi dilewati.';
                continue;
            }

            $transaksi = Transaksi::create([
                'id_kasir' => $kasir->id,
                'tanggal' => $tanggal,
                'total' => $total,
                'uang_bayar' => $total,
                'kembalian' => 0,
                'metode_pembayaran' => $metode,
                'shift' => Transaksi::shiftDariWaktu($tanggal),
            ]);

            foreach ($detail as $d) {
                $d['id_transaksi'] = $transaksi->id;
                DetailTransaksi::create($d);
            }

            $hasil['transaksi']++;
            $hasil['detail'] += count($detail);
        }
    }

    /**
     * Membaca isi sheet menjadi array baris (tanpa baris header).
     * Mengembalikan [nomor_baris_excel => [nilai_kolom, ...]].
     * Baris yang seluruh selnya kosong dilewati.
     */
    private function baris(Worksheet $sheet): array
    {
        $semua = $sheet->toArray(null, true, false, false);
        $hasil = [];
        foreach ($semua as $indeks => $r) {
            if ($indeks === 0) {
                continue;
            }
            $kosong = true;
            foreach ($r as $nilai) {
                if ($nilai !== null && trim((string) $nilai) !== '') {
                    $kosong = false;
                    break;
                }
            }
            if ($kosong) {
                continue;
            }
            $hasil[$indeks + 1] = array_values($r);
        }

        return $hasil;
    }

    private function teks(mixed $nilai): string
    {
        return trim((string) ($nilai ?? ''));
    }

    private function angka(mixed $nilai, float $default = 0): float
    {
        if ($nilai === null || $nilai === '') {
            return $default;
        }

        return (float) $nilai;
    }

    /**
     * Mengubah nilai sel menjadi Carbon. Menangani tiga bentuk:
     * tanggal serial Excel (angka), teks tanggal, dan objek DateTime.
     * Mengembalikan null bila nilainya kosong atau tidak dapat dibaca.
     */
    private function parseTanggal(mixed $nilai): ?Carbon
    {
        if ($nilai === null || $nilai === '') {
            return null;
        }
        if ($nilai instanceof \DateTimeInterface) {
            return Carbon::instance($nilai);
        }
        if (is_numeric($nilai)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $nilai));
            } catch (\Throwable) {
                return null;
            }
        }
        try {
            return Carbon::parse((string) $nilai);
        } catch (\Throwable) {
            return null;
        }
    }

    private function penggunaDariUsername(string $username): ?Pengguna
    {
        if ($username === '') {
            return null;
        }

        return Pengguna::where('username', $username)->first();
    }
}
