<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BarangMasukController extends Controller
{
    /**
     * Halaman Barang Masuk (UC-04): form pencatatan + riwayat
     * penerimaan terakhir. Parameter barang_id (misalnya dari tautan
     * "Catat Masuk" di halaman Rekomendasi) membuat barang tersebut
     * terpilih otomatis di form.
     */
    public function index(Request $request): View
    {
        return view('barang-masuk.index', [
            'barang' => Barang::orderBy('nama')->get(),
            'barangTerpilih' => $request->input('barang_id'),
            'riwayat' => BarangMasuk::with('barang', 'pengguna')
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }

    /**
     * Mencatat penerimaan (UC-04): simpan riwayat sekaligus menambah
     * stok barangnya lewat BarangMasuk::catatMasuk().
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_barang' => ['required', 'exists:barang,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
        ], [
            'id_barang.required' => 'Barang wajib dipilih.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'tanggal.required' => 'Tanggal wajib diisi.',
        ]);

        $masuk = new BarangMasuk([
            'id_barang' => $data['id_barang'],
            'id_pengguna' => Auth::id(),
            'tanggal' => $data['tanggal'],
            'jumlah' => $data['jumlah'],
        ]);
        $masuk->catatMasuk();

        return redirect()->route('barang-masuk.index')
            ->with('sukses', 'Barang masuk berhasil dicatat. Stok ' . $masuk->barang->nama . ' bertambah ' . $data['jumlah'] . '.');
    }

    /**
     * Hapus riwayat penerimaan (koreksi salah catat): stok dikembalikan
     * dulu lewat kurangiStok; jika stok sudah tidak mencukupi (sebagian
     * terjual), penghapusan ditolak agar stok tidak negatif.
     */
    public function destroy(BarangMasuk $barangMasuk): RedirectResponse
    {
        try {
            $barangMasuk->barang->kurangiStok($barangMasuk->jumlah);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Riwayat tidak dapat dihapus: stok ' . $barangMasuk->barang->nama . ' tidak mencukupi untuk pengembalian (sebagian sudah terjual).');
        }
        $barangMasuk->delete();

        return redirect()->route('barang-masuk.index')->with('sukses', 'Riwayat penerimaan dihapus, stok dikembalikan.');
    }
}
