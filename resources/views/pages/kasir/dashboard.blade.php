@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
    @include('components.page-header', [
        'title'    => 'Dashboard Kasir',
        'subtitle' => 'Selamat bekerja, kelola transaksi pelanggan bengkel hari ini',
        'icon'     => 'dashboard',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'transaksi',
            'text'    => 'Buka POS Kasir',
            'href'    => route('kasir.transaksi')
        ])->render()
    ])

    <!-- Kasir Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        @include('components.stat-card', [
            'label' => 'TOTAL TRANSAKSI SAYA',
            'value' => '14',
            'icon'  => 'refresh',
            'color' => 'blue',
            'sub'   => 'Hari ini',
        ])
        @include('components.stat-card', [
            'label' => 'PENDAPATAN KASIR',
            'value' => 'Rp 580.000',
            'icon'  => 'dollar',
            'color' => 'emerald',
            'sub'   => 'Tunai & QRIS',
        ])
        @include('components.stat-card', [
            'label' => 'JASA SELESAI',
            'value' => '9',
            'icon'  => 'layanan',
            'color' => 'purple',
            'sub'   => 'Unit motor dikerjakan',
        ])
        @include('components.stat-card', [
            'label' => 'STOK KRITIS',
            'value' => '2',
            'icon'  => 'alert',
            'color' => 'amber',
            'sub'   => 'Segera infokan admin',
        ])
    </div>

    <!-- Transaksi Terakhir Kasir -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Transaksi Terakhir Kasir</h3>
                <p class="text-xs text-gray-400 mt-0.5">Penjualan barang & jasa yang Anda proses hari ini</p>
            </div>
            @include('components.button', [
                'variant' => 'secondary',
                'size'    => 'sm',
                'text'    => 'Lihat Semua Riwayat',
                'href'    => route('kasir.riwayat')
            ])
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold border-b border-gray-100">
                        <th class="py-2.5 px-3">NO. NOTA</th>
                        <th class="py-2.5 px-3">WAKTU</th>
                        <th class="py-2.5 px-3">PELANGGAN</th>
                        <th class="py-2.5 px-3">METODE</th>
                        <th class="py-2.5 px-3 text-right">TOTAL</th>
                        <th class="py-2.5 px-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-3 font-bold text-gray-900">#TRX-001</td>
                        <td class="py-3 px-3 text-gray-500">14:20</td>
                        <td class="py-3 px-3 font-semibold text-gray-900">Andi Saputra</td>
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Tunai</span>
                        </td>
                        <td class="py-3 px-3 text-right font-bold text-gray-900">Rp 85.000</td>
                        <td class="py-3 px-3 text-center">
                            @include('components.button-action', ['type' => 'print', 'onclick' => "alert('Cetak struk #TRX-001')"])
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-3 font-bold text-gray-900">#TRX-002</td>
                        <td class="py-3 px-3 text-gray-500">15:45</td>
                        <td class="py-3 px-3 font-semibold text-gray-900">Budi Wicaksono</td>
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700">QRIS</span>
                        </td>
                        <td class="py-3 px-3 text-right font-bold text-gray-900">Rp 120.000</td>
                        <td class="py-3 px-3 text-center">
                            @include('components.button-action', ['type' => 'print', 'onclick' => "alert('Cetak struk #TRX-002')"])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
