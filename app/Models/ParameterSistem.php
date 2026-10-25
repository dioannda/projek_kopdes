<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterSistem extends Model
{
    use HasFactory;

    protected $table = 'parameter_sistem';

    protected $fillable = [
        'nama_parameter',
        'nilai',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:4',
        ];
    }

    /**
     * Membaca nilai parameter ('S' atau 'H') dari tabel parameter_sistem.
     * Mengembalikan null jika parameter belum pernah diinput.
     */
    public static function ambilNilai(string $nama): ?float
    {
        $parameter = self::where('nama_parameter', $nama)->first();

        return $parameter ? (float) $parameter->nilai : null;
    }

    /**
     * Mengubah (atau membuat jika belum ada) nilai parameter.
     * Hanya dipanggil dari UC-08, yang aktornya Manajer.
     */
    public static function ubahNilai(string $nama, float $nilai, ?string $keterangan = null): self
    {
        $data = ['nilai' => $nilai];

        if ($keterangan !== null) {
            $data['keterangan'] = $keterangan;
        }

        return self::updateOrCreate(['nama_parameter' => $nama], $data);
    }
}
