<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    /**
     * Halaman POS (UC-03): hanya barang yang masih ada stoknya yang
     * bisa dipilih kasir.
     */
    public function create(): View
    {
        return view('transaksi.create', [
            'barang' => Barang::where('stok', '>', 0)->orderBy('nama')->get(),
        ]);
    }

    /**
     * Memproses penjualan (UC-03 + SD-03): simpan transaksi + detail,
     * kurangi stok, lalu periksa stok minimum — jika menipis/kosong,
     * buat notifikasi (dengan rekomendasi EOQ bila tersedia).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'bayar' => ['required', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Keranjang masih kosong.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
            'bayar.required' => 'Uang bayar wajib diisi.',
        ]);

        // Gabungkan baris yang barangnya sama, lalu hitung total dengan
        // harga jual saat ini (harga tidak dipercaya dari input kasir).
        $gabung = [];
        foreach ($data['items'] as $item) {
            $gabung[$item['barang_id']] = ($gabung[$item['barang_id']] ?? 0) + $item['jumlah'];
        }

        $barangList = Barang::whereIn('id', array_keys($gabung))->get()->keyBy('id');
        $itemsHarga = [];
        $total = 0;
        foreach ($gabung as $idBarang => $jumlah) {
            $barang = $barangList[$idBarang];
            if ($jumlah > $barang->stok) {
                return back()->with('error', "Stok {$barang->nama} tidak mencukupi (sisa {$barang->stok}).")->withInput();
            }
            $itemsHarga[] = ['id_barang' => $idBarang, 'jumlah' => $jumlah, 'harga_satuan' => (float) $barang->harga_jual];
            $total += $jumlah * (float) $barang->harga_jual;
        }

        if ((float) $data['bayar'] < $total) {
            return back()->with('error', 'Uang bayar kurang dari total belanja.')->withInput();
        }

        try {
            DB::transaction(function () use ($itemsHarga, $barangList) {
                $transaksi = Transaksi::create([
                    'id_kasir' => Auth::id(),
                    'tanggal' => now(),
                    'total' => 0,
                ]);
                $transaksi->prosesTransaksi($itemsHarga);

                foreach ($itemsHarga as $item) {
                    $barang = $barangList[$item['id_barang']];
                    $barang->kurangiStok($item['jumlah']);
                    $status = $barang->cekStok();
                    if ($status === 'kosong') {
                        Notifikasi::buatNotifikasi($barang->id, 'stok_kosong', "Stok {$barang->nama} habis.");
                    } elseif ($status === 'menipis') {
                        $eoq = $barang->hitungEOQ();
                        Notifikasi::buatNotifikasi(
                            $barang->id,
                            'stok_menipis',
                            "Stok {$barang->nama} menipis (sisa {$barang->stok}).",
                            $eoq['nilai'],
                        );
                    }
                }
            });
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        $kembalian = (float) $data['bayar'] - $total;

        return redirect()->route('transaksi.create')->with(
            'sukses',
            'Transaksi berhasil. Total Rp'.number_format($total, 0, ',', '.')
            .'. Kembalian Rp'.number_format($kembalian, 0, ',', '.').'.',
        );
    }
}
