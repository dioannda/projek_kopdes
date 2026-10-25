<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Daftar notifikasi (UC-05): belum dibaca dulu, lalu terbaru.
     * Sebelum daftar tampil, sistem membangkitkan notifikasi
     * kedaluwarsa secara on-demand (konsisten dengan EOQ on-demand):
     * satu notifikasi per barang yang mendekati/sudah kedaluwarsa
     * (30 hari) dan belum pernah dibuatkan notifikasi kedaluwarsa.
     */
    public function index(): View
    {
        foreach (Barang::whereNotNull('tanggal_kedaluarsa')->get() as $barang) {
            if (! $barang->cekKedaluarsa()) {
                continue;
            }
            if (Notifikasi::where('id_barang', $barang->id)->where('jenis', 'kedaluwarsa')->exists()) {
                continue;
            }
            $lewat = $barang->tanggal_kedaluarsa->isPast();
            Notifikasi::buatNotifikasi(
                $barang->id,
                'kedaluwarsa',
                $lewat
                    ? "Barang {$barang->nama} sudah kedaluwarsa ({$barang->tanggal_kedaluarsa->format('d-m-Y')})."
                    : "Barang {$barang->nama} mendekati kedaluwarsa ({$barang->tanggal_kedaluarsa->format('d-m-Y')}).",
            );
        }

        $query = Notifikasi::with('barang');
        if (in_array(request('jenis'), ['stok_kosong', 'stok_menipis', 'kedaluwarsa'], true)) {
            $query->where('jenis', request('jenis'));
        }

        return view('notifikasi.index', [
            'notifikasi' => $query
                ->orderBy('status_baca')
                ->orderByDesc('tanggal')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    /**
     * Detail notifikasi (UC-05, AD-05): membuka detail sekaligus
     * menandai notifikasi sudah dibaca.
     */
    public function show(Notifikasi $notifikasi): View
    {
        $notifikasi->tandaiDibaca();

        return view('notifikasi.show', ['notifikasi' => $notifikasi]);
    }

    /**
     * Menandai semua notifikasi yang belum dibaca menjadi sudah dibaca.
     */
    public function bacaSemua(): RedirectResponse
    {
        Notifikasi::where('status_baca', false)->update(['status_baca' => true]);

        return back()->with('sukses', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
