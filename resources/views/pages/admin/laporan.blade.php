@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
    {{-- Page Header --}}
    <x-page-header 
        title="Laporan" 
        subtitle="Laporan rekapitulasi penjualan bengkel" 
        icon="laporan" 
    />

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
                <option value="2026-10">Periode: Okt 2026</option>
                <option value="2026-09">Periode: Sep 2026</option>
                <option value="2026-08">Periode: Agu 2026</option>
            </select>
        </div>
    </div>

    {{-- Table Card --}}
    <x-table 
        id="table-laporan" 
        :headers="['TANGGAL', 'KASIR', 'JUMLAH TRANSAKSI', 'TOTAL PENJUALAN']"
    >
        @forelse($laporans as $lap)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse($lap->tgl)->translatedFormat('d M Y') }}
                </td>
                <td class="py-3.5 px-4 text-gray-600">{{ $lap->user->name ?? 'Kasir Bengkel' }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $lap->jml_transaksi }} Transaksi</td>
                <td class="py-3.5 px-4 last:pr-6 font-semibold text-gray-900">
                    Rp {{ number_format($lap->total_penjualan, 0, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-400">Belum ada data laporan penjualan</td>
            </tr>
        @endforelse
    </x-table>

    <!-- Export Action Buttons Matching Mockup 2 -->
    <div class="flex items-center gap-3 mt-5">
        <x-button 
            variant="outline-danger" 
            size="sm" 
            text="Ekspor PDF" 
            onclick="alert('Mengekspor laporan ke format PDF...')" 
        />
        <x-button 
            variant="outline-success" 
            size="sm" 
            text="Ekspor Excel" 
            onclick="alert('Mengekspor laporan ke format Excel...')" 
        />
    </div>
@endsection
