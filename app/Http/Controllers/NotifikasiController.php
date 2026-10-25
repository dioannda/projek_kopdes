<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
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
     * Menandai satu notifikasi sudah dibaca (UC-05).
     */
    public function baca(Notifikasi $notifikasi): RedirectResponse
    {
        $notifikasi->tandaiDibaca();

        return back();
    }
}
