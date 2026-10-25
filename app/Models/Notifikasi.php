<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'id_barang',
        'jenis',
        'pesan',
        'rekomendasi_jumlah',
        'status_baca',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'status_baca' => 'boolean',
            'tanggal' => 'datetime',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    /**
     * Membuat satu baris notifikasi (UC-03/UC-05). $jenis memakai nilai
     * ENUM persis dari migrasi: 'stok_kosong', 'stok_menipis', atau
     * 'kedaluwarsa'. $rekomendasiJumlah hanya diisi untuk rekomendasi
     * EOQ (notifikasi stok menipis), null untuk jenis lainnya.
     */
    public static function buatNotifikasi(int $idBarang, string $jenis, string $pesan, ?int $rekomendasiJumlah = null): self
    {
        return self::create([
            'id_barang' => $idBarang,
            'jenis' => $jenis,
            'pesan' => $pesan,
            'rekomendasi_jumlah' => $rekomendasiJumlah,
            'status_baca' => false,
            'tanggal' => now(),
        ]);
    }

    /**
     * Menandai notifikasi sudah dibaca (UC-05: saat aktor membuka detail).
     */
    public function tandaiDibaca(): void
    {
        $this->status_baca = true;
        $this->save();
    }
}
