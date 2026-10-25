<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\ParameterSistem;
use Illuminate\View\View;

class RekomendasiController extends Controller
{
    /**
     * Halaman rekomendasi EOQ (UC-06): EOQ dihitung on-demand saat
     * halaman dibuka — satu baris per barang beserta D, S, dan H
     * pembentuknya, agar angkanya bisa diverifikasi manual (lih. UJI-22).
     */
    public function index(): View
    {
        $S = ParameterSistem::ambilNilai('S');
        $tarif = ParameterSistem::ambilNilai('H');

        $rekomendasi = Barang::orderBy('nama')->get()->map(function ($barang) use ($S, $tarif) {
            $D = DetailTransaksi::where('id_barang', $barang->id)
                ->whereHas('transaksi', fn ($q) => $q->where('tanggal', '>=', now()->subDays(30)))
                ->sum('jumlah');
            $eoq = $barang->hitungEOQ();

            return [
                'barang' => $barang,
                'D' => $D,
                'S' => $S,
                'H' => $tarif === null ? null : (float) $barang->harga_beli * $tarif,
                'nilai' => $eoq['nilai'],
                'status' => $eoq['status'],
            ];
        });

        return view('rekomendasi.index', ['rekomendasi' => $rekomendasi]);
    }
}
