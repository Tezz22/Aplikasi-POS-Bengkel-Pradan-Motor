@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    {{-- Page Header --}}
    <x-page-header 
        title="Dashboard" 
        :subtitle="'Ringkasan performa bengkel, ' . date('l, d F Y')" 
        icon="dashboard" 
    />

    {{-- Top 4 Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-5">
        <x-stat-card 
            label="PENDAPATAN (LUNAS)" 
            :value="'Rp ' . number_format($pendapatanHariIni, 0, ',', '.')" 
            icon="dollar" 
            color="emerald" 
            sub="Total penjualan" 
        />
        <x-stat-card 
            label="TOTAL TRANSAKSI" 
            :value="(string)$totalTransaksiHariIni" 
            icon="refresh" 
            color="blue" 
            sub="Transaksi tercatat" 
        />
        <x-stat-card 
            label="PRODUK TERJUAL" 
            :value="(string)$produkTerjual" 
            icon="bag" 
            color="purple" 
            sub="Oli, ban, & sparepart" 
        />
        <x-stat-card 
            label="RATA-RATA PENDAPATAN" 
            :value="'Rp ' . number_format($rataRata, 0, ',', '.')" 
            icon="chart" 
            color="amber" 
            sub="Per transaksi" 
        />
    </div>

    {{-- 3 Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6">
        <x-summary-card 
            label="STOK BARANG MENIPIS" 
            :value="(string)$stokMenipis" 
            unit="produk" 
            sub="Perlu segera restock" 
            icon="alert" 
            type="danger" 
        />
        <x-summary-card 
            label="TOTAL ITEM BARANG" 
            :value="(string)$totalBarang" 
            unit="produk" 
            sub="Tersedia di gudang" 
            icon="box" 
            type="info" 
        />
        <x-summary-card 
            label="TOTAL JASA" 
            :value="(string)$totalJasa" 
            unit="layanan" 
            sub="Tarif aktif" 
            icon="layanan" 
            type="success" 
        />
    </div>

    {{-- Two Columns: Sales Trend Chart & Status Transaksi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Column (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Tren Penjualan</h3>
                <span class="text-xs text-gray-400 font-medium">7 Hari Terakhir</span>
            </div>

            <!-- SVG Chart Mockup Matching Mockup 1 -->
            <div class="w-full h-64 pt-4 flex flex-col justify-end">
                <div class="relative w-full h-48 border-b border-gray-100">
                    <!-- Background Grid Lines -->
                    <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-40">
                        <div class="border-b border-gray-100 w-full"></div>
                        <div class="border-b border-gray-100 w-full"></div>
                        <div class="border-b border-gray-100 w-full"></div>
                        <div class="border-b border-gray-100 w-full"></div>
                    </div>

                    <!-- SVG Curve & Points -->
                    <svg class="w-full h-full overflow-visible" viewBox="0 0 700 200" preserveAspectRatio="none">
                        <!-- Gradient Fill -->
                        <defs>
                            <linearGradient id="chartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0" />
                            </linearGradient>
                        </defs>
                        <!-- Area -->
                        <path d="M 30,170 L 140,170 L 250,170 L 360,170 L 470,170 L 580,170 L 670,30 L 670,190 L 30,190 Z" fill="url(#chartGradient)"></path>
                        <!-- Line -->
                        <path d="M 30,170 L 140,170 L 250,170 L 360,170 L 470,170 L 580,170 L 670,30" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                        <!-- Circles on Points -->
                        <circle cx="30" cy="170" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                        <circle cx="140" cy="170" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                        <circle cx="250" cy="170" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                        <circle cx="360" cy="170" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                        <circle cx="470" cy="170" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                        <circle cx="580" cy="170" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2"></circle>
                        <circle cx="670" cy="30" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2.5"></circle>
                    </svg>
                </div>

                <!-- Labels -->
                <div class="flex justify-between text-[11px] text-gray-400 font-medium mt-3 px-2">
                    <span>Mon</span>
                    <span>Tue</span>
                    <span>Wed</span>
                    <span>Thu</span>
                    <span>Fri</span>
                    <span>Sat</span>
                    <span class="text-blue-600 font-bold">Sun</span>
                </div>
            </div>
        </div>

        <!-- Status Transaksi Column (1 Col) -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-2">
                <h3 class="text-sm font-bold text-gray-900">Status Transaksi</h3>
                <span class="text-xs text-gray-400 font-medium">Terbaru</span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold border-b border-gray-50">
                            <th class="py-2.5">ID TRX</th>
                            <th class="py-2.5">PELANGGAN</th>
                            <th class="py-2.5 text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        @forelse($transaksiTerbaru as $trx)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-2.5 font-medium text-gray-500">{{ $trx->no_nota }}</td>
                                <td class="py-2.5 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? 'Umum' }}</td>
                                <td class="py-2.5 text-right font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-400">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
