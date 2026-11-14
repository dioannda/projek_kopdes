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
        'uang_bayar',
        'kembalian',
        'metode_pembayaran',
        'shift',
    ];

        protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'total' => 'decimal:2',
            'uang_bayar' => 'decimal:2',
            'kembalian' => 'decimal:2',
            'shift' => 'integer',
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

    public function hitungTotal(): float
    {
        $total = $this->detailTransaksi()->sum('subtotal');
        $this->total = $total;
        $this->save();

        return (float) $total;
    }


    public static function shiftDariWaktu($waktu): int
    {
        return (int) $waktu->format('H') < 14 ? 1 : 2;
    }

    public function labelShift(): string
    {
        return $this->shift === 2 ? 'Shift 2' : 'Shift 1';
    }


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
