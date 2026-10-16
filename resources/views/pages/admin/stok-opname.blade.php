@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')
    {{-- Page Header --}}
    <x-page-header 
        title="Stok Opname" 
        subtitle="Kelola penyesuaian stok pada aplikasi dengan stok asli toko" 
        icon="stok-opname"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Mulai Opname Baru" onclick="openModal('modal-stok-opname')" />
        </x-slot:actions>
    </x-page-header>

    {{-- 4 Metric Summary Cards Matching Mockup 4 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <x-summary-card 
            label="Total Pembelian" 
            :value="(string)$totalPembelian" 
            sub="transaksi tercatat" 
            icon="bag" 
            type="info" 
        />
        <x-summary-card 
            label="Item Diperiksa" 
            :value="(string)$itemDiperiksa" 
            sub="total item" 
            icon="clipboard" 
            type="success" 
        />
        <x-summary-card 
            label="Item Selisih" 
            :value="(string)$itemSelisih" 
            sub="perlu ditinjau" 
            icon="alert" 
            type="danger" 
        />
        <x-summary-card 
            label="Supplier" 
            :value="(string)$totalSupplier" 
            sub="supplier aktif" 
            icon="truck" 
            type="warning" 
        />
    </div>

    {{-- Table Card --}}
    <x-table 
        id="table-stok-opname" 
        :searchable="true" 
        searchPlaceholder="Cari kode, tanggal, atau supplier...." 
        :headers="['KODE', 'TANGGAL', 'SUPPLIER', 'ITEM DIPERIKSA', 'ITEM SELISIH', 'DICATAT OLEH', 'AKSI']"
    >
        @forelse($opnames as $opname)
            @php
                $selisihCount = $opname->details->where('selisih', '!=', 0)->count();
            @endphp
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $opname->kode_opname }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($opname->tanggal)->translatedFormat('d F Y') }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-800">{{ $opname->supplier->nama_supplier ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $opname->details->count() }}</td>
                <td class="py-3.5 px-4">
                    <x-badge 
                        :type="$selisihCount > 0 ? 'danger' : 'success'" 
                        :text="$selisihCount . ' item'" 
                    />
                </td>
                <td class="py-3.5 px-4 text-gray-500">{{ $opname->user->name ?? 'Admin' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <x-button-action type="info" onclick="openModal('modal-detail-opname-{{ $opname->id }}')" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data stok opname</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Mulai Stok Opname --}}
    <x-modal 
        id="modal-stok-opname" 
        title="Mulai Sesi Stok Opname" 
        subtitle="Pemeriksaan dan audit stok fisik di gudang / etalase toko" 
        size="md"
    >
        <x-input label="Kode Opname" name="kode_opname" :value="'OPN-' . date('Ymd-His')" :readonly="true" />
        <x-input label="Tanggal Pemeriksaan" name="tanggal" type="date" :value="date('Y-m-d')" :required="true" />
        <x-select 
            label="Kategori Barang yang Diperiksa" 
            name="kategori" 
            :options="['Semua' => 'Semua Kategori'] + $kategoris->pluck('nama_kategori', 'nama_kategori')->toArray()" 
        />
        <x-input label="Catatan / Alasan Audit" name="catatan" placeholder="Contoh: Audit berkala akhir bulan" />
    </x-modal>
@endsection
