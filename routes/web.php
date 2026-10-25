<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // UC-02: hanya Pengurus Gudang dan Manajer.
    Route::middleware('role:gudang,manajer')->group(function () {
        Route::resource('barang', BarangController::class)->except(['show']);
    });

    // UC-03: hanya Kasir.
    Route::middleware('role:kasir')->group(function () {
        Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    });

    // UC-04: hanya Pengurus Gudang.
    Route::middleware('role:gudang')->group(function () {
        Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
        Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    });

    // UC-05: Pengurus Gudang dan Manajer.
    Route::middleware('role:gudang,manajer')->group(function () {
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/{notifikasi}', [NotifikasiController::class, 'show'])->name('notifikasi.show');
    });

    // UC-06: Pengurus Gudang dan Manajer.
    Route::middleware('role:gudang,manajer')->group(function () {
        Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi.index');
    });

    // UC-07: hanya Manajer.
    Route::middleware('role:manajer')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });

    // UC-08: hanya Manajer.
    Route::middleware('role:manajer')->group(function () {
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan/parameter', [PengaturanController::class, 'updateParameter'])->name('pengaturan.updateParameter');
        Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
        Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::get('/pengguna/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    });
});
