<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'id_barang',
        'id_pengguna',
        'tanggal',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    /**
     * Mencatat penerimaan: menyimpan riwayat barang masuk ini sekaligus
     * menambah stok barangnya (UC-04).
     */
    public function catatMasuk(): self
    {
        $this->save();
        $this->barang->tambahStok($this->jumlah);

        return $this;
    }
}
