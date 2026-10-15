@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    @include('components.page-header', [
        'title'    => 'Riwayat Transaksi',
        'subtitle' => 'Daftar semua transaksi penjualan barang dan jasa bengkel',
        'icon'     => 'riwayat'
    ])

    @include('components.table', [
        'id'                => 'table-riwayat',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari no nota atau nama pelanggan...',
        'headers'           => ['NO. NOTA', 'TANGGAL', 'PELANGGAN', 'TOTAL', 'METODE', 'STATUS', 'KASIR', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">#TRX-20261015-001</td>
            <td class="py-3.5 px-4 text-gray-600">15 Okt 2026, 14:20</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Andi Saputra</td>
            <td class="py-3.5 px-4 font-bold text-gray-900">Rp 85.000</td>
            <td class="py-3.5 px-4 text-gray-600">Tunai / Cash</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Lunas'])
            </td>
            <td class="py-3.5 px-4 text-gray-500">Joo (Kasir)</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-transaksi')"])
                    @include('components.button-action', ['type' => 'print', 'onclick' => "alert('Mencetak struk transaksi...')"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">#TRX-20261015-002</td>
            <td class="py-3.5 px-4 text-gray-600">15 Okt 2026, 15:45</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Budi Wicaksono</td>
            <td class="py-3.5 px-4 font-bold text-gray-900">Rp 120.000</td>
            <td class="py-3.5 px-4 text-gray-600">QRIS</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Lunas'])
            </td>
            <td class="py-3.5 px-4 text-gray-500">Joo (Kasir)</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-transaksi')"])
                    @include('components.button-action', ['type' => 'print', 'onclick' => "alert('Mencetak struk transaksi...')"])
                </div>
            </td>
        </tr>
    </div>

    {{-- Modal Detail Transaksi --}}
    @component('components.modal', [
        'id'        => 'modal-detail-transaksi',
        'title'     => 'Detail Transaksi #TRX-20261015-001',
        'subtitle'  => 'Informasi rincian pembelian barang dan jasa servis',
        'size'      => 'lg'
    ])
        <div class="space-y-3">
            <div class="p-3 bg-gray-50 rounded-xl flex justify-between text-xs">
                <div>
                    <span class="text-gray-400 block">Pelanggan</span>
                    <span class="font-bold text-gray-900">Andi Saputra (B 1234 ABC)</span>
                </div>
                <div class="text-right">
                    <span class="text-gray-400 block">Metode Pembayaran</span>
                    <span class="font-semibold text-emerald-600">Tunai (Lunas)</span>
                </div>
            </div>

            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase text-[10px] text-left">
                        <th class="py-2">Item</th>
                        <th class="py-2 text-center">Qty</th>
                        <th class="py-2 text-right">Harga</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    <tr>
                        <td class="py-2.5 font-medium">Oli Mesin Matic 1L</td>
                        <td class="py-2.5 text-center">1</td>
                        <td class="py-2.5 text-right">Rp 45.000</td>
                        <td class="py-2.5 text-right font-semibold">Rp 45.000</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 font-medium">Servis Ringan / Tune Up (Jasa)</td>
                        <td class="py-2.5 text-center">1</td>
                        <td class="py-2.5 text-right">Rp 40.000</td>
                        <td class="py-2.5 text-right font-semibold">Rp 40.000</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200 font-bold text-gray-900">
                        <td colspan="3" class="pt-3 text-right">TOTAL TRANSAKSI:</td>
                        <td class="pt-3 text-right text-base text-blue-600">Rp 85.000</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endcomponent
@endsection
