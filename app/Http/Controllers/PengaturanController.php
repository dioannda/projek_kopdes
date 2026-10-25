<?php

namespace App\Http\Controllers;

use App\Models\ParameterSistem;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    /**
     * Halaman Pengaturan (UC-08): form parameter EOQ + daftar pengguna.
     */
    public function index(): View
    {
        return view('pengaturan.index', [
            'nilaiS' => ParameterSistem::ambilNilai('S'),
            'tarifH' => ParameterSistem::ambilNilai('H'),
            'pengguna' => Pengguna::orderBy('nama')->get(),
        ]);
    }

    /**
     * Mengubah parameter S/H (UC-08): disimpan ke parameter_sistem,
     * dibaca hitungEOQ() setiap kali dibutuhkan.
     */
    public function updateParameter(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'S' => ['required', 'numeric', 'min:0'],
            'H' => ['required', 'numeric', 'min:0', 'max:1'],
        ], [
            'S.required' => 'Biaya pemesanan (S) wajib diisi.',
            'H.required' => 'Tarif penyimpanan (H) wajib diisi.',
            'H.max' => 'Tarif H diisi sebagai desimal (maksimal 1 = 100%).',
        ]);

        ParameterSistem::ubahNilai('S', (float) $data['S']);
        ParameterSistem::ubahNilai('H', (float) $data['H']);

        return redirect()->route('pengaturan.index')->with('sukses', 'Parameter EOQ berhasil diperbarui.');
    }
}
