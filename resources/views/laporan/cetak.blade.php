<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Shift Per Kasir — {{ $periodeLabel }}</title>
    <style>
        @if ($ukuran === 'a5')
            @page {
                size: A5;
                margin: 12mm;
            }

            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                color: #000;
                margin: 18px;
            }

            .kertas {
                max-width: 480px;
                margin: 0 auto;
            }
        @elseif($ukuran === 'f4')
            @page {
                size: 210mm 330mm;
                margin: 15mm;
            }

            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #000;
                margin: 24px;
            }

            .kertas {
                max-width: 900px;
                margin: 0 auto;
            }
        @elseif($ukuran === 'thermal80')
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                color: #000;
                margin: 0;
            }

            .kertas {
                max-width: 72mm;
                margin: 0 auto;
                padding: 2mm;
            }
        @else
            @page {
                size: A4;
                margin: 15mm;
            }

            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #000;
                margin: 24px;
            }

            .kertas {
                max-width: 900px;
                margin: 0 auto;
            }
        @endif

        h1 {
            font-size: 18px;
            margin: 0;
        }

        h2 {
            font-size: 14px;
            margin: 2px 0 0;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .info {
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .info td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info td.label {
            width: 110px;
        }

        table.rekap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.rekap th,
        table.rekap td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        table.rekap th {
            background: #f0f0f0;
            text-align: left;
        }

        table.rekap td.angka,
        table.rekap th.angka {
            text-align: right;
        }

        tfoot td {
            font-weight: bold;
        }

        .ttd {
            margin-top: 36px;
            width: 100%;
        }

        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .nama-terang {
            margin-top: 56px;
        }

        .kaki {
            margin-top: 24px;
            font-size: 10px;
            color: #555;
            text-align: right;
        }

        @if ($ukuran === 'thermal80')
            h1 {
                font-size: 13px;
            }

            h2 {
                font-size: 11px;
            }

            .info td.label {
                width: 52px;
            }

            .blok {
                border-bottom: 1px dashed #000;
                padding: 6px 0;
            }

            .blok-judul {
                font-weight: bold;
            }

            .baris {
                display: flex;
                justify-content: space-between;
            }

            .ttd {
                margin-top: 24px;
            }

            .ttd td {
                display: block;
                width: 100%;
                padding: 8px 0;
            }

            .nama-terang {
                margin-top: 36px;
            }

            .kaki {
                font-size: 9px;
            }
        @endif

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="kertas">
        <div class="kop">
            <h1>KOPERASI DESA MERAH PUTIH PULO LOR</h1>
            <h2>Laporan Shift Per Kasir — Rekapitulasi {{ $periodeLabel }}</h2>
        </div>

        <table class="info">
            <tr>
                <td class="label">Periode</td>
                <td>: {{ $periodeLabel }} ({{ $mulaiLabel }} s.d. {{ $sampaiLabel }})</td>
            </tr>
            <tr>
                <td class="label">Kasir</td>
                <td>: {{ $kasirNama }}</td>
            </tr>
            <tr>
                <td class="label">Shift</td>
                <td>: {{ $shiftLabel }}</td>
            </tr>
        </table>

        @if ($ukuran === 'thermal80')
            @forelse ($rekap as $r)
                <div class="blok">
                    <div class="blok-judul">{{ $r['label'] }}</div>
                    <div class="baris"><span>Transaksi</span><span>{{ $r['jumlah_transaksi'] }}</span></div>
                    <div class="baris">
                        <span>Tunai</span><span>Rp{{ number_format($r['total_tunai'], 0, ',', '.') }}</span>
                    </div>
                    <div class="baris">
                        <span>QRIS</span><span>Rp{{ number_format($r['total_qris'], 0, ',', '.') }}</span>
                    </div>
                    <div class="baris">
                        <span>Total</span><span>Rp{{ number_format($r['total_pendapatan'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <p style="text-align:center;">Tidak ada transaksi pada rentang ini.</p>
            @endforelse
            <div class="blok">
                <div class="blok-judul">Total</div>
                <div class="baris"><span>Transaksi</span><span>{{ $totalTransaksi }}</span></div>
                <div class="baris"><span>Tunai</span><span>Rp{{ number_format($totalTunai, 0, ',', '.') }}</span>
                </div>
                <div class="baris"><span>QRIS</span><span>Rp{{ number_format($totalQris, 0, ',', '.') }}</span></div>
                <div class="baris"><span>Total</span><span>Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                </div>
            </div>
        @else
            <table class="rekap">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th class="angka">Jumlah Transaksi</th>
                        <th class="angka">Tunai (Rp)</th>
                        <th class="angka">QRIS (Rp)</th>
                        <th class="angka">Total Pendapatan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekap as $r)
                        <tr>
                            <td>{{ $r['label'] }}</td>
                            <td class="angka">{{ $r['jumlah_transaksi'] }}</td>
                            <td class="angka">{{ number_format($r['total_tunai'], 0, ',', '.') }}</td>
                            <td class="angka">{{ number_format($r['total_qris'], 0, ',', '.') }}</td>
                            <td class="angka">{{ number_format($r['total_pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;">Tidak ada transaksi pada rentang ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td class="angka">{{ $totalTransaksi }}</td>
                        <td class="angka">{{ number_format($totalTunai, 0, ',', '.') }}</td>
                        <td class="angka">{{ number_format($totalQris, 0, ',', '.') }}</td>
                        <td class="angka">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <table class="ttd">
            <tr>
                <td>
                    Mengetahui,<br>Ketua Koperasi
                    <div class="nama-terang">( ............................ )</div>
                </td>
                <td>
                    Pulo Lor, {{ $sampaiLabel }}<br>Dibuat oleh,
                    <div class="nama-terang">( {{ $dicetakOleh }} )</div>
                </td>
            </tr>
        </table>

        <p class="kaki">Dicetak pada {{ $tanggalCetak }} WIB</p>
    </div>
</body>

</html>
