@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
    {{-- Page Header --}}
    @include('components.page-header', [
        'title'    => 'Barang Masuk',
        'subtitle' => 'Kelola inventaris barang masuk dari supplier Pradan Motor',
        'icon'     => 'barang-masuk',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Catat Barang Masuk',
            'onclick' => "openModal('modal-barang-masuk')"
        ])->render()
    ])

    {{-- Table Card --}}
    @include('components.table', [
        'id'                => 'table-barang-masuk',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari kode, tanggal, atau supplier....',
        'headers'           => ['KODE', 'TANGGAL', 'SUPPLIER', 'ITEM', 'TOTAL HARGA', 'DICATAT OLEH', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BM-001</td>
            <td class="py-3.5 px-4 text-gray-600">3 Oktober 2026</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">CV Rusdi Speed</td>
            <td class="py-3.5 px-4 text-gray-600">3</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 20.000</td>
            <td class="py-3.5 px-4 text-gray-500">Admin</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-bm')"])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BM-002</td>
            <td class="py-3.5 px-4 text-gray-600">29 September 2026</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">CV Rusdi Speed</td>
            <td class="py-3.5 px-4 text-gray-600">12</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 20.000</td>
            <td class="py-3.5 px-4 text-gray-500">Admin</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-bm')"])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BM-003</td>
            <td class="py-3.5 px-4 text-gray-600">1 April 2026</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">UD Amba Motor Parts</td>
            <td class="py-3.5 px-4 text-gray-600">7</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 20.000</td>
            <td class="py-3.5 px-4 text-gray-500">Admin</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button-action', ['type' => 'info', 'onclick' => "openModal('modal-detail-bm')"])
            </td>
        </tr>
    </div>

    {{-- Modal Catat Barang Masuk --}}
    @component('components.modal', [
        'id'        => 'modal-barang-masuk',
        'title'     => 'Catat Barang Masuk',
        'subtitle'  => 'Formulir penerimaan barang masuk dari supplier',
        'size'      => 'lg'
    ])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'No. Bukti / Kode Masuk', 'name' => 'kode_masuk', 'value' => 'BM-004', 'required' => true])
            @include('components.input', ['label' => 'Tanggal Masuk', 'name' => 'tanggal', 'type' => 'date', 'value' => date('Y-m-d'), 'required' => true])
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.select', [
                'label'   => 'Supplier',
                'name'    => 'supplier_id',
                'options' => ['CV Rusdi Speed' => 'CV Rusdi Speed', 'UD Amba Motor Parts' => 'UD Amba Motor Parts', 'PT Astra Otoparts' => 'PT Astra Otoparts']
            ])
            @include('components.input', ['label' => 'No. Surat Jalan / Faktur', 'name' => 'no_faktur', 'placeholder' => 'Opsional'])
        </div>
        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
            <h4 class="font-bold text-xs text-gray-800 mb-2">Item Barang Diterima</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                @include('components.select', [
                    'label' => 'Pilih Barang',
                    'name' => 'barang_id',
                    'options' => ['Oli Mesin 1L' => 'BRG-001 - Oli Mesin 1L', 'Kampas Rem' => 'BRG-002 - Kampas Rem', 'Busi Standar' => 'BRG-003 - Busi Standar']
                ])
                @include('components.input', ['label' => 'Jumlah Masuk', 'name' => 'qty', 'type' => 'number', 'placeholder' => '0'])
                @include('components.input', ['label' => 'Harga Beli Satuan', 'name' => 'harga_beli', 'type' => 'number', 'placeholder' => '0'])
            </div>
        </div>
        @include('components.input', ['label' => 'Catatan Tambahan', 'name' => 'catatan', 'placeholder' => 'Keterangan kondisi barang, dll'])
    @endcomponent
@endsection
