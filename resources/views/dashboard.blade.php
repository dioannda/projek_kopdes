@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if ($pengguna->peran === 'manajer')
        <h2 class="text-2xl font-bold">👨‍💼Dashboard Manajer</h2>
        <p class="text-stone-500 dark:text-stone-400 mb-6">Selamat datang, {{ $pengguna->nama }}. Ringkasan kondisi
            persediaan dan penjualan hari ini untuk pengambilan keputusan.</p>

        @php
            $gaya = [
                'Total barang' => ['bg-red-100', 'text-red-700', '📦'],
                'Stok menipis' => ['bg-amber-100', 'text-amber-700', '⚠️'],
                'Stok kosong' => ['bg-red-200', 'text-red-900', '⛔'],
                'Mendekati kedaluwarsa' => ['bg-stone-100', 'text-stone-600', '🕐'],
                'Notifikasi belum dibaca' => ['bg-orange-100', 'text-orange-700', '🔔'],
                'Transaksi hari ini' => ['bg-orange-100', 'text-orange-700', '🧾'],
                'Pendapatan hari ini' => ['bg-amber-100', 'text-amber-800', '💰'],
            ];
            $grupPenjualan = ['Transaksi hari ini', 'Pendapatan hari ini'];
        @endphp

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mb-3">📦Kondisi Persediaan
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (!in_array($stat['label'], $grupPenjualan, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}"
                        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 group hover:-translate-y-1 hover:shadow-lg hover:border-red-300 dark:hover:border-red-800 transition">
                        <span
                            class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl group-hover:scale-110 transition">{{ $g[2] }}</span>
                        <span>
                            <span
                                class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mt-6 mb-3">💰Penjualan Hari
            Ini</div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (in_array($stat['label'], $grupPenjualan, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}"
                        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 group hover:-translate-y-1 hover:shadow-lg hover:border-red-300 dark:hover:border-red-800 transition">
                        <span
                            class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl group-hover:scale-110 transition">{{ $g[2] }}</span>
                        <span>
                            <span
                                class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    @elseif ($pengguna->peran === 'gudang')
        <h2 class="text-2xl font-bold">🏭 Dashboard Gudang</h2>
        <p class="text-stone-500 dark:text-stone-400 mb-6">Selamat datang, {{ $pengguna->nama }}. Ringkasan kondisi stok
            gudang dan notifikasi yang belum dibaca.</p>

        @php
            $gaya = [
                'Total barang' => ['bg-red-100', 'text-red-700', '📦'],
                'Stok menipis' => ['bg-amber-100', 'text-amber-700', '⚠️'],
                'Stok kosong' => ['bg-red-200', 'text-red-900', '⛔'],
                'Mendekati kedaluwarsa' => ['bg-stone-100', 'text-stone-600', '🕐'],
                'Notifikasi belum dibaca' => ['bg-orange-100', 'text-orange-700', '🔔'],
            ];
            $grupNotifikasi = ['Notifikasi belum dibaca'];
        @endphp

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mb-3">📦Kondisi Stok</div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (!in_array($stat['label'], $grupNotifikasi, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}"
                        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 group hover:-translate-y-1 hover:shadow-lg hover:border-red-300 dark:hover:border-red-800 transition">
                        <span
                            class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl group-hover:scale-110 transition">{{ $g[2] }}</span>
                        <span>
                            <span
                                class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mt-6 mb-3">🔔Notifikasi
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @if (in_array($stat['label'], $grupNotifikasi, true))
                    @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                    <a href="{{ $stat['url'] }}"
                        class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 group hover:-translate-y-1 hover:shadow-lg hover:border-red-300 dark:hover:border-red-800 transition">
                        <span
                            class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl group-hover:scale-110 transition">{{ $g[2] }}</span>
                        <span>
                            <span
                                class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                            <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    @elseif ($pengguna->peran === 'kasir')
        <h2 class="text-2xl font-bold">🧾Dashboard Kasir</h2>
        <p class="text-stone-500 dark:text-stone-400 mb-6">Selamat datang, {{ $pengguna->nama }}. Ringkasan penjualan Anda
            hari ini.</p>

        @php
            $gaya = [
                'Transaksi hari ini' => ['bg-orange-100', 'text-orange-700', '🧾'],
                'Pendapatan hari ini' => ['bg-amber-100', 'text-amber-800', '💰'],
            ];
        @endphp

        <div class="text-sm font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400 mb-3">💰Penjualan Hari Ini
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($stats as $stat)
                @php $g = $gaya[$stat['label']] ?? ['bg-stone-100', 'text-stone-600', '📋']; @endphp
                <a href="{{ $stat['url'] }}"
                    class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-4 flex items-center gap-4 group hover:-translate-y-1 hover:shadow-lg hover:border-red-300 dark:hover:border-red-800 transition">
                    <span
                        class="w-12 h-12 shrink-0 rounded-full {{ $g[0] }} {{ $g[1] }} flex items-center justify-center text-xl group-hover:scale-110 transition">{{ $g[2] }}</span>
                    <span>
                        <span
                            class="block text-xs font-bold uppercase tracking-wide text-stone-500 dark:text-stone-400">{{ $stat['label'] }}</span>
                        <span class="block text-2xl font-bold mt-1">{{ $stat['nilai'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    @else
        <div
            class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 max-w-[720px]">
            <h2 class="text-2xl font-bold">Selamat datang, {{ $pengguna->nama }}</h2>
            <p class="mt-2">Anda login sebagai <strong class="capitalize">{{ $pengguna->peran }}</strong>.</p>
        </div>

        <div class="flex gap-4 mt-4 flex-wrap">
            @foreach ($stats as $stat)
                <div
                    class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 flex-1 min-w-[160px] hover:-translate-y-0.5 hover:shadow-md transition">
                    <a href="{{ $stat['url'] }}" class="block">
                        <strong>{{ $stat['label'] }}</strong>
                        <p class="text-xl mt-2">{{ $stat['nilai'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if ($hariIni)
        <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 mt-6">
            <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-lg font-bold">🕐 Tren Penjualan Hari Ini</h3>
                    <p class="text-stone-500 dark:text-stone-400 text-sm">Per jam, 07.00–21.00 • diperbarui otomatis tiap 60
                        detik</p>
                </div>
                <p id="tren-diperbarui" class="text-xs text-stone-500 dark:text-stone-400"></p>
            </div>
            <div class="relative h-72">
                <canvas id="grafik-hari-ini"></canvas>
            </div>
            <p id="tren-catatan" class="text-sm text-stone-500 dark:text-stone-400 mt-3">
                @if (array_sum($hariIni['transaksi']) === 0)
                    Belum ada penjualan hari ini.
                @endif
            </p>
        </div>
    @endif

    @if ($grafik)
        <div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl shadow-sm p-5 mt-6">
            <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-lg font-bold">
                        {{ $grafik['jenis'] === 'arus' ? '📊Grafik Arus Barang' : '📈Grafik Penjualan' }}</h3>
                    <p class="text-stone-500 dark:text-stone-400 text-sm">{{ $grafik['judul'] }}</p>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2 items-center print:hidden">
                    <select name="periode"
                        class="px-2.5 py-2 border border-stone-300 dark:border-stone-700 rounded-lg bg-white dark:bg-stone-800 text-sm focus:outline-none focus:border-red-700 focus:ring-2 focus:ring-red-700/20 transition">
                        <option value="harian" {{ $grafik['periode'] === 'harian' ? 'selected' : '' }}>Per Hari</option>
                        <option value="mingguan" {{ $grafik['periode'] === 'mingguan' ? 'selected' : '' }}>Per Minggu
                        </option>
                        <option value="bulanan" {{ $grafik['periode'] === 'bulanan' ? 'selected' : '' }}>Per Bulan</option>
                        <option value="tahunan" {{ $grafik['periode'] === 'tahunan' ? 'selected' : '' }}>Per Tahun</option>
                    </select>
                    <button type="submit"
                        class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Tampilkan</button>
                </form>
            </div>
            <div class="relative h-80">
                <canvas id="grafik-dashboard"></canvas>
            </div>
            <p id="grafik-catatan" class="text-sm text-stone-500 dark:text-stone-400 mt-3">
                @if ($grafik['jenis'] === 'arus' && array_sum($grafik['masuk'] ?? []) + array_sum($grafik['keluar'] ?? []) === 0)
                    Belum ada arus barang pada rentang ini.
                @elseif ($grafik['jenis'] !== 'arus' && array_sum($grafik['transaksi'] ?? []) === 0)
                    Belum ada transaksi pada rentang ini.
                @endif
            </p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                var canvas = document.getElementById('grafik-dashboard');
                if (!canvas) return;
                var catatan = document.getElementById('grafik-catatan');
                if (!window.Chart) {
                    if (catatan) {
                        catatan.textContent = 'Grafik memerlukan koneksi internet (Chart.js CDN).';
                    }
                    return;
                }
                var jenis = @json($grafik['jenis']);
                var labels = @json($grafik['labels']);
                var datasets;
                var scales = {
                    x: {
                        ticks: {
                            color: '#78716c'
                        },
                        grid: {
                            color: 'rgba(120,113,108,0.15)'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#78716c'
                        },
                        grid: {
                            color: 'rgba(120,113,108,0.15)'
                        }
                    },
                };
                if (jenis === 'arus') {
                    datasets = [{
                            label: 'Barang masuk',
                            data: @json($grafik['masuk'] ?? []),
                            backgroundColor: '#b91c1c',
                            borderRadius: 6
                        },
                        {
                            label: 'Barang keluar',
                            data: @json($grafik['keluar'] ?? []),
                            backgroundColor: '#d97706',
                            borderRadius: 6
                        },
                    ];
                } else {
                    datasets = [{
                            type: 'bar',
                            label: 'Transaksi',
                            data: @json($grafik['transaksi'] ?? []),
                            backgroundColor: '#b91c1c',
                            borderRadius: 6
                        },
                        {
                            type: 'line',
                            label: 'Pendapatan (Rp)',
                            data: @json($grafik['pendapatan'] ?? []),
                            borderColor: '#d97706',
                            backgroundColor: '#d97706',
                            yAxisID: 'y1',
                            tension: 0.3
                        },
                    ];
                    scales.y1 = {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            color: '#78716c',
                            callback: function(v) {
                                return 'Rp' + v.toLocaleString('id-ID');
                            }
                        }
                    };
                }
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: scales,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#78716c'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        var v = ctx.parsed.y;
                                        if (ctx.dataset.yAxisID === 'y1') {
                                            return ' ' + ctx.dataset.label + ': Rp' + v.toLocaleString('id-ID');
                                        }
                                        return ' ' + ctx.dataset.label + ': ' + v;
                                    }
                                }
                            },
                        },
                    },
                });
            })();
        </script>
    @endif
    @if ($hariIni)
        <script>
            (function() {
                var canvas = document.getElementById('grafik-hari-ini');
                if (!canvas) return;
                var catatan = document.getElementById('tren-catatan');
                var diperbarui = document.getElementById('tren-diperbarui');
                if (!window.Chart) {
                    if (catatan) catatan.textContent = 'Grafik memerlukan koneksi internet (Chart.js CDN).';
                    return;
                }
                var labels = @json($hariIni['labels']);
                var transaksiPerJam = @json($hariIni['transaksi']);
                var chartTren = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: @json($hariIni['pendapatan']),
                            borderColor: '#b91c1c',
                            backgroundColor: 'rgba(185, 28, 28, 0.12)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                ticks: {
                                    color: '#78716c'
                                },
                                grid: {
                                    color: 'rgba(120,113,108,0.15)'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#78716c',
                                    callback: function(v) {
                                        return 'Rp' + v.toLocaleString('id-ID');
                                    }
                                },
                                grid: {
                                    color: 'rgba(120,113,108,0.15)'
                                }
                            },
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#78716c'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ' Pendapatan: Rp' + ctx.parsed.y.toLocaleString('id-ID') + ' (' +
                                            transaksiPerJam[ctx.dataIndex] + ' transaksi)';
                                    }
                                }
                            },
                        },
                    },
                });

                function tandaiDiperbarui() {
                    if (diperbarui) diperbarui.textContent = 'Terakhir diperbarui ' + new Date().toLocaleTimeString(
                        'id-ID');
                }
                tandaiDiperbarui();
                setInterval(function() {
                    fetch('{{ route('dashboard.hari-ini') }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(data) {
                            chartTren.data.datasets[0].data = data.pendapatan;
                            transaksiPerJam = data.transaksi;
                            chartTren.update();
                            tandaiDiperbarui();
                        })
                        .catch(function() {
                            if (diperbarui) diperbarui.textContent =
                                'Gagal memperbarui otomatis, refresh halaman untuk data terbaru.';
                        });
                }, 60000);
            })();
        </script>
    @endif
@endsection

@php
    $sekarang = today();
    $namaHariPanjang = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $namaBulanPanjang = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];
@endphp

<script>
    function perbaruiJam() {
        var el = document.getElementById('jam-realtime');
        if (el) {
            el.textContent = new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }
    perbaruiJam();
    setInterval(perbaruiJam, 1000);
</script>
