@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')
    {{-- Page Header --}}
    @include('components.page-header', [
        'title'    => 'Stok Opname',
        'subtitle' => 'Kelola penyesuaian stok pada aplikasi dengan stok asli toko',
        'icon'     => 'stok-opname',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Mulai Opname Baru',
            'onclick' => "openModal('modal-stok-opname')"
        ])->render()
    ])

    {{-- 4 Metric Summary Cards Matching Mockup 4 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        @include('components.summary-card', [
            'label' => 'Total Pembelian',
            'value' => '3',
            'sub'   => 'transaksi tercatat',
            'icon'  => 'bag',
            'type'  => 'info',
        ])
        @include('components.summary-card', [
            'label' => 'Item Diperiksa',
            'value' => '22',
            'sub'   => 'total item',
            'icon'  => 'clipboard',
            'type'  => 'success',
        ])
        @include('components.summary-card', [
            'label' => 'Item Selisih',
            'value' => '5',
            'sub'   => 'perlu ditinjau',
            'icon'  => 'alert',
            'type'  => 'danger',
        ])
        @include('components.summary-card', [
            'label' => 'Supplier',
            'value' => '2',
            'sub'   => 'supplier aktif',
            'icon'  => 'truck',
            'type'  => 'warning',
        ])
    </div>

    {{-- Table Card --}}
    @include('components.table', [
        'id'                => 'table-stok-opname',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari kode, tanggal, atau supplier....',
        'headers'           => ['KODE', 'TANGGAL', 'SUPPLIER', 'ITEM DIPERIKSA', 'ITEM SELISIH', 'DICATAT OLEH', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BM-001</td>
            <td class="py-3.5 px-4 text-gray-600">3 Oktober 2026</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">CV Rusdi Speed</td>
            <td class="py-3.5 px-4 text-gray-600">42</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'danger', 'text' => '5 item'])
            </td>
            <td class="py-3.5 px-4 text-gray-500">Admin</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-opname')"])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BM-002</td>
            <td class="py-3.5 px-4 text-gray-600">29 September 2026</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">CV Rusdi Speed</td>
            <td class="py-3.5 px-4 text-gray-600">40</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => '0 item'])
            </td>
            <td class="py-3.5 px-4 text-gray-500">Admin</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-opname')"])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BM-003</td>
            <td class="py-3.5 px-4 text-gray-600">1 April 2026</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">UD Amba Motor Parts</td>
            <td class="py-3.5 px-4 text-gray-600">7</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'warning', 'text' => '2 item'])
            </td>
            <td class="py-3.5 px-4 text-gray-500">Admin</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-opname')"])
            </td>
        </tr>
    </div>

    {{-- Modal Mulai Stok Opname --}}
    @component('components.modal', [
        'id'        => 'modal-stok-opname',
        'title'     => 'Mulai Sesi Stok Opname',
        'subtitle'  => 'Pemeriksaan dan audit stok fisik di gudang / etalase toko',
        'size'      => 'md'
    ])
        @include('components.input', ['label' => 'Kode Opname', 'name' => 'kode_opname', 'value' => 'OPN-' . date('Ymd-His'), 'readonly' => true])
        @include('components.input', ['label' => 'Tanggal Pemeriksaan', 'name' => 'tanggal', 'type' => 'date', 'value' => date('Y-m-d'), 'required' => true])
        @include('components.select', [
            'label'   => 'Kategori Barang yang Diperiksa',
            'name'    => 'kategori',
            'options' => ['Semua' => 'Semua Kategori', 'Oli' => 'Khusus Oli & Pelumas', 'Sparepart' => 'Khusus Sparepart', 'Ban' => 'Khusus Ban Motor']
        ])
        @include('components.input', ['label' => 'Catatan / Alasan Audit', 'name' => 'catatan', 'placeholder' => 'Contoh: Audit berkala akhir bulan'])
    @endcomponent
@endsection
