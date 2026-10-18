@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
    {{-- Page Header --}}
    <x-page-header 
        title="Barang Masuk" 
        subtitle="Kelola inventaris pembelian dan pasokan suku cadang dari supplier" 
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
        searchPlaceholder="Cari no. referensi, tanggal, atau supplier...." 
        :headers="['NO. REFERENSI', 'TANGGAL', 'SUPPLIER', 'BARANG', 'JUMLAH', 'HARGA BELI', 'SUBTOTAL', 'DICATAT OLEH', 'AKSI']"
    >
        @forelse($pembelians as $pembelian)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $pembelian->no_referensi }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($pembelian->tanggal)->translatedFormat('d M Y') }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-800">{{ $pembelian->supplier->nama_supplier ?? '-' }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $pembelian->barang->nama_barang ?? '-' }}</td>
                <td class="py-3.5 px-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-800">
                        {{ $pembelian->jumlah }} {{ $pembelian->barang->satuan ?? 'Pcs' }}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-gray-600">Rp {{ number_format($pembelian->harga_beli, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 font-bold text-emerald-600">Rp {{ number_format($pembelian->subtotal, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $pembelian->user->nama ?? $pembelian->user->name ?? 'Admin' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <form action="{{ route('admin.barang-masuk.destroy', $pembelian->id_tr_pembelian) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus catatan penerimaan barang ini? Stok barang akan dikurangi kembali.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-button-action type="delete" buttonType="submit" title="Batalkan Penerimaan" />
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="py-8 text-center text-gray-400">Belum ada data barang masuk</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Catat Barang Masuk --}}
    <x-modal 
        id="modal-barang-masuk" 
        title="Catat Barang Masuk" 
        subtitle="Formulir penerimaan barang masuk dari supplier" 
        size="lg"
        :formAction="route('admin.barang-masuk.store')"
        formMethod="POST"
        submitText="Simpan & Tambah Stok"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="No. Referensi" name="no_referensi" :value="'REF-' . date('Ym') . '-' . str_pad(($pembelians->count() + 1), 3, '0', STR_PAD_LEFT)" maxlength="25" :required="true" />
            <x-input label="Tanggal Masuk" name="tanggal" type="date" :value="date('Y-m-d')" :required="true" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-select 
                label="Supplier" 
                name="id_supplier" 
                :options="$suppliers->pluck('nama_supplier', 'id_supplier')->toArray()" 
                :required="true" 
            />
            <x-select 
                label="Pilih Barang" 
                name="id_barang" 
                :options="$barangs->pluck('nama_barang', 'id_barang')->toArray()" 
                :required="true" 
            />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Jumlah Masuk" name="jumlah" type="number" placeholder="1" :required="true" />
            <x-input label="Harga Beli Satuan (Rp)" name="harga_beli" type="number" placeholder="0" :required="true" />
        </div>
        <x-input label="Keterangan Tambahan" name="keterangan" placeholder="Keterangan pasokan atau kondisi barang (Maks. 255 Karakter)" maxlength="255" />
    </x-modal>
@endsection
