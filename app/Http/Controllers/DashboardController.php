<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard (UC-01). Semua peran mendarat di halaman
     * yang sama; menunya menyesuaikan peran (lihat layouts/app).
     * Kartu ringkasan menyesuaikan peran: kasir melihat penjualannya
     * hari ini; gudang/manajer melihat kondisi stok dan notifikasi.
     */
    public function __invoke(): View
    {
        $pengguna = Auth::user();
        $stats = [];

        if ($pengguna->peran === 'kasir') {
            $transaksiHariIni = Transaksi::where('id_kasir', $pengguna->id)
                ->whereDate('tanggal', today());
            $stats[] = ['label' => 'Transaksi hari ini', 'nilai' => $transaksiHariIni->count() . ' transaksi', 'url' => route('transaksi.index')];
            $stats[] = ['label' => 'Pendapatan hari ini', 'nilai' => 'Rp' . number_format($transaksiHariIni->sum('total'), 0, ',', '.'), 'url' => route('transaksi.index')];
        } else {
            $barang = Barang::all();
            $stats[] = ['label' => 'Total barang', 'nilai' => $barang->count() . ' barang', 'url' => route('barang.index')];
            $stats[] = ['label' => 'Stok menipis', 'nilai' => $barang->filter(fn ($b) => $b->cekStok() === 'menipis')->count() . ' barang', 'url' => route('barang.index', ['status' => 'menipis'])];
            $stats[] = ['label' => 'Stok kosong', 'nilai' => $barang->filter(fn ($b) => $b->cekStok() === 'kosong')->count() . ' barang', 'url' => route('barang.index', ['status' => 'kosong'])];
            $stats[] = ['label' => 'Mendekati kedaluwarsa', 'nilai' => $barang->filter(fn ($b) => $b->cekKedaluarsa())->count() . ' barang', 'url' => route('barang.index', ['status' => 'kedaluwarsa'])];
            $stats[] = ['label' => 'Notifikasi belum dibaca', 'nilai' => Notifikasi::where('status_baca', false)->count() . ' notifikasi', 'url' => route('notifikasi.index', ['belum_dibaca' => '1'])];
            if ($pengguna->peran === 'manajer') {
                $transaksiHariIni = Transaksi::whereDate('tanggal', today());
                $stats[] = ['label' => 'Transaksi hari ini', 'nilai' => $transaksiHariIni->count() . ' transaksi', 'url' => route('laporan.index')];
                $stats[] = ['label' => 'Pendapatan hari ini', 'nilai' => 'Rp' . number_format($transaksiHariIni->sum('total'), 0, ',', '.'), 'url' => route('laporan.index')];
            }
        }

        return view('dashboard', [
            'pengguna' => $pengguna,
            'stats' => $stats,
        ]);
    }
}
