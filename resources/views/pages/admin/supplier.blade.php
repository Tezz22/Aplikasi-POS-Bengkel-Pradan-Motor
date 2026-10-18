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
        :headers="['ID', 'NAMA SUPPLIER', 'NO. TELEPON', 'ALAMAT', 'AKSI']"
    >
        @forelse($suppliers as $supplier)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">#{{ $supplier->id_supplier }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $supplier->nama_supplier }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $supplier->no_telepon ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $supplier->alamat ?? '-' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-supplier-{{ $supplier->id_supplier }}')" />
                        
                        <form action="{{ route('admin.supplier.destroy', $supplier->id_supplier) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier \'{{ $supplier->nama_supplier }}\'?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button-action type="delete" buttonType="submit" />
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-400">Belum ada data supplier</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Supplier --}}
    <x-modal 
        id="modal-tambah-supplier" 
        title="Tambah Supplier Baru" 
        subtitle="Masukkan data perusahaan atau distributor pemasok barang"
        :formAction="route('admin.supplier.store')"
        formMethod="POST"
        submitText="Simpan Supplier"
    >
        <x-input label="Nama Supplier / Distributor" name="nama_supplier" placeholder="Contoh: PT Astra Otoparts Tbk (Maks. 50 Karakter)" maxlength="50" :required="true" />
        <x-input label="No. Telepon" name="no_telepon" placeholder="Contoh: 021-4603888 (Maks. 15 Karakter)" maxlength="15" />
        <x-input label="Alamat Kantor / Gudang" name="alamat" placeholder="Alamat lengkap supplier" maxlength="255" />
    </x-modal>

    {{-- Modals Edit Supplier --}}
    @foreach($suppliers as $supplier)
        <x-modal 
            id="modal-edit-supplier-{{ $supplier->id_supplier }}" 
            title="Edit Supplier #{{ $supplier->id_supplier }}" 
            subtitle="Perbarui kontak atau alamat distributor"
            :formAction="route('admin.supplier.update', $supplier->id_supplier)"
            formMethod="PUT"
            submitText="Perbarui Supplier"
        >
            <x-input label="Nama Supplier / Distributor" name="nama_supplier" :value="$supplier->nama_supplier" maxlength="50" :required="true" />
            <x-input label="No. Telepon" name="no_telepon" :value="$supplier->no_telepon" maxlength="15" />
            <x-input label="Alamat Kantor / Gudang" name="alamat" :value="$supplier->alamat" maxlength="255" />
        </x-modal>
    @endforeach
@endsection
