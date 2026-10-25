<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BarangController extends Controller
{
    /**
     * Daftar barang (UC-02): pencarian nama/kode + filter kategori
     * dan status (menipis/kosong/kedaluwarsa), sesuai mockup halaman
     * Data Barang.
     */
    public function index(Request $request): View
    {
        $barang = Barang::query()
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = (string) $request->string('cari');
                $query->where(function ($q) use ($cari) {
                    $q->where('nama', 'like', "%{$cari}%")
                        ->orWhere('kode', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori', (string) $request->string('kategori')))
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'menipis') {
                    $query->where('stok', '>', 0)->whereColumn('stok', '<=', 'stok_minimum');
                } elseif ($request->status === 'kosong') {
                    $query->where('stok', '<=', 0);
                } elseif ($request->status === 'kedaluwarsa') {
                    $query->whereNotNull('tanggal_kedaluarsa')->where('tanggal_kedaluarsa', '<=', now()->addDays(30));
                }
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $daftarKategori = Barang::distinct()->orderBy('kategori')->pluck('kategori');

        return view('barang.index', [
            'barang' => $barang,
            'daftarKategori' => $daftarKategori,
        ]);
    }

    public function create(): View
    {
        return view('barang.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Barang::create($this->validasi($request));

        return redirect()->route('barang.index')->with('sukses', 'Data barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang): View
    {
        return view('barang.edit', ['barang' => $barang]);
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $barang->update($this->validasi($request, $barang));

        return redirect()->route('barang.index')->with('sukses', 'Data barang berhasil diperbarui.');
    }

    /**
     * Hapus barang (UC-02 + AD-02): ditolak jika barang masih terkait
     * riwayat transaksi (detail_transaksi) atau riwayat penerimaan
     * (barang_masuk). FK keduanya restrict, jadi penolakan harus
     * dilakukan di aplikasi, bukan dibiarkan menjadi error database.
     */
    public function destroy(Barang $barang): RedirectResponse
    {
        if ($barang->detailTransaksi()->exists() || $barang->barangMasuk()->exists()) {
            return redirect()->route('barang.index')
                ->with('error', 'Data tidak dapat dihapus karena masih terkait dengan riwayat transaksi atau penerimaan.');
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('sukses', 'Data barang berhasil dihapus.');
    }

    private function validasi(Request $request, ?Barang $barang = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('barang', 'kode')->ignore($barang?->id)],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'tanggal_kedaluarsa' => ['nullable', 'date'],
        ], [
            'kode.required' => 'Kode barang wajib diisi.',
            'kode.unique' => 'Kode barang sudah digunakan.',
            'nama.required' => 'Nama barang wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
        ]);
    }
}
