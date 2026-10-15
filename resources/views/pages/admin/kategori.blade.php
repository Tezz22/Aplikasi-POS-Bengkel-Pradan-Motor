@extends('layouts.app')

@section('title', 'Kategori Barang')

@section('content')
    @include('components.page-header', [
        'title'    => 'Kategori Barang',
        'subtitle' => 'Klasifikasi dan pengelompokan suku cadang bengkel',
        'icon'     => 'kategori',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Tambah Kategori',
            'onclick' => "openModal('modal-tambah-kategori')"
        ])->render()
    ])

    @include('components.table', [
        'id'                => 'table-kategori',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari kategori...',
        'headers'           => ['KODE', 'NAMA KATEGORI', 'JUMLAH ITEM', 'DESKRIPSI', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">KTG-01</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Oli & Pelumas</td>
            <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">12 Item</span>
            </td>
            <td class="py-3.5 px-4 text-gray-500">Oli mesin, gardan, shock, dan minyak rem</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-kategori')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus kategori?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">KTG-02</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Sistem Pengereman</td>
            <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">8 Item</span>
            </td>
            <td class="py-3.5 px-4 text-gray-500">Kampas rem cakram dan tromol</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-kategori')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus kategori?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">KTG-03</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Kelistrikan</td>
            <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">15 Item</span>
            </td>
            <td class="py-3.5 px-4 text-gray-500">Aki motor, busi, bohlam lampu, sekring</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-kategori')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus kategori?')){}"])
                </div>
            </td>
        </tr>
    </div>

    {{-- Modal Tambah Kategori --}}
    @component('components.modal', [
        'id'        => 'modal-tambah-kategori',
        'title'     => 'Tambah Kategori Barang',
        'subtitle'  => 'Buat kategori baru untuk pengelompokan suku cadang'
    ])
        @include('components.input', ['label' => 'Kode Kategori', 'name' => 'kode_kategori', 'value' => 'KTG-04', 'required' => true])
        @include('components.input', ['label' => 'Nama Kategori', 'name' => 'nama_kategori', 'placeholder' => 'Contoh: Ban Luar & Dalam', 'required' => true])
        @include('components.input', ['label' => 'Deskripsi Kategori', 'name' => 'deskripsi', 'placeholder' => 'Keterangan opsional'])
    @endcomponent
@endsection
