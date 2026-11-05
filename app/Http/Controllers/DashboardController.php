<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\DetailTransaksi;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{

    public function __invoke(): View
    {
        $pengguna = Auth::user();
        $stats = [];

        if ($pengguna->peran === 'kasir') {
            $transaksiHariIni = Transaksi::where('id_kasir', $pengguna->id)
                ->whereDate('tanggal', today());
            $stats[] = ['label' => 'Transaksi hari ini', 'nilai' => $transaksiHariIni->count() . ' transaksi', 'url' => route('transaksi.index')];
            $stats[] = ['label' => 'Pendapatan hari ini', 'nilai' => 'Rp' . number_format($transaksiHariIni->sum('total'), 0, ',', '.'), 'url' => route('transaksi.index')];
        } else {
            $barang = Barang::all();
            $stats[] = ['label' => 'Total barang', 'nilai' => $barang->count() . ' barang', 'url' => route('barang.index')];
            $stats[] = ['label' => 'Stok menipis', 'nilai' => $barang->filter(fn ($b) => $b->cekStok() === 'menipis')->count() . ' barang', 'url' => route('barang.index', ['status' => 'menipis'])];
            $stats[] = ['label' => 'Stok kosong', 'nilai' => $barang->filter(fn ($b) => $b->cekStok() === 'kosong')->count() . ' barang', 'url' => route('barang.index', ['status' => 'kosong'])];
            $stats[] = ['label' => 'Mendekati kedaluwarsa', 'nilai' => $barang->filter(fn ($b) => $b->cekKedaluarsa())->count() . ' barang', 'url' => route('barang.index', ['status' => 'kedaluwarsa'])];
            $stats[] = ['label' => 'Notifikasi belum dibaca', 'nilai' => Notifikasi::where('status_baca', false)->count() . ' notifikasi', 'url' => route('notifikasi.index', ['belum_dibaca' => '1'])];
            if ($pengguna->peran === 'manajer') {
                $transaksiHariIni = Transaksi::whereDate('tanggal', today());
                $stats[] = ['label' => 'Transaksi hari ini', 'nilai' => $transaksiHariIni->count() . ' transaksi', 'url' => route('laporan.index')];
                $stats[] = ['label' => 'Pendapatan hari ini', 'nilai' => 'Rp' . number_format($transaksiHariIni->sum('total'), 0, ',', '.'), 'url' => route('laporan.index')];
            }
        }

        $periode = request('periode', 'harian');
        if (! in_array($periode, ['harian', 'mingguan', 'bulanan', 'tahunan'], true)) {
            $periode = 'harian';
        }
        $grafik = $this->grafikDashboard($pengguna, $periode);
        $hariIni = $this->dataHariIni($pengguna);

        return view('dashboard', [
            'pengguna' => $pengguna,
            'stats' => $stats,
            'grafik' => $grafik,
            'hariIni' => $hariIni,
        ]);
    }


    public function hariIni()
    {
        $data = $this->dataHariIni(Auth::user());

        return response()->json($data ?? ['labels' => [], 'transaksi' => [], 'pendapatan' => []]);
    }


    private function formatTanggalPanjang(Carbon $tanggal): string
    {
        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $tanggal->day . ' ' . $namaBulan[$tanggal->month - 1] . ' ' . $tanggal->year;
    }


    private function bucketKalender(string $periode): array
    {
        $hariIni = today();
        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $singkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        if ($periode === 'harian') {
            $bucket = [];
            for ($jam = 7; $jam <= 21; $jam++) {
                $bucket[] = [
                    'mulai' => $hariIni->copy()->setTime($jam, 0, 0),
                    'selesai' => $hariIni->copy()->setTime($jam, 59, 59),
                    'label' => sprintf('%02d.00', $jam),
                ];
            }
            $judul = 'Hari ini, ' . $namaHari[$hariIni->dayOfWeek] . ', ' . $this->formatTanggalPanjang($hariIni);
        } elseif ($periode === 'mingguan') {
            $senin = $hariIni->copy()->startOfWeek(Carbon::MONDAY);
            $minggu = $senin->copy()->addDays(6);
            $bucket = [];
            for ($i = 0; $i < 7; $i++) {
                $d = $senin->copy()->addDays($i);
                $bucket[] = [
                    'mulai' => $d->copy()->startOfDay(),
                    'selesai' => $d->copy()->endOfDay(),
                    'label' => $namaHari[$d->dayOfWeek] . ' ' . $d->day . ' ' . $singkat[$d->month - 1],
                ];
            }
            $judul = 'Minggu ini, ' . $this->formatTanggalPanjang($senin) . ' – ' . $this->formatTanggalPanjang($minggu);
        } elseif ($periode === 'bulanan') {
            $bucket = [];
            for ($tgl = 1; $tgl <= $hariIni->daysInMonth; $tgl++) {
                $d = $hariIni->copy()->day($tgl);
                $bucket[] = [
                    'mulai' => $d->copy()->startOfDay(),
                    'selesai' => $d->copy()->endOfDay(),
                    'label' => $tgl . ' ' . $singkat[$hariIni->month - 1],
                ];
            }
            $judul = $namaBulan[$hariIni->month - 1] . ' ' . $hariIni->year;
        } else { // tahunan
            $bucket = [];
            for ($bln = 1; $bln <= 12; $bln++) {
                $d = $hariIni->copy()->month($bln)->startOfMonth();
                $bucket[] = [
                    'mulai' => $d->copy()->startOfMonth(),
                    'selesai' => $d->copy()->endOfMonth(),
                    'label' => $namaBulan[$bln - 1],
                ];
            }
            $judul = 'Tahun ' . $hariIni->year;
        }

        return ['bucket' => $bucket, 'judul' => $judul];
    }


    private function grafikDashboard($pengguna, string $periode): ?array
    {
        $info = $this->bucketKalender($periode);
        $bucket = $info['bucket'];
        $judul = $info['judul'];
        $mulai = $bucket[0]['mulai'];
        $selesai = $bucket[count($bucket) - 1]['selesai'];

        if ($pengguna->peran === 'gudang') {
            $semuaMasuk = BarangMasuk::whereBetween('tanggal', [$mulai, $selesai])->get(['tanggal', 'jumlah']);
            $semuaKeluar = DetailTransaksi::whereHas('transaksi', function ($q) use ($mulai, $selesai) {
                $q->whereBetween('tanggal', [$mulai, $selesai]);
            })->with('transaksi:id,tanggal')->get();

            $masuk = [];
            $keluar = [];
            foreach ($bucket as $b) {
                $masuk[] = (int) $semuaMasuk->filter(fn ($m) => $m->tanggal >= $b['mulai'] && $m->tanggal <= $b['selesai'])->sum('jumlah');
                $keluar[] = (int) $semuaKeluar->filter(function ($d) use ($b) {
                    $t = $d->transaksi?->tanggal;

                    return $t && $t >= $b['mulai'] && $t <= $b['selesai'];
                })->sum('jumlah');
            }

            return [
                'periode' => $periode,
                'judul' => $judul,
                'jenis' => 'arus',
                'labels' => array_column($bucket, 'label'),
                'masuk' => $masuk,
                'keluar' => $keluar,
            ];
        }

        if (! in_array($pengguna->peran, ['kasir', 'manajer'], true)) {
            return null;
        }

        $query = Transaksi::whereBetween('tanggal', [$mulai, $selesai]);
        if ($pengguna->peran === 'kasir') {
            $query->where('id_kasir', $pengguna->id);
        }
        $semua = $query->get(['tanggal', 'total']);

        $jumlah = [];
        $pendapatan = [];
        foreach ($bucket as $b) {
            $diBucket = $semua->filter(fn ($t) => $t->tanggal >= $b['mulai'] && $t->tanggal <= $b['selesai']);
            $jumlah[] = $diBucket->count();
            $pendapatan[] = (float) $diBucket->sum('total');
        }

        return [
            'periode' => $periode,
            'judul' => $judul,
            'jenis' => 'penjualan',
            'labels' => array_column($bucket, 'label'),
            'transaksi' => $jumlah,
            'pendapatan' => $pendapatan,
        ];
    }


    private function dataHariIni($pengguna): ?array
    {
        if (! in_array($pengguna->peran, ['kasir', 'manajer'], true)) {
            return null;
        }

        $query = Transaksi::whereDate('tanggal', today());
        if ($pengguna->peran === 'kasir') {
            $query->where('id_kasir', $pengguna->id);
        }
        $semua = $query->get(['tanggal', 'total']);

        $labels = [];
        $transaksi = [];
        $pendapatan = [];
        for ($jam = 7; $jam <= 21; $jam++) {
            $mulai = today()->copy()->setTime($jam, 0, 0);
            $selesai = today()->copy()->setTime($jam, 59, 59);
            $diJam = $semua->filter(fn ($t) => $t->tanggal >= $mulai && $t->tanggal <= $selesai);
            $labels[] = sprintf('%02d.00', $jam);
            $transaksi[] = $diJam->count();
            $pendapatan[] = (float) $diJam->sum('total');
        }

        return ['labels' => $labels, 'transaksi' => $transaksi, 'pendapatan' => $pendapatan];
    }
}
