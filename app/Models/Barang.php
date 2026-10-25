<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'tanggal_kedaluarsa',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'tanggal_kedaluarsa' => 'date',
        ];
    }

    public function barangMasuk(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_barang');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_barang');
    }

    /**
     * Mengurangi stok sebesar jumlah terjual. Menolak jika stok tidak
     * mencukupi, agar stok tidak pernah negatif (KF No. 2: pencegahan
     * transaksi saat stok nol ditegakkan controller sebelum memanggil ini).
     */
    public function kurangiStok(int $jumlah): void
    {
        if ($jumlah < 0) {
            throw new \InvalidArgumentException('Jumlah pengurangan stok tidak boleh negatif.');
        }
        if ($jumlah > $this->stok) {
            throw new \InvalidArgumentException('Stok tidak mencukupi.');
        }
        $this->stok -= $jumlah;
        $this->save();
    }

    /**
     * Menambah stok (dipakai saat barang masuk dicatat).
     */
    public function tambahStok(int $jumlah): void
    {
        if ($jumlah < 0) {
            throw new \InvalidArgumentException('Jumlah penambahan stok tidak boleh negatif.');
        }
        $this->stok += $jumlah;
        $this->save();
    }

    /**
     * Status ketersediaan menurut Tabel klasifikasi Bab 2:
     * 'kosong' jika stok = 0, 'menipis' jika 0 < stok <= stok_minimum,
     * 'aman' jika stok > stok_minimum.
     */
    public function cekStok(): string
    {
        if ($this->stok <= 0) {
            return 'kosong';
        }
        if ($this->stok <= $this->stok_minimum) {
            return 'menipis';
        }
        return 'aman';
    }

    /**
     * True jika barang punya tanggal kedaluwarsa dan tanggal itu sudah
     * lewat atau tinggal $hari hari lagi (default 30 hari, sesuai
     * peringatan kedaluwarsa di dashboard gudang).
     */
    public function cekKedaluarsa(int $hari = 30): bool
    {
        if ($this->tanggal_kedaluarsa === null) {
            return false;
        }
        return $this->tanggal_kedaluarsa->lessThanOrEqualTo(now()->addDays($hari));
    }

    /**
     * Menghitung jumlah pemesanan ekonomis (Bab 3 subbab 3.2.3):
     * EOQ = akar(2DS/H), dibulatkan ke satuan terdekat.
     * - D = total penjualan barang ini selama 30 hari terakhir (basis
     *   bulanan, sama dengan basis H per unit per bulan).
     * - S = biaya pemesanan dari parameter_sistem.
     * - H per unit = harga_beli x tarif dari parameter_sistem (harga beli
     *   dipakai karena modal yang mengendap di gudang senilai harga beli).
     *
     * Mengembalikan ['nilai' => ?int, 'status' => string]:
     * - D = 0 -> nilai null, status 'menunggu data' (notifikasi tetap dibuat).
     * - S/H belum lengkap atau H = 0 -> nilai null, status
     *   'parameter belum lengkap' (barang dilewati dan ditandai).
     * - Lengkap -> nilai EOQ terbulat, status 'ok'.
     */
    public function hitungEOQ(): array
    {
        $D = DetailTransaksi::where('id_barang', $this->id)
            ->whereHas('transaksi', fn ($q) => $q->where('tanggal', '>=', now()->subDays(30)))
            ->sum('jumlah');

        if ($D <= 0) {
            return ['nilai' => null, 'status' => 'menunggu data'];
        }

        $S = ParameterSistem::ambilNilai('S');
        $tarif = ParameterSistem::ambilNilai('H');
        $H = (float) $this->harga_beli * (float) $tarif;

        if ($S === null || $tarif === null || $H <= 0) {
            return ['nilai' => null, 'status' => 'parameter belum lengkap'];
        }

        return ['nilai' => (int) round(sqrt((2 * $D * $S) / $H)), 'status' => 'ok'];
    }
}
