<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Laporan penjualan (UC-07): filter rentang tanggal, ringkasan
     * jumlah transaksi + total pendapatan, dan daftar transaksi.
     */
    public function index(Request $request): View
    {
        $mulai = $request->filled('mulai')
            ? (string) $request->string('mulai')
            : now()->subDays(30)->format('Y-m-d');
        $sampai = $request->filled('sampai')
            ? (string) $request->string('sampai')
            : now()->format('Y-m-d');

                    $kasirDipilih = $request->filled('kasir')
            ? (int) $request->integer('kasir')
            : 0;
        $shiftDipilih = $request->filled('shift')
            ? (int) $request->integer('shift')
            : 0;

        $query = Transaksi::with('kasir', 'detailTransaksi')
            ->whereDate('tanggal', '>=', $mulai)
            ->whereDate('tanggal', '<=', $sampai);

                    if ($kasirDipilih !== 0) {
            $query->where('id_kasir', $kasirDipilih);
        }
        if ($shiftDipilih === 1 || $shiftDipilih === 2) {
            $query->where('shift', $shiftDipilih);
        }

        return view('laporan.index', [
            'mulai' => $mulai,
            'sampai' => $sampai,
                        'kasirList' => Pengguna::where('peran', 'kasir')->orderBy('nama')->get(),
            'kasirDipilih' => $kasirDipilih,
            'shiftDipilih' => $shiftDipilih,
            'totalTransaksi' => (clone $query)->count(),
            'totalPendapatan' => (clone $query)->sum('total'),
            'transaksi' => $query->orderByDesc('tanggal')->orderByDesc('id')
                ->paginate(10)
                ->withQueryString(),
            'stok' => Barang::orderBy('nama')->get(),
        ]);
    }
}
