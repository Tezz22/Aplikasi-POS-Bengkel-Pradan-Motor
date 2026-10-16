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
        :headers="['KODE', 'NAMA PRODUK', 'KATEGORI', 'SATUAN', 'HARGA JUAL', 'STOK', 'AKSI']"
    >
        @forelse($barangs as $barang)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6">
                    <span class="font-bold text-gray-900 block">{{ $barang->kode_barang }}</span>
                    @if($barang->barcode)
                        <span class="text-[10px] text-gray-400">{{ $barang->barcode }}</span>
                    @endif
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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Kode Barang" name="kode_barang" :value="'BRG-' . str_pad(($barangs->count() + 1), 3, '0', STR_PAD_LEFT)" :required="true" />
            <x-input label="Barcode / SKU" name="barcode" placeholder="Scan barcode jika ada" />
        </div>
        <x-input label="Nama Barang" name="nama_barang" placeholder="Contoh: Filter Udara Honda Vario" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-select 
                label="Kategori" 
                name="kategori_id" 
                :options="$kategoris->pluck('nama_kategori', 'id')->toArray()" 
            />
            <x-input label="Satuan" name="satuan" placeholder="Pcs, Botol, Set" value="Pcs" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-input label="Harga Beli (Rp)" name="harga_beli" type="number" placeholder="0" />
            <x-input label="Harga Jual (Rp)" name="harga_jual" type="number" placeholder="0" :required="true" />
            <x-input label="Stok Awal" name="stok" type="number" placeholder="0" :required="true" />
        </div>
    </x-modal>
@endsection
