@extends('layouts.app')

@section('title', 'Riwayat Kasir')

@section('content')
    <x-page-header 
        title="Riwayat Transaksi Kasir" 
        subtitle="Daftar transaksi penjualan dan servis yang dilayani bengkel" 
        icon="riwayat" 
    />

    <x-table 
        id="table-riwayat-kasir" 
        :searchable="true" 
        searchPlaceholder="Cari no transaksi atau pelanggan..." 
        :headers="['NO. TRANSAKSI', 'TANGGAL & WAKTU', 'PELANGGAN', 'TOTAL', 'NOMINAL BAYAR', 'KEMBALIAN', 'AKSI']"
    >
        @forelse($transaksis as $trx)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $trx->no_transaksi }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y, H:i') }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                <td class="py-3.5 px-4 font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-600">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 font-semibold text-emerald-600">Rp {{ number_format($trx->kembalian ?? 0, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <x-button-action type="print" :onclick="'alert(\'Mencetak struk ' . $trx->no_transaksi . '...\')'" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada riwayat transaksi</td>
            </tr>
        @endforelse
    </x-table>
@endsection
