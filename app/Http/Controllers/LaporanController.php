<?php

namespace App\Http\Controllers;

use App\Models\Barang;
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

        $query = Transaksi::with('kasir', 'detailTransaksi')
            ->whereDate('tanggal', '>=', $mulai)
            ->whereDate('tanggal', '<=', $sampai);

        return view('laporan.index', [
            'mulai' => $mulai,
            'sampai' => $sampai,
            'totalTransaksi' => (clone $query)->count(),
            'totalPendapatan' => (clone $query)->sum('total'),
            'transaksi' => $query->orderByDesc('tanggal')->orderByDesc('id')
                ->paginate(10)
                ->withQueryString(),
            'stok' => Barang::orderBy('nama')->get(),
        ]);
    }
}
