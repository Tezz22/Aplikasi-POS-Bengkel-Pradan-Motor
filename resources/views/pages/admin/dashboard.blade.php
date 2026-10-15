@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    {{-- Page Header --}}
    @include('components.page-header', [
        'title'    => 'Dashboard',
        'subtitle' => 'Ringkasan performa bengkel, ' . date('l, d F Y'),
        'icon'     => 'dashboard',
    ])

    {{-- Top 4 Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-5">
        @include('components.stat-card', [
            'label' => 'PENDAPATAN (LUNAS)',
            'value' => 'Rp 724.500',
            'icon'  => 'dollar',
            'color' => 'emerald',
            'sub'   => '+12% dari kemarin',
        ])
        @include('components.stat-card', [
            'label' => 'TOTAL TRANSAKSI',
            'value' => '18',
            'icon'  => 'refresh',
            'color' => 'blue',
            'sub'   => 'Hari ini',
        ])
        @include('components.stat-card', [
            'label' => 'PRODUK TERJUAL',
            'value' => '39',
            'icon'  => 'bag',
            'color' => 'purple',
            'sub'   => 'Oli, ban, & sparepart',
        ])
        @include('components.stat-card', [
            'label' => 'RATA-RATA PENDAPATAN',
            'value' => 'Rp 40.250',
            'icon'  => 'chart',
            'color' => 'amber',
            'sub'   => 'Per transaksi',
        ])
    </div>

    {{-- 3 Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6">
        @include('components.summary-card', [
            'label' => 'STOK BARANG MENIPIS',
            'value' => '2',
            'unit'  => 'produk',
            'sub'   => 'Perlu segera restock',
            'icon'  => 'alert',
            'type'  => 'danger',
        ])
        @include('components.summary-card', [
            'label' => 'TOTAL ITEM BARANG',
            'value' => '6',
            'unit'  => 'produk',
            'sub'   => 'Tersedia di gudang',
            'icon'  => 'box',
            'type'  => 'info',
        ])
        @include('components.summary-card', [
            'label' => 'TOTAL JASA',
            'value' => '21',
            'unit'  => 'layanan',
            'sub'   => 'Tarif aktif',
            'icon'  => 'layanan',
            'type'  => 'success',
        ])
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
                <span class="text-xs text-gray-400 font-medium">Hari ini</span>
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
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-500">#TRX-001</td>
                            <td class="py-2.5 font-semibold text-gray-900">Andi S.</td>
                            <td class="py-2.5 text-right font-bold text-gray-900">85.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-500">#TRX-002</td>
                            <td class="py-2.5 font-semibold text-gray-900">Budi W.</td>
                            <td class="py-2.5 text-right font-bold text-gray-900">120.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-500">#TRX-003</td>
                            <td class="py-2.5 font-semibold text-gray-900">Citra M.</td>
                            <td class="py-2.5 text-right font-bold text-gray-900">45.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-500">#TRX-004</td>
                            <td class="py-2.5 font-semibold text-gray-900">Dian P.</td>
                            <td class="py-2.5 text-right font-bold text-gray-900">200.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-500">#TRX-005</td>
                            <td class="py-2.5 font-semibold text-gray-900">Eka R.</td>
                            <td class="py-2.5 text-right font-bold text-gray-900">75.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-500">#TRX-006</td>
                            <td class="py-2.5 font-semibold text-gray-900">Fajar K.</td>
                            <td class="py-2.5 text-right font-bold text-gray-900">55.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-500">#TRX-007</td>
                            <td class="py-2.5 font-semibold text-gray-900">Gita L.</td>
                            <td class="py-2.5 text-right font-bold text-gray-900">144.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
