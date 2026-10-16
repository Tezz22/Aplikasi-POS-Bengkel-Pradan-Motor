@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
    {{-- Page Header --}}
    <x-page-header 
        title="Barang Masuk" 
        subtitle="Kelola inventaris barang masuk dari supplier Pradan Motor" 
        icon="barang-masuk"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Catat Barang Masuk" onclick="openModal('modal-barang-masuk')" />
        </x-slot:actions>
    </x-page-header>

    {{-- Table Card --}}
    <x-table 
        id="table-barang-masuk" 
        :searchable="true" 
        searchPlaceholder="Cari kode, tanggal, atau supplier...." 
        :headers="['KODE', 'TANGGAL', 'SUPPLIER', 'ITEM', 'TOTAL HARGA', 'DICATAT OLEH', 'AKSI']"
    >
        @forelse($pembelians as $pembelian)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $pembelian->kode_masuk }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($pembelian->tanggal)->translatedFormat('d F Y') }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-800">{{ $pembelian->supplier->nama_supplier ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $pembelian->details->sum('qty') }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-900">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $pembelian->user->name ?? 'Admin' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <x-button-action type="info" onclick="openModal('modal-detail-bm-{{ $pembelian->id }}')" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data barang masuk</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Catat Barang Masuk --}}
    <x-modal 
        id="modal-barang-masuk" 
        title="Catat Barang Masuk" 
        subtitle="Formulir penerimaan barang masuk dari supplier" 
        size="lg"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="No. Bukti / Kode Masuk" name="kode_masuk" :value="'BM-' . str_pad(($pembelians->count() + 1), 3, '0', STR_PAD_LEFT)" :required="true" />
            <x-input label="Tanggal Masuk" name="tanggal" type="date" :value="date('Y-m-d')" :required="true" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-select 
                label="Supplier" 
                name="supplier_id" 
                :options="$suppliers->pluck('nama_supplier', 'id')->toArray()" 
            />
            <x-input label="No. Surat Jalan / Faktur" name="no_faktur" placeholder="Opsional" />
        </div>
        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
            <h4 class="font-bold text-xs text-gray-800 mb-2">Item Barang Diterima</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <x-select 
                    label="Pilih Barang" 
                    name="barang_id" 
                    :options="$barangs->pluck('nama_barang', 'id')->toArray()" 
                />
                <x-input label="Jumlah Masuk" name="qty" type="number" placeholder="0" />
                <x-input label="Harga Beli Satuan" name="harga_beli" type="number" placeholder="0" />
            </div>
        </div>
        <x-input label="Catatan Tambahan" name="catatan" placeholder="Keterangan kondisi barang, dll" />
    </x-modal>
@endsection
