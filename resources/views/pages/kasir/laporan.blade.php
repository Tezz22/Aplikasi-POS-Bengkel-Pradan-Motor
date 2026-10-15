@extends('layouts.app')

@section('title', 'Laporan Kasir')

@section('content')
    @include('components.page-header', [
        'title'    => 'Laporan Penjualan Kasir',
        'subtitle' => 'Rekapitulasi penjualan shift dan harian kasir',
        'icon'     => 'laporan'
    ])

    <!-- Filter Shift & Tanggal -->
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <select class="px-3.5 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
            <option value="hari-ini">Shift Hari Ini (15 Okt 2026)</option>
            <option value="kemarin">Kemarin (14 Okt 2026)</option>
            <option value="bulan-ini">Bulan Ini (Oktober 2026)</option>
        </select>
    </div>

    @include('components.table', [
        'id'      => 'table-laporan-kasir',
        'headers' => ['METODE PEMBAYARAN', 'JUMLAH TRANSAKSI', 'TOTAL NOMINAL']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-semibold text-gray-900">Uang Tunai (Cash)</td>
            <td class="py-3.5 px-4 text-gray-600">10 Transaksi</td>
            <td class="py-3.5 px-4 last:pr-6 font-bold text-gray-900">Rp 420.000</td>
        </tr>
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-semibold text-gray-900">QRIS / Non-Tunai</td>
            <td class="py-3.5 px-4 text-gray-600">4 Transaksi</td>
            <td class="py-3.5 px-4 last:pr-6 font-bold text-gray-900">Rp 160.000</td>
        </tr>
        <tr class="bg-gray-50/80 font-bold text-gray-900 border-t border-gray-200">
            <td class="py-3.5 px-4 first:pl-6">TOTAL SETORAN KASIR:</td>
            <td class="py-3.5 px-4 text-gray-700">14 Transaksi</td>
            <td class="py-3.5 px-4 last:pr-6 text-blue-600 text-sm">Rp 580.000</td>
        </tr>
    </div>

    <div class="flex items-center gap-3 mt-5">
        @include('components.button', [
            'variant' => 'primary',
            'size'    => 'sm',
            'icon'    => 'printer',
            'text'    => 'Cetak Rekap Shift Kasir',
            'onclick' => "alert('Mencetak rekap kasir...')"
        ])
    </div>
@endsection
