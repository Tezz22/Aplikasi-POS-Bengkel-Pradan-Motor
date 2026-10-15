@extends('layouts.app')

@section('title', 'Riwayat Kasir')

@section('content')
    @include('components.page-header', [
        'title'    => 'Riwayat Transaksi Kasir',
        'subtitle' => 'Daftar transaksi penjualan dan servis yang Anda layani',
        'icon'     => 'riwayat'
    ])

    @include('components.table', [
        'id'                => 'table-riwayat-kasir',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari no nota...',
        'headers'           => ['NO. NOTA', 'TANGGAL', 'PELANGGAN', 'TOTAL', 'METODE', 'STATUS', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">#TRX-001</td>
            <td class="py-3.5 px-4 text-gray-600">15 Okt 2026, 14:20</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Andi Saputra</td>
            <td class="py-3.5 px-4 font-bold text-gray-900">Rp 85.000</td>
            <td class="py-3.5 px-4 text-gray-600">Tunai</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Lunas'])
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'print', 'onclick' => "alert('Cetak ulang struk #TRX-001')"])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">#TRX-002</td>
            <td class="py-3.5 px-4 text-gray-600">15 Okt 2026, 15:45</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Budi Wicaksono</td>
            <td class="py-3.5 px-4 font-bold text-gray-900">Rp 120.000</td>
            <td class="py-3.5 px-4 text-gray-600">QRIS</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Lunas'])
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'print', 'onclick' => "alert('Cetak ulang struk #TRX-002')"])
            </td>
        </tr>
    </div>
@endsection
