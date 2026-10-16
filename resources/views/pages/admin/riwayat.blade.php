@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <x-page-header 
        title="Riwayat Transaksi" 
        subtitle="Daftar semua transaksi penjualan barang dan jasa bengkel" 
        icon="riwayat" 
    />

    <x-table 
        id="table-riwayat" 
        :searchable="true" 
        searchPlaceholder="Cari no nota atau nama pelanggan..." 
        :headers="['NO. NOTA', 'TANGGAL', 'PELANGGAN', 'TOTAL', 'METODE', 'STATUS', 'KASIR', 'AKSI']"
    >
        @forelse($transaksis as $trx)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $trx->no_nota }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y, H:i') }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                <td class="py-3.5 px-4 font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $trx->metode_pembayaran }}</td>
                <td class="py-3.5 px-4">
                    <x-badge :type="$trx->status === 'Lunas' ? 'success' : 'warning'" :text="$trx->status" />
                </td>
                <td class="py-3.5 px-4 text-gray-500">{{ $trx->user->name ?? 'Kasir' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="info" onclick="openModal('modal-detail-transaksi-{{ $trx->id }}')" />
                        <x-button-action type="print" :onclick="'alert(\'Mencetak struk transaksi ' . $trx->no_nota . '...\')'" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="py-8 text-center text-gray-400">Belum ada riwayat transaksi</td>
            </tr>
        @endforelse
    </x-table>
@endsection
