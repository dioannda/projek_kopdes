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
}
