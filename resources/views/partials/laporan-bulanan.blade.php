{{-- Partial Tampilan Grafik Penjualan & Barang Paling Laris --}}

{{-- 1. Kartu Ringkasan Statistik --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5 print:grid-cols-4 print:gap-2">
    {{-- Card 1: Total Omset --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                Total Penjualan {{ $isFilterBulan ? 'Bulan ' . $namaBulanTerpilih : 'Tahun ' . $tahun }}
            </p>
            <p class="text-lg sm:text-xl font-black text-blue-600 mt-1">Rp {{ number_format($totalTahunanNominal ?? 0, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">
                Rata-rata: Rp {{ number_format($rataRataBulanan ?? 0, 0, ',', '.') }}/{{ $isFilterBulan ? 'hari aktif' : 'bulan aktif' }}
            </p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 print:hidden">
            @include('components.icon', ['name' => 'dollar', 'size' => 22])
        </div>
    </div>

    {{-- Card 2: Total Transaksi --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Transaksi</p>
            <p class="text-lg sm:text-xl font-black text-gray-900 mt-1">{{ number_format($totalTahunanTransaksi ?? 0, 0, ',', '.') }}</p>
            <p class="text-[10px] text-emerald-600 font-medium mt-0.5">Penjualan Selesai</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 print:hidden">
            @include('components.icon', ['name' => 'transaksi', 'size' => 22])
        </div>
    </div>

    {{-- Card 3: Total Suku Cadang Terjual --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Barang Terjual</p>
            <p class="text-lg sm:text-xl font-black text-purple-600 mt-1">{{ number_format($totalTahunanQty ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold text-gray-500">Unit</span></p>
            <p class="text-[10px] text-purple-600 font-medium mt-0.5">Suku Cadang & Produk Fisik</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 print:hidden">
            @include('components.icon', ['name' => 'box', 'size' => 22])
        </div>
    </div>

    {{-- Card 4: Produk Paling Laris --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between">
        <div class="min-w-0 pr-2">
            <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">
                Produk Terlaris {{ $isFilterBulan ? 'Bulan ' . $namaBulanTerpilih : 'Tahun ' . $tahun }}
            </p>
            <p class="text-sm font-bold text-gray-900 mt-1 truncate" title="{{ $juaraTahunan->nama_item ?? 'Belum ada' }}">
                {{ $juaraTahunan->nama_item ?? 'Belum ada data' }}
            </p>
            <p class="text-[10px] text-gray-500 mt-0.5">
                @if($juaraTahunan)
                    Terjual <strong class="text-amber-700 font-bold">{{ $juaraTahunan->total_terjual }}</strong> unit (Rp {{ number_format($juaraTahunan->total_nominal, 0, ',', '.') }})
                @else
                    Belum ada transaksi
                @endif
            </p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 print:hidden">
            @include('components.icon', ['name' => 'box', 'size' => 22])
        </div>
    </div>
</div>

{{-- 2. Grafik Penjualan (Harian jika filter bulan, Bulanan jika 1 tahun) --}}
<div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2 mb-4">
        <div>
            <h3 class="text-sm sm:text-base font-bold text-gray-900">
                @if($isFilterBulan)
                    Grafik Penjualan Harian - Bulan {{ $namaBulanTerpilih }} {{ $tahun }}
                @else
                    Grafik Penjualan Bulanan - Tahun {{ $tahun }}
                @endif
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
                @if($isFilterBulan)
                    Pergerakan omset pendapatan (Rp) dan jumlah transaksi dari tanggal 1 sampai akhir bulan {{ $namaBulanTerpilih }}
                @else
                    Perbandingan omset pendapatan (Rp) dan jumlah transaksi dari Januari sampai Desember
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-600 bg-blue-50/80 px-2.5 py-1 rounded-lg border border-blue-100">
                <span>Omset Penjualan (Rp)</span>
            </span>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-gray-600 bg-emerald-50/80 px-2.5 py-1 rounded-lg border border-emerald-100">
                <span>Jumlah Transaksi</span>
            </span>
        </div>
    </div>

    {{-- Canvas Chart.js --}}
    <div class="relative w-full h-72 sm:h-80">
        <canvas id="chartPenjualanBulanan"></canvas>
    </div>
</div>

{{-- 3. Grafik Barang Paling Laris (Menampilkan nama produk langsung) --}}
<div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2 mb-4">
        <div>
            <h3 class="text-sm sm:text-base font-bold text-gray-900">
                @if($isFilterBulan)
                    Grafik Barang Paling Laris - Bulan {{ $namaBulanTerpilih }} {{ $tahun }}
                @else
                    Grafik Barang Paling Laris - Tahun {{ $tahun }}
                @endif
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Urutan suku cadang / produk fisik dengan jumlah unit penjualan tertinggi
            </p>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-900 bg-amber-50 px-3 py-1 rounded-lg border border-amber-200/80">
                <span>Unit Terjual (Pcs)</span>
            </span>
        </div>
    </div>

    @if(!empty($chartLarisLabels) && count($chartLarisLabels) > 0)
        {{-- Canvas Chart.js untuk Barang Terlaris --}}
        <div class="relative w-full" style="height: {{ max(280, count($chartLarisLabels) * 42) }}px;">
            <canvas id="chartBarangLarisBulanan"></canvas>
        </div>
    @else
        <div class="py-12 text-center text-gray-400 text-xs">
            Belum ada data barang terjual pada periode {{ $isFilterBulan ? 'bulan ' . $namaBulanTerpilih . ' ' . $tahun : 'tahun ' . $tahun }}
        </div>
    @endif
</div>

{{-- 4. Tabel Daftar Top 10 Barang Terlaris --}}
<div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs mb-6">
    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
        <div>
            <h4 class="text-sm font-bold text-gray-900">
                Daftar Top 10 Produk Terlaris {{ $isFilterBulan ? 'Bulan ' . $namaBulanTerpilih : 'Tahun ' . $tahun }}
            </h4>
        </div>
        <span class="text-xs font-semibold text-gray-500">
            Total {{ count($topBarangTahunan ?? []) }} Produk
        </span>
    </div>

    @if($topBarangTahunan && $topBarangTahunan->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 uppercase font-semibold text-[11px]">
                        <th class="py-2.5 px-3 w-12 text-center">No</th>
                        <th class="py-2.5 px-3">Nama Produk / Suku Cadang</th>
                        <th class="py-2.5 px-3 text-right">Total Terjual</th>
                        <th class="py-2.5 px-3 text-right">Total Omset</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($topBarangTahunan as $index => $item)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-2.5 px-3 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs font-bold">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-gray-900">
                                {{ $item->nama_item }}
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold">
                                    {{ number_format($item->total_terjual, 0, ',', '.') }} pcs
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right font-medium text-gray-900">
                                Rp {{ number_format($item->total_nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-8 text-center text-gray-400 text-xs">
            Belum ada data barang terjual pada periode {{ $isFilterBulan ? 'bulan ' . $namaBulanTerpilih . ' ' . $tahun : 'tahun ' . $tahun }}
        </div>
    @endif
</div>

{{-- Script Khusus Chart.js untuk Grafik Bulanan --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Grafik Penjualan (Harian / Bulanan)
            const ctx = document.getElementById('chartPenjualanBulanan');
            if (ctx) {
                const labels = @json($chartLabels);
                const dataNominal = @json($chartNominal);
                const dataTransaksi = @json($chartTransaksi);

                const formatRupiah = (value) => {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                };

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                type: 'line',
                                label: 'Jumlah Transaksi',
                                data: dataTransaksi,
                                borderColor: '#10b981',
                                backgroundColor: '#10b981',
                                borderWidth: 2.5,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#10b981',
                                pointBorderWidth: 2,
                                tension: 0.35,
                                yAxisID: 'y1',
                                order: 1,
                            },
                            {
                                type: 'bar',
                                label: 'Omset Penjualan (Rp)',
                                data: dataNominal,
                                backgroundColor: 'rgba(37, 99, 235, 0.85)',
                                hoverBackgroundColor: 'rgba(29, 78, 216, 1)',
                                borderRadius: 8,
                                borderSkipped: false,
                                yAxisID: 'y',
                                order: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#f8fafc',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                boxPadding: 4,
                                usePointStyle: true,
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.dataset.yAxisID === 'y') {
                                            label += formatRupiah(context.parsed.y);
                                        } else {
                                            label += context.parsed.y + ' Transaksi';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11,
                                        weight: 500
                                    },
                                    color: '#64748b'
                                }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 10
                                    },
                                    color: '#64748b',
                                    callback: function (value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000) + ' Jt';
                                        } else if (value >= 1000) {
                                            return (value / 1000) + ' Rb';
                                        }
                                        return value;
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 10
                                    },
                                    color: '#10b981',
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // 2. Grafik Barang Paling Laris (Horizontal Bar Chart dengan Nama Produk di Sumbu Y)
            const ctxLaris = document.getElementById('chartBarangLarisBulanan');
            if (ctxLaris) {
                const labelsLaris = @json($chartLarisLabels);
                const dataLarisQty = @json($chartLarisQty);
                const dataLarisNominal = @json($chartLarisNominal);

                const formatRupiah = (value) => {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                };

                new Chart(ctxLaris, {
                    type: 'bar',
                    data: {
                        labels: labelsLaris,
                        datasets: [
                            {
                                label: 'Unit Terjual (Pcs)',
                                data: dataLarisQty,
                                backgroundColor: 'rgba(245, 158, 11, 0.85)',
                                hoverBackgroundColor: 'rgba(217, 119, 6, 1)',
                                borderRadius: 6,
                                borderSkipped: false,
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y', // Tampilan horizontal agar nama produk terbaca jelas
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#f8fafc',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 12,
                                boxPadding: 6,
                                callbacks: {
                                    title: function(context) {
                                        const idx = context[0].dataIndex;
                                        return (idx + 1) + '. ' + labelsLaris[idx];
                                    },
                                    label: function(context) {
                                        const idx = context.dataIndex;
                                        const qty = context.parsed.x;
                                        const nom = dataLarisNominal[idx] || 0;
                                        return [
                                            'Terjual: ' + qty + ' Pcs / Unit',
                                            'Total Omset: ' + formatRupiah(nom)
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { family: "'Inter', sans-serif", size: 10 },
                                    color: '#64748b',
                                    stepSize: 1,
                                    precision: 0,
                                    callback: function(value) {
                                        return value + ' pcs';
                                    }
                                }
                            },
                            y: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: "'Inter', sans-serif", size: 11, weight: 600 },
                                    color: '#334155',
                                    callback: function(val, index) {
                                        const label = labelsLaris[index] || '';
                                        return label.length > 32 ? label.substring(0, 30) + '...' : label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
