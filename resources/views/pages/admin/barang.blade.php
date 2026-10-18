@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    {{-- Page Header --}}
    <x-page-header 
        title="Data Barang" 
        subtitle="Kumpulan data barang yang tersedia pada sistem POS & Manajemen" 
        icon="barang"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Tambah Barang" onclick="openModal('modal-tambah-barang')" />
        </x-slot:actions>
    </x-page-header>

    {{-- Table Card --}}
    <x-table 
        id="table-barang" 
        :searchable="true" 
        searchPlaceholder="Cari data barang..." 
        :headers="['ID', 'NAMA PRODUK', 'KATEGORI', 'SATUAN', 'HARGA JUAL', 'STOK', 'AKSI']"
    >
        @forelse($barangs as $barang)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">
                    #{{ $barang->id_barang }}
                </td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $barang->nama_barang }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $barang->satuan }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-900">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4">
                    <span class="font-bold {{ $barang->stok <= 10 ? 'text-rose-500' : 'text-gray-800' }}">
                        {{ $barang->stok }}
                    </span>
                </td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-barang')" />
                        <x-button-action type="delete" onclick="if(confirm('Hapus barang ini?')){{}}" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data barang</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Barang --}}
    <x-modal 
        id="modal-tambah-barang" 
        title="Tambah Barang Baru" 
        subtitle="Masukkan informasi barang atau sparepart baru ke inventaris"
    >
        <x-input label="Nama Barang" name="nama_barang" placeholder="Contoh: Oli MPX2 0.8L Matic (Maks. 30 Karakter)" maxlength="30" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-select 
                label="Kategori" 
                name="id_kategori" 
                :options="$kategoris->pluck('nama_kategori', 'id_kategori')->toArray()" 
                :required="true"
            />
            <x-input label="Satuan" name="satuan" placeholder="Botol, Pcs, Set (Maks. 20 Karakter)" maxlength="20" value="Pcs" :required="true" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Harga Jual (Rp)" name="harga_jual" type="number" placeholder="0" :required="true" />
            <x-input label="Stok Awal" name="stok" type="number" placeholder="0" :required="true" />
        </div>
    </x-modal>
@endsection
