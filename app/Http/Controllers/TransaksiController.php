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
    /**
     * Riwayat transaksi kasir (tambahan): transaksi milik kasir yang
     * login, terbaru dulu, masing-masing tertaut ke struknya.
     */
    public function index(): View
    {
        return view('transaksi.index', [
            'transaksi' => Transaksi::where('id_kasir', Auth::id())
                ->with('detailTransaksi')
                ->orderByDesc('tanggal')->orderByDesc('id')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        // Barang kedaluwarsa tidak dijual: tanggal kedaluwarsa kosong
        // atau masih berlaku (sampai dengan hari ini).
        return view('transaksi.create', [
            'barang' => Barang::where('stok', '>', 0)
                ->where(function ($query) {
                    $query->whereNull('tanggal_kedaluarsa')
                        ->orWhereDate('tanggal_kedaluarsa', '>=', today());
                })
                ->orderBy('nama')->get(),
        ]);
    }

    /**
     * Struk penjualan (UJI-11): detail transaksi + total + bayar/
     * kembalian (dari flash saat redirect setelah pembayaran).
     */
    public function show(Transaksi $transaksi): View
    {
        if (Auth::user()->peran === 'kasir' && $transaksi->id_kasir !== Auth::id()) {
            abort(403);
        }
        $transaksi->load('detailTransaksi.barang', 'kasir');

        return view('transaksi.show', ['transaksi' => $transaksi]);
    }

    /**
     * Membatalkan transaksi (tambahan): hanya kasir pemilik transaksi.
     * Stok tiap barang dikembalikan, lalu detail dan transaksi dihapus.
     * Notifikasi yang pernah dibuat saat penjualan tetap menjadi riwayat.
     */
    public function batalkan(Transaksi $transaksi): RedirectResponse
    {
        if ($transaksi->id_kasir !== Auth::id()) {
            abort(403);
        }
        DB::transaction(function () use ($transaksi) {
            foreach ($transaksi->detailTransaksi as $detail) {
                $detail->barang->tambahStok($detail->jumlah);
            }
            $transaksi->detailTransaksi()->delete();
            $transaksi->delete();
        });

        return redirect()->route('transaksi.index')->with('sukses', 'Transaksi dibatalkan, stok dikembalikan.');
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
            'metode_pembayaran' => ['required', 'in:cash,qris'],
            'bayar' => ['required_if:metode_pembayaran,cash', 'nullable', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Keranjang masih kosong.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'bayar.required_if' => 'Uang bayar wajib diisi untuk pembayaran tunai.',
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
            if ($barang->tanggal_kedaluarsa !== null && $barang->tanggal_kedaluarsa->lt(today())) {
                return back()->with('error', "Barang {$barang->nama} sudah kedaluwarsa dan tidak dapat dijual.")->withInput();
            }
            $itemsHarga[] = ['id_barang' => $idBarang, 'jumlah' => $jumlah, 'harga_satuan' => (float) $barang->harga_jual];
            $total += $jumlah * (float) $barang->harga_jual;
        }

                // QRIS statis: tidak ada kembalian, bayar selalu sama dengan total.
        // Tunai: tetap wajib bayar >= total seperti sebelumnya.
        $bayar = (float) ($data['bayar'] ?? 0);
        if ($data['metode_pembayaran'] === 'qris') {
            $bayar = $total;
        } elseif ($bayar < $total) {
            return back()->with('error', 'Uang bayar kurang dari total belanja.')->withInput();
        }

        try {
            $transaksi = null;
                       DB::transaction(function () use ($itemsHarga, $barangList, $data, &$transaksi) {
                $transaksi = Transaksi::create([
                    'id_kasir' => Auth::id(),
                    'tanggal' => now(),
                    'total' => 0,
                    'metode_pembayaran' => $data['metode_pembayaran'],
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

        $kembalian = $bayar - $total;

        $pesan = 'Transaksi berhasil. Total Rp'.number_format($total, 0, ',', '.');
        $pesan .= $data['metode_pembayaran'] === 'qris'
            ? ' (QRIS).'
            : '. Kembalian Rp'.number_format($kembalian, 0, ',', '.').'.';

        return redirect()->route('transaksi.show', $transaksi)->with(
            'sukses',
            $pesan,
        )->with('bayar', $bayar)->with('kembalian', $kembalian);
    }
}
