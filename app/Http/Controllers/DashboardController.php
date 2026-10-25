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
            $stats['Transaksi hari ini'] = $transaksiHariIni->count() . ' transaksi';
            $stats['Pendapatan hari ini'] = 'Rp' . number_format($transaksiHariIni->sum('total'), 0, ',', '.');
        } else {
            $barang = Barang::all();
            $stats['Total barang'] = $barang->count() . ' barang';
            $stats['Stok menipis'] = $barang->filter(fn ($b) => $b->cekStok() === 'menipis')->count() . ' barang';
            $stats['Stok kosong'] = $barang->filter(fn ($b) => $b->cekStok() === 'kosong')->count() . ' barang';
            $stats['Mendekati kedaluwarsa'] = $barang->filter(fn ($b) => $b->cekKedaluarsa())->count() . ' barang';
            $stats['Notifikasi belum dibaca'] = Notifikasi::where('status_baca', false)->count() . ' notifikasi';
            if ($pengguna->peran === 'manajer') {
                $transaksiHariIni = Transaksi::whereDate('tanggal', today());
                $stats['Transaksi hari ini'] = $transaksiHariIni->count() . ' transaksi';
                $stats['Pendapatan hari ini'] = 'Rp' . number_format($transaksiHariIni->sum('total'), 0, ',', '.');
            }
        }

        return view('dashboard', [
            'pengguna' => $pengguna,
            'stats' => $stats,
        ]);
    }
}
