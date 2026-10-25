<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Daftar notifikasi (UC-05): belum dibaca dulu, lalu terbaru.
     */
    public function index(): View
    {
        return view('notifikasi.index', [
            'notifikasi' => Notifikasi::with('barang')
                ->orderBy('status_baca')
                ->orderByDesc('tanggal')
                ->paginate(10),
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
}
