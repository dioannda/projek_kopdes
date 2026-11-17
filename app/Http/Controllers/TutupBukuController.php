<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TutupBukuShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TutupBukuController extends Controller
{
    /**
     * Kasir menyerahkan tutup buku (UC-10): rekap shift dihitung
     * dari transaksi kasir itu pada tanggal dan shift tersebut.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'shift' => ['required', 'integer', 'in:1,2'],
            'jumlah_diserahkan' => ['required', 'numeric', 'gt:0'],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        $tanggal = (string) $data['tanggal'];
        $shift = (int) $data['shift'];

        $query = Transaksi::where('id_kasir', Auth::id())
            ->whereDate('tanggal', $tanggal)
            ->where('shift', $shift);

        $total = (float) (clone $query)->sum('total');
        $tunai = (float) (clone $query)->where('metode_pembayaran', 'cash')->sum('total');
        $qris = (float) (clone $query)->where('metode_pembayaran', 'qris')->sum('total');

        if ($total <= 0) {
            return back()->withErrors(['jumlah_diserahkan' => 'Belum ada transaksi pada shift ini, jadi belum ada yang bisa ditutup.'])->withInput();
        }

        $sudahAda = TutupBukuShift::where('tanggal', $tanggal)
            ->where('shift', $shift)
            ->where('id_kasir', Auth::id())
            ->exists();
        if ($sudahAda) {
            return back()->withErrors(['jumlah_diserahkan' => 'Shift ini sudah ditutup.'])->withInput();
        }

        $diserahkan = (float) $data['jumlah_diserahkan'];
        $sisa = $total - $diserahkan;

        if ($diserahkan >= $total) {
            return back()->withErrors(['jumlah_diserahkan' => 'Jumlah yang diserahkan harus kurang dari total pendapatan, supaya ada sisa untuk saldo awal.'])->withInput();
        }
        if ($sisa > $tunai) {
            return back()->withErrors(['jumlah_diserahkan' => 'Sisa tidak boleh melebihi uang tunai yang terkumpul (Rp' . number_format($tunai, 0, ',', '.') . '), karena sisa diberikan dalam bentuk tunai.'])->withInput();
        }

        TutupBukuShift::create([
            'tanggal' => $tanggal,
            'shift' => $shift,
            'id_kasir' => Auth::id(),
            'saldo_awal' => TutupBukuShift::saldoAwalUntuk($tanggal, $shift),
            'total_pendapatan' => $total,
            'total_tunai' => $tunai,
            'total_qris' => $qris,
            'jumlah_diserahkan' => $diserahkan,
            'saldo_lanjutan' => $sisa,
            'keterangan' => (string) $data['keterangan'],
            'status' => 'menunggu',
        ]);

        return redirect()->route('transaksi.rekap', ['tanggal' => $tanggal, 'shift' => $shift])
            ->with('sukses', 'Tutup buku shift diserahkan ke manajer. Sisa Rp' . number_format($sisa, 0, ',', '.') . ' menjadi saldo awal shift berikutnya.');
    }

    /**
     * Halaman persetujuan manajer (UC-10): yang menunggu dulu,
     * lalu riwayat yang sudah disetujui.
     */
    public function index(): View
    {
        return view('tutup-buku.index', [
            'menunggu' => TutupBukuShift::with('kasir')
                ->where('status', 'menunggu')
                ->orderByDesc('tanggal')->orderByDesc('shift')
                ->paginate(10, ['*'], 'menunggu_page'),
            'riwayat' => TutupBukuShift::with(['kasir', 'manajer'])
                ->where('status', 'disetujui')
                ->orderByDesc('tanggal')->orderByDesc('shift')
                ->paginate(10, ['*'], 'riwayat_page'),
        ]);
    }

    /**
     * Manajer menyetujui penerimaan uang (UC-10). Tidak ada jalur
     * penolakan: rekap berasal dari sistem, jadi manajer menerima.
     */
    public function setujui(TutupBukuShift $tutupBuku): RedirectResponse
    {
        $tutupBuku->update([
            'status' => 'disetujui',
            'id_manajer' => Auth::id(),
            'disetujui_pada' => now(),
        ]);

        return redirect()->route('tutup-buku.index')
            ->with('sukses', 'Tutup buku Shift ' . $tutupBuku->shift . ' tanggal ' . $tutupBuku->tanggal->format('d-m-Y') . ' disetujui.');
    }
}
