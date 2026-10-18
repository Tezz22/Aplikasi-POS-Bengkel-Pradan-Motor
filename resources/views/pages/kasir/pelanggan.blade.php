@extends('layouts.app')

@section('title', 'Pelanggan Kasir')

@section('content')
    <x-page-header 
        title="Data Pelanggan" 
        subtitle="Cari dan daftarkan data pemilik serta kendaraan motor pelanggan baru" 
        icon="pelanggan"
    >
        <x-slot:actions>
            <x-button 
                variant="primary" 
                icon="plus" 
                text="Pelanggan Baru" 
                onclick="openModal('modal-tambah-pelanggan')" 
            />
        </x-slot:actions>
    </x-page-header>

    <x-table 
        id="table-pelanggan-kasir" 
        :searchable="true" 
        searchPlaceholder="Cari plat nomor atau nama pelanggan..." 
        :headers="['ID', 'NAMA PELANGGAN', 'PLAT NOMOR', 'NO. TELEPON', 'AKSI']"
    >
        @forelse($pelanggans as $pelanggan)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">#{{ $pelanggan->id_pelanggan }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $pelanggan->nama }}</td>
                <td class="py-3.5 px-4 font-bold text-blue-600">{{ $pelanggan->plat_nomor ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $pelanggan->no_telepon ?? '-' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <x-button 
                        variant="primary" 
                        size="sm" 
                        text="Pilih Transaksi" 
                        :href="route('kasir.transaksi')" 
                    />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-400">Belum ada data pelanggan</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Pelanggan Kasir --}}
    <x-modal 
        id="modal-tambah-pelanggan" 
        title="Daftarkan Pelanggan Baru" 
        subtitle="Input data pemilik dan plat nomor motor pelanggan"
    >
        <x-input label="Nama Lengkap" name="nama" placeholder="Contoh: Budi Santoso (Maks. 50 Karakter)" maxlength="50" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Plat Nomor Motor" name="plat_nomor" placeholder="Contoh: B 1234 ABC (Maks. 12 Karakter)" maxlength="12" />
            <x-input label="No. Telepon / WhatsApp" name="no_telepon" placeholder="Contoh: 08123456789 (Maks. 15 Karakter)" maxlength="15" />
        </div>
    </x-modal>
@endsection
