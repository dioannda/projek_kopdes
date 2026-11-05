<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

      protected $fillable = [
        'id_kasir',
        'tanggal',
        'total',
        'metode_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'total' => 'decimal:2',
        ];
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_kasir');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }

    /**
     * Menghitung total belanja dari jumlah subtotal seluruh detail,
     * menyimpannya ke kolom total, dan mengembalikan nilainya.
     */
    public function hitungTotal(): float
    {
        $total = $this->detailTransaksi()->sum('subtotal');
        $this->total = $total;
        $this->save();

        return (float) $total;
    }

        /**
     * Aturan shift (SATU-SATUNYA tempat batas jam didefinisikan):
     * Shift 1 = transaksi sebelum pukul 14.00, Shift 2 = pukul 14.00
     * ke atas. Selama jam buka koperasi (07.00-21.00) aturan ini
     * membagi hari kerja menjadi dua shift. Kalau dospem/manager
     * menetapkan jam berbeda, ubah angka 14 di sini saja.
     */
    public static function shiftDariWaktu($waktu): int
    {
        return (int) $waktu->format('H') < 14 ? 1 : 2;
    }

    public function labelShift(): string
    {
        return $this->shift === 2 ? 'Shift 2' : 'Shift 1';
    }

    /**
     * Memproses penjualan: membuat baris detail dari $items lalu
     * menghitung total. $items berbentuk
     * [['id_barang' => int, 'jumlah' => int, 'harga_satuan' => float], ...].
     * Pemotongan stok dan notifikasi EOQ diorkestrasi controller
     * (lihat SD-03), bukan di sini.
     */
    public function prosesTransaksi(array $items): self
    {
        foreach ($items as $item) {
            $this->detailTransaksi()->create([
                'id_barang' => $item['id_barang'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal' => $item['jumlah'] * $item['harga_satuan'],
            ]);
        }

        $this->hitungTotal();

        return $this;
    }
}
