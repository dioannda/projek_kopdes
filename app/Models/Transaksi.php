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
