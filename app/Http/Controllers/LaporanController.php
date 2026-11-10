<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pengguna;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Laporan penjualan (UC-07): filter rentang tanggal, kasir dan
     * shift, ringkasan jumlah transaksi + total pendapatan, rekapitulasi
     * per periode (harian/mingguan/bulanan/tahunan), dan daftar transaksi.
     */
    public function index(Request $request): View
    {
        [$mulai, $sampai, $kasirDipilih, $shiftDipilih, $periode, $query] = $this->filterDasar($request);
        $ukuran = $this->ukuranKertas($request);

        return view('laporan.index', [
            'mulai' => $mulai,
            'sampai' => $sampai,
            'kasirList' => Pengguna::where('peran', 'kasir')->orderBy('nama')->get(),
            'kasirDipilih' => $kasirDipilih,
            'shiftDipilih' => $shiftDipilih,
            'periode' => $periode,
            'periodeList' => $this->periodeList(),
            'ukuran' => $ukuran,
            'ukuranList' => $this->ukuranList(),
            'totalTransaksi' => (clone $query)->count(),
            'totalPendapatan' => (clone $query)->sum('total'),
            'rekap' => $this->rekapitulasi($query, $periode),
            'transaksi' => $query->orderByDesc('tanggal')->orderByDesc('id')
                ->paginate(10)
                ->withQueryString(),
            'stok' => Barang::orderBy('nama')->get(),
        ]);
    }

    /**
     * Cetak laporan shift per kasir: rekapitulasi per periode yang
     * dipilih, mengikuti filter tanggal, kasir, dan shift yang sama
     * dengan halaman Laporan.
     */
    public function cetak(Request $request): View
    {
           [$mulai, $sampai, $kasirDipilih, $shiftDipilih, $periode, $query] = $this->filterDasar($request);
        $ukuran = $this->ukuranKertas($request);

        $kasir = $kasirDipilih !== 0 ? Pengguna::find($kasirDipilih) : null;

        return view('laporan.cetak', [
            'mulai' => $mulai,
            'sampai' => $sampai,
            'mulaiLabel' => Carbon::parse($mulai)->format('d-m-Y'),
            'sampaiLabel' => Carbon::parse($sampai)->format('d-m-Y'),
            'kasirDipilih' => $kasirDipilih,
            'shiftDipilih' => $shiftDipilih,
            'periode' => $periode,
                        'periodeLabel' => $this->periodeList()[$periode],
            'ukuran' => $ukuran,
            'ukuranList' => $this->ukuranList(),
            'kasirNama' => $kasir
                ? (string) $kasir->nama
                : ($kasirDipilih !== 0 ? 'Kasir tidak ditemukan' : 'Semua Kasir'),
            'shiftLabel' => $shiftDipilih === 1
                ? 'Shift 1'
                : ($shiftDipilih === 2 ? 'Shift 2' : 'Semua Shift'),
            'rekap' => $this->rekapitulasi($query, $periode),
            'totalTransaksi' => (clone $query)->count(),
            'totalPendapatan' => (clone $query)->sum('total'),
            'totalTunai' => (clone $query)->where('metode_pembayaran', 'cash')->sum('total'),
            'totalQris' => (clone $query)->where('metode_pembayaran', 'qris')->sum('total'),
            'dicetakOleh' => (string) $request->user()->nama,
            'tanggalCetak' => now()->format('d-m-Y H:i'),
        ]);
    }

    /**
     * Pilihan periode rekapitulasi.
     */
    private function periodeList(): array
    {
        return [
            'harian' => 'Harian',
            'mingguan' => 'Mingguan',
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
        ];
    }

        /**
     * Pilihan ukuran kertas cetak.
     */
    private function ukuranList(): array
    {
        return [
            'a4' => 'A4',
            'a5' => 'A5',
            'f4' => 'F4 / Folio',
            'thermal80' => 'Struk 80mm',
        ];
    }

    /**
     * Ukuran kertas dari query, default A4. Nilai di luar daftar
     * dikembalikan ke A4 supaya @page selalu valid.
     */
    private function ukuranKertas(Request $request): string
    {
        $ukuran = (string) $request->query('ukuran', 'a4');

        return array_key_exists($ukuran, $this->ukuranList()) ? $ukuran : 'a4';
    }
    /**
     * Filter dasar yang dipakai index() dan cetak(): mengembalikan
     * nilai filter dan query transaksi yang sudah difilter.
     */
    private function filterDasar(Request $request): array
    {
        $mulai = $request->filled('mulai')
            ? (string) $request->string('mulai')
            : now()->subDays(30)->format('Y-m-d');
        $sampai = $request->filled('sampai')
            ? (string) $request->string('sampai')
            : now()->format('Y-m-d');

        $kasirDipilih = $request->filled('kasir')
            ? (int) $request->integer('kasir')
            : 0;
        $shiftDipilih = $request->filled('shift')
            ? (int) $request->integer('shift')
            : 0;
        $periode = (string) $request->string('periode', 'harian');
        if (! array_key_exists($periode, $this->periodeList())) {
            $periode = 'harian';
        }

        $query = Transaksi::with('kasir', 'detailTransaksi')
            ->whereDate('tanggal', '>=', $mulai)
            ->whereDate('tanggal', '<=', $sampai);

        if ($kasirDipilih !== 0) {
            $query->where('id_kasir', $kasirDipilih);
        }
        if ($shiftDipilih === 1 || $shiftDipilih === 2) {
            $query->where('shift', $shiftDipilih);
        }

        return [$mulai, $sampai, $kasirDipilih, $shiftDipilih, $periode, $query];
    }

    /**
     * Rekapitulasi transaksi per periode: tiap baris berisi label
     * periode, jumlah transaksi, total pendapatan, total tunai, dan
     * total QRIS. Urut kronologis.
     */
    private function rekapitulasi(Builder $query, string $periode): Collection
    {
        $kelompok = match ($periode) {
            'mingguan' => "YEARWEEK(tanggal, 1)",
            'bulanan' => "DATE_FORMAT(tanggal, '%Y-%m')",
            'tahunan' => "YEAR(tanggal)",
            default => "DATE(tanggal)",
        };

        $baris = (clone $query)
            ->setEagerLoads([])
            ->selectRaw("{$kelompok} as kunci")
            ->selectRaw('COUNT(*) as jumlah_transaksi')
            ->selectRaw('SUM(total) as total_pendapatan')
            ->selectRaw("SUM(CASE WHEN metode_pembayaran = 'cash' THEN total ELSE 0 END) as total_tunai")
            ->selectRaw("SUM(CASE WHEN metode_pembayaran = 'qris' THEN total ELSE 0 END) as total_qris")
            ->groupBy('kunci')
            ->orderBy('kunci')
            ->get();

        return $baris->map(function ($row) use ($periode) {
            return [
                'label' => $this->labelPeriode($periode, (string) $row->kunci),
                'jumlah_transaksi' => (int) $row->jumlah_transaksi,
                'total_pendapatan' => (float) $row->total_pendapatan,
                'total_tunai' => (float) $row->total_tunai,
                'total_qris' => (float) $row->total_qris,
            ];
        });
    }

    /**
     * Label baris rekap berdasarkan kunci kelompoknya.
     */
    private function labelPeriode(string $periode, string $kunci): string
    {
        return match ($periode) {
            'mingguan' => $this->labelMinggu($kunci),
            'bulanan' => $this->labelBulan($kunci),
            'tahunan' => $kunci,
            default => Carbon::parse($kunci)->format('d-m-Y'),
        };
    }

    /**
     * Kunci YEARWEEK '202645' -> '02-11-2026 s.d. 08-11-2026'
     * (Senin s.d. Minggu minggu ISO tersebut).
     */
    private function labelMinggu(string $kunci): string
    {
        $tahun = (int) substr($kunci, 0, 4);
        $minggu = (int) substr($kunci, 4, 2);
        $senin = Carbon::now()->setISODate($tahun, $minggu)->startOfDay();
        $mingguAkhir = $senin->copy()->addDays(6);

        return $senin->format('d-m-Y') . ' s.d. ' . $mingguAkhir->format('d-m-Y');
    }

    /**
     * Kunci '2026-11' -> 'November 2026'.
     */
    private function labelBulan(string $kunci): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        [$tahun, $bulan] = explode('-', $kunci);

        return ($namaBulan[(int) $bulan] ?? $bulan) . ' ' . $tahun;
    }
}
