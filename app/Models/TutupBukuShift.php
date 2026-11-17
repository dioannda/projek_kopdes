<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutupBukuShift extends Model
{
    use HasFactory;

    protected $table = 'tutup_buku_shift';

    protected $fillable = [
        'tanggal',
        'shift',
        'id_kasir',
        'saldo_awal',
        'total_pendapatan',
        'total_tunai',
        'total_qris',
        'jumlah_diserahkan',
        'saldo_lanjutan',
        'keterangan',
        'status',
        'id_manajer',
        'disetujui_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'shift' => 'integer',
            'saldo_awal' => 'decimal:2',
            'total_pendapatan' => 'decimal:2',
            'total_tunai' => 'decimal:2',
            'total_qris' => 'decimal:2',
            'jumlah_diserahkan' => 'decimal:2',
            'saldo_lanjutan' => 'decimal:2',
            'disetujui_pada' => 'datetime',
        ];
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_kasir');
    }

    public function manajer(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_manajer');
    }

    public function labelShift(): string
    {
        return $this->shift === 2 ? 'Shift 2' : 'Shift 1';
    }

    public function labelStatus(): string
    {
        return $this->status === 'disetujui' ? 'Disetujui' : 'Menunggu Persetujuan';
    }

    /**
     * Saldo awal untuk satu shift: sisa dari tutup buku terakhir
     * sebelum shift itu (urut tanggal lalu shift). Shift pertama
     * yang pernah ada: 0.
     */
    public static function saldoAwalUntuk(string $tanggal, int $shift): float
    {
        $terakhir = static::where(function ($query) use ($tanggal, $shift) {
            $query->where('tanggal', '<', $tanggal)
                ->orWhere(function ($query) use ($tanggal, $shift) {
                    $query->where('tanggal', $tanggal)->where('shift', '<', $shift);
                });
        })->orderByDesc('tanggal')->orderByDesc('shift')->first();

        return $terakhir ? (float) $terakhir->saldo_lanjutan : 0.0;
    }
}
