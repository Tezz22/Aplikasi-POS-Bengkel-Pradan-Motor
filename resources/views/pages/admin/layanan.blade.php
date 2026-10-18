@extends('layouts.app')

@section('title', 'Data Layanan & Jasa')

@section('content')
    <x-page-header 
        title="Data Layanan & Jasa" 
        subtitle="Daftar tarif jasa servis mekanik dan perawatan motor" 
        icon="layanan"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Tambah Layanan" onclick="openModal('modal-tambah-layanan')" />
        </x-slot:actions>
    </x-page-header>

    <x-table 
        id="table-layanan" 
        :searchable="true" 
        searchPlaceholder="Cari jenis servis / jasa..." 
        :headers="['ID', 'NAMA JASA / LAYANAN', 'TARIF JASA', 'AKSI']"
    >
        @forelse($layanans as $layanan)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">#{{ $layanan->id_jasa }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $layanan->nama_jasa }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-900">Rp {{ number_format($layanan->tarif, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-layanan')" />
                        <x-button-action type="delete" onclick="if(confirm('Hapus jasa ini?')){{}}" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-400">Belum ada data layanan</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Layanan --}}
    <x-modal 
        id="modal-tambah-layanan" 
        title="Tambah Jasa / Layanan Baru" 
        subtitle="Masukkan nama servis dan tarif jasa mekanik"
    >
        <x-input label="Nama Jasa / Servis" name="nama_jasa" placeholder="Contoh: Servis Ringan / Tune Up (Maks. 120 Karakter)" maxlength="120" :required="true" />
        <x-input label="Tarif Jasa (Rp)" name="tarif" type="number" placeholder="0" :required="true" />
    </x-modal>
@endsection
