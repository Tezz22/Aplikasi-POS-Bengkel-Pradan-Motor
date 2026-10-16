@extends('layouts.app')

@section('title', 'Riwayat Kasir')

@section('content')
    <x-page-header 
        title="Riwayat Transaksi Kasir" 
        subtitle="Daftar transaksi penjualan dan servis yang Anda layani" 
        icon="riwayat" 
    />

    <x-table 
        id="table-riwayat-kasir" 
        :searchable="true" 
        searchPlaceholder="Cari no nota..." 
        :headers="['NO. NOTA', 'TANGGAL', 'PELANGGAN', 'TOTAL', 'METODE', 'STATUS', 'AKSI']"
    >
        @forelse($transaksis as $trx)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $trx->no_faktur }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y, H:i') }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? ($trx->nama_pelanggan_non_member ?? 'Umum') }}</td>
                <td class="py-3.5 px-4 font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $trx->metode_pembayaran }}</td>
                <td class="py-3.5 px-4">
                    <x-badge :type="$trx->status === 'Lunas' ? 'success' : 'warning'" :text="$trx->status" />
                </td>
                <td class="py-3.5 px-4 last:pr-6">
                    <x-button-action type="print" onclick="alert('Cetak ulang struk {{ $trx->no_faktur }}')" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada riwayat transaksi</td>
            </tr>
        @endforelse
    </x-table>
@endsection
