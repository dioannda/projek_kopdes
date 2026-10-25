<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard (UC-01). Semua peran mendarat di halaman
     * yang sama; menunya menyesuaikan peran (lihat layouts/app).
     */
    public function __invoke(): View
    {
        return view('dashboard', [
            'pengguna' => auth()->user(),
        ]);
    }
}
