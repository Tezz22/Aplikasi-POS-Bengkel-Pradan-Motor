@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
    @include('components.page-header', [
        'title'    => 'Data Supplier',
        'subtitle' => 'Daftar mitra pemasok dan distributor suku cadang Pradan Motor',
        'icon'     => 'supplier',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Tambah Supplier',
            'onclick' => "openModal('modal-tambah-supplier')"
        ])->render()
    ])

    @include('components.table', [
        'id'                => 'table-supplier',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari supplier...',
        'headers'           => ['KODE', 'NAMA SUPPLIER', 'NO. TELEPON', 'ALAMAT', 'KONTAK PERSON', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">SPL-01</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">CV Rusdi Speed</td>
            <td class="py-3.5 px-4 text-gray-600">021-55443322</td>
            <td class="py-3.5 px-4 text-gray-500">Kawasan Industri Pulogadung Blok B</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">Pak Rusdi (0812999888)</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-supplier')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus supplier?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">SPL-02</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">UD Amba Motor Parts</td>
            <td class="py-3.5 px-4 text-gray-600">021-88776655</td>
            <td class="py-3.5 px-4 text-gray-500">Jl. Raya Bekasi KM 21</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">Amba (0818223344)</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-supplier')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus supplier?')){}"])
                </div>
            </td>
        </tr>
    </div>

    {{-- Modal Tambah Supplier --}}
    @component('components.modal', [
        'id'        => 'modal-tambah-supplier',
        'title'     => 'Tambah Supplier Baru',
        'subtitle'  => 'Masukkan data perusahaan atau distributor pemasok barang'
    ])
        @include('components.input', ['label' => 'Kode Supplier', 'name' => 'kode_supplier', 'value' => 'SPL-03', 'required' => true])
        @include('components.input', ['label' => 'Nama Supplier / Perusahaan', 'name' => 'nama_supplier', 'placeholder' => 'Contoh: PT Astra Otoparts Tbk', 'required' => true])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'No. Telepon Kantor', 'name' => 'no_telp', 'placeholder' => '021-xxxxxx'])
            @include('components.input', ['label' => 'Kontak Person (PIC)', 'name' => 'kontak_person', 'placeholder' => 'Nama & No HP PIC'])
        </div>
        @include('components.input', ['label' => 'Alamat Kantor / Gudang', 'name' => 'alamat', 'placeholder' => 'Alamat lengkap'])
    @endcomponent
@endsection
