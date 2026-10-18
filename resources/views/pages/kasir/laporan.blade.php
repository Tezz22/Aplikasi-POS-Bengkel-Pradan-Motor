@extends('layouts.app')

@section('title', 'Laporan Kasir')

@section('content')
    <x-page-header 
        title="Laporan Penjualan Kasir" 
        subtitle="Rekapitulasi penjualan harian dan total setoran kasir" 
        icon="laporan" 
    />

    <x-table 
        id="table-laporan-kasir" 
        :headers="['TANGGAL TRANSAKSI', 'JUMLAH TRANSAKSI', 'TOTAL NOMINAL']"
    >
        @forelse($rekapHarian as $rekap)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($rekap->tgl)->translatedFormat('d F Y') }}
                </td>
                <td class="py-3.5 px-4 text-gray-600">{{ $rekap->jml_transaksi }} Transaksi</td>
                <td class="py-3.5 px-4 last:pr-6 font-bold text-gray-900">Rp {{ number_format($rekap->total_nominal, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="py-8 text-center text-gray-400">Belum ada transaksi</td>
            </tr>
        @endforelse
        <tr class="bg-gray-50/80 font-bold text-gray-900 border-t border-gray-200">
            <td class="py-3.5 px-4 first:pl-6">TOTAL PENJUALAN:</td>
            <td class="py-3.5 px-4 text-gray-700">{{ $totalTransaksi }} Transaksi</td>
            <td class="py-3.5 px-4 last:pr-6 text-blue-600 text-sm">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
        </tr>
    </x-table>

    <div class="flex items-center gap-3 mt-5">
        <x-button 
            variant="primary" 
            size="sm" 
            icon="printer" 
            text="Cetak Rekap Kasir" 
            onclick="alert('Mencetak rekap transaksi kasir...')" 
        />
    </div>
@endsection
