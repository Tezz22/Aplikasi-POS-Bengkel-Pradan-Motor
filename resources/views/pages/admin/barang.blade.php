@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    {{-- Page Header --}}
    @include('components.page-header', [
        'title'    => 'Data Barang',
        'subtitle' => 'Kumpulan data barang yang tersedia pada sistem POS & Manajemen',
        'icon'     => 'barang',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Tambah Barang',
            'onclick' => "openModal('modal-tambah-barang')"
        ])->render()
    ])

    {{-- Table Card --}}
    @include('components.table', [
        'id'                => 'table-barang',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari data barang...',
        'headers'           => ['KODE', 'NAMA PRODUK', 'KATEGORI', 'SATUAN', 'HARGA JUAL', 'STOK', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6">
                <span class="font-bold text-gray-900 block">BRG-001</span>
                <span class="text-[10px] text-gray-400">448855262223</span>
            </td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Oli Mesin 1L</td>
            <td class="py-3.5 px-4 text-gray-600">Oli</td>
            <td class="py-3.5 px-4 text-gray-500">Botol</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 20.000</td>
            <td class="py-3.5 px-4">
                <span class="font-bold text-rose-500">9</span>
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-barang')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus barang ini?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6">
                <span class="font-bold text-gray-900 block">BRG-002</span>
                <span class="text-[10px] text-gray-400">775511333994</span>
            </td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Kampas Rem</td>
            <td class="py-3.5 px-4 text-gray-600">Rem</td>
            <td class="py-3.5 px-4 text-gray-500">Pcs</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 8.500</td>
            <td class="py-3.5 px-4">
                <span class="font-bold text-gray-800">100</span>
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-barang')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus barang ini?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6">
                <span class="font-bold text-gray-900 block">BRG-003</span>
                <span class="text-[10px] text-gray-400">8992345410181</span>
            </td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Busi Standar</td>
            <td class="py-3.5 px-4 text-gray-600">Kelistrikan</td>
            <td class="py-3.5 px-4 text-gray-500">Pcs</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 25.000</td>
            <td class="py-3.5 px-4">
                <span class="font-bold text-gray-800">50</span>
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-barang')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus barang ini?')){}"])
                </div>
            </td>
        </tr>
    </div>

    {{-- Modal Tambah Barang --}}
    @component('components.modal', [
        'id'        => 'modal-tambah-barang',
        'title'     => 'Tambah Barang Baru',
        'subtitle'  => 'Masukkan informasi barang atau sparepart baru ke inventaris'
    ])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'Kode Barang', 'name' => 'kode_barang', 'value' => 'BRG-004', 'required' => true])
            @include('components.input', ['label' => 'Barcode / SKU', 'name' => 'barcode', 'placeholder' => 'Scan barcode jika ada'])
        </div>
        @include('components.input', ['label' => 'Nama Barang', 'name' => 'nama_barang', 'placeholder' => 'Contoh: Filter Udara Honda Vario', 'required' => true])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.select', [
                'label'   => 'Kategori',
                'name'    => 'kategori',
                'options' => ['Oli' => 'Oli & Pelumas', 'Rem' => 'Sistem Pengereman', 'Kelistrikan' => 'Kelistrikan & Pengapian', 'Mesin' => 'Suku Cadang Mesin']
            ])
            @include('components.input', ['label' => 'Satuan', 'name' => 'satuan', 'placeholder' => 'Pcs, Botol, Set'])
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @include('components.input', ['label' => 'Harga Beli (Rp)', 'name' => 'harga_beli', 'type' => 'number', 'placeholder' => '0'])
            @include('components.input', ['label' => 'Harga Jual (Rp)', 'name' => 'harga_jual', 'type' => 'number', 'placeholder' => '0', 'required' => true])
            @include('components.input', ['label' => 'Stok Awal', 'name' => 'stok', 'type' => 'number', 'placeholder' => '0', 'required' => true])
        </div>
    @endcomponent
@endsection
