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
        :headers="['KODE', 'NAMA PELANGGAN', 'PLAT NOMOR', 'MOTOR', 'NO. WHATSAPP', 'AKSI']"
    >
        @forelse($pelanggans as $pelanggan)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $pelanggan->kode_pelanggan }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $pelanggan->nama }}</td>
                <td class="py-3.5 px-4 font-bold text-blue-600">{{ $pelanggan->plat_nomor }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $pelanggan->tipe_motor ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $pelanggan->no_hp ?? '-' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <x-button 
                        variant="primary" 
                        size="sm" 
                        text="Pilih Servis" 
                        :href="route('kasir.transaksi')" 
                    />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data pelanggan</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Pelanggan Kasir --}}
    <x-modal 
        id="modal-tambah-pelanggan" 
        title="Daftarkan Pelanggan Cepat" 
        subtitle="Input data motor dan kontak pemilik untuk servis hari ini"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Plat Nomor Motor" name="plat_nomor" placeholder="Contoh: B 1234 ABC" :required="true" />
            <x-input label="Nama Pemilik" name="nama" placeholder="Nama pelanggan" :required="true" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="No. WhatsApp / HP" name="no_hp" placeholder="08xxxxxxxxxx" :required="true" />
            <x-input label="Tipe Motor" name="tipe_motor" placeholder="Vario, NMAX, Beat, Scoopy" />
        </div>
        <x-input label="Keluhan / Catatan Awal Servis" name="keluhan" placeholder="Contoh: Rem bunyi, tarikan berat" />
    </x-modal>
@endsection
