<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PemasokController extends Controller
{
    /**
     * Halaman Data Pemasok (hanya Manajer): daftar pemasok + form
     * tambah pemasok. Pemasok juga dapat masuk lewat impor data
     * (sheet 4_Data_Pemasok); nama yang sama diperbarui, bukan digandakan.
     */
    public function index(): View
    {
        return view('pemasok.index', [
            'pemasok' => Pemasok::orderBy('nama')->paginate(10),
        ]);
    }

    /**
     * Menambah pemasok baru. Nama pemasok unik; nama yang sudah ada
     * (misalnya dari impor) ditolak supaya tidak ganda.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:pemasok,nama'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama.required' => 'Nama pemasok wajib diisi.',
            'nama.unique' => 'Pemasok dengan nama ini sudah ada.',
        ]);

        Pemasok::create($data);

        return redirect()->route('pemasok.index')
            ->with('sukses', 'Pemasok ' . $data['nama'] . ' berhasil ditambahkan.');
    }

    /**
     * Menghapus pemasok. Ditolak bila pemasok masih terkait dengan
     * riwayat pembelian, supaya data pembelian tidak kehilangan
     * pemasoknya (pola yang sama dengan hapus barang).
     */
    public function destroy(Pemasok $pemasok): RedirectResponse
    {
        if ($pemasok->barangMasuk()->exists()) {
            return redirect()->route('pemasok.index')
                ->with('error', 'Pemasok tidak dapat dihapus karena masih terkait dengan riwayat pembelian.');
        }

        $pemasok->delete();

        return redirect()->route('pemasok.index')->with('sukses', 'Pemasok berhasil dihapus.');
    }
}
