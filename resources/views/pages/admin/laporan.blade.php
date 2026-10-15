@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
    {{-- Page Header --}}
    @include('components.page-header', [
        'title'    => 'Laporan',
        'subtitle' => 'Laporan penjualan',
        'icon'     => 'laporan'
    ])

    <!-- Filter Dropdowns Matching Mockup 2 -->
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative">
            <select class="px-3.5 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                <option value="penjualan">Jenis: Penjualan</option>
                <option value="pembelian">Jenis: Pembelian (Barang Masuk)</option>
                <option value="stok">Jenis: Mutasi Stok</option>
                <option value="jasa">Jenis: Jasa Servis</option>
            </select>
        </div>

        <div class="relative">
            <select class="px-3.5 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                <option value="2026-01">Periode: Jan 2026</option>
                <option value="2026-02">Periode: Feb 2026</option>
                <option value="2026-03">Periode: Mar 2026</option>
                <option value="2026-04">Periode: Apr 2026</option>
            </select>
        </div>
    </div>

    {{-- Table Card --}}
    @include('components.table', [
        'id'      => 'table-laporan',
        'headers' => ['TANGGAL', 'KASIR', 'JUMLAH TRANSAKSI', 'TOTAL PENJUALAN']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-medium text-gray-800">25 Jan 2026</td>
            <td class="py-3.5 px-4 text-gray-600">Rina Wati</td>
            <td class="py-3.5 px-4 text-gray-600">1</td>
            <td class="py-3.5 px-4 last:pr-6 font-semibold text-gray-900">Rp 100.000</td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-medium text-gray-800">26 Jan 2026</td>
            <td class="py-3.5 px-4 text-gray-600">Budi Santoso</td>
            <td class="py-3.5 px-4 text-gray-600">2</td>
            <td class="py-3.5 px-4 last:pr-6 font-semibold text-gray-900">Rp 100.000</td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-medium text-gray-800">27 Jan 2026</td>
            <td class="py-3.5 px-4 text-gray-600">Ayu Lestari</td>
            <td class="py-3.5 px-4 text-gray-600">3</td>
            <td class="py-3.5 px-4 last:pr-6 font-semibold text-gray-900">Rp 100.000</td>
        </tr>
    </div>

    <!-- Export Action Buttons Matching Mockup 2 -->
    <div class="flex items-center gap-3 mt-5">
        @include('components.button', [
            'variant' => 'outline-danger',
            'size'    => 'sm',
            'text'    => 'Ekspor PDF',
            'onclick' => "alert('Mengekspor laporan ke format PDF...')"
        ])
        @include('components.button', [
            'variant' => 'outline-success',
            'size'    => 'sm',
            'text'    => 'Ekspor Excel',
            'onclick' => "alert('Mengekspor laporan ke format Excel...')"
        ])
    </div>
@endsection
