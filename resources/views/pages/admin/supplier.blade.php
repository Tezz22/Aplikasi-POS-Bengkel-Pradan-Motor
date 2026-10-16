@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
    <x-page-header 
        title="Data Supplier" 
        subtitle="Daftar mitra pemasok dan distributor suku cadang Pradan Motor" 
        icon="supplier"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Tambah Supplier" onclick="openModal('modal-tambah-supplier')" />
        </x-slot:actions>
    </x-page-header>

    <x-table 
        id="table-supplier" 
        :searchable="true" 
        searchPlaceholder="Cari supplier..." 
        :headers="['KODE', 'NAMA SUPPLIER', 'NO. TELEPON', 'ALAMAT', 'KONTAK PERSON', 'AKSI']"
    >
        @forelse($suppliers as $supplier)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $supplier->kode_supplier }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $supplier->nama_supplier }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $supplier->no_telp ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $supplier->alamat ?? '-' }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-800">{{ $supplier->kontak_person ?? '-' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-supplier')" />
                        <x-button-action type="delete" onclick="if(confirm('Hapus supplier ini?')){{}}" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data supplier</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Supplier --}}
    <x-modal 
        id="modal-tambah-supplier" 
        title="Tambah Supplier Baru" 
        subtitle="Masukkan data perusahaan atau distributor pemasok barang"
    >
        <x-input label="Kode Supplier" name="kode_supplier" :value="'SPL-' . str_pad(($suppliers->count() + 1), 2, '0', STR_PAD_LEFT)" :required="true" />
        <x-input label="Nama Supplier / Perusahaan" name="nama_supplier" placeholder="Contoh: PT Astra Otoparts Tbk" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="No. Telepon Kantor" name="no_telp" placeholder="021-xxxxxx" />
            <x-input label="Kontak Person (PIC)" name="kontak_person" placeholder="Nama & No HP PIC" />
        </div>
        <x-input label="Alamat Kantor / Gudang" name="alamat" placeholder="Alamat lengkap" />
    </x-modal>
@endsection
