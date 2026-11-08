<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Shift Per Kasir — {{ $periodeLabel }}</title>
    <style>
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
