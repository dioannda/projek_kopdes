<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Pengguna extends Authenticatable
{
    use HasFactory;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'peran',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_kasir');
    }

    public function barangMasuk(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'id_pengguna');
    }

    /**
     * Memverifikasi akun (UC-01): mencari username lalu mencocokkan
     * password dengan hash tersimpan. Mengembalikan pengguna jika cocok,
     * null jika username tidak ada atau password salah. Controller
     * kemudian memanggil Auth::login() untuk membuka sesi.
     */
    public static function login(string $username, string $password): ?self
    {
        $pengguna = self::where('username', $username)->first();

        if ($pengguna && Hash::check($password, $pengguna->password)) {
            return $pengguna;
        }

        return null;
    }

    /**
     * Mengakhiri sesi pengguna (UC-01). Controller juga membatalkan
     * dan meregenerasi token sesi sesuai konvensi Laravel.
     */
    public function logout(): void
    {
        Auth::logout();
    }
}
