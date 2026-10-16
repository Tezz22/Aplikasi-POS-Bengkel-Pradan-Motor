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
        :headers="['KODE', 'NAMA LAYANAN', 'BIAYA JASA', 'ESTIMASI WAKTU', 'STATUS', 'AKSI']"
    >
        @forelse($layanans as $layanan)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $layanan->kode_layanan }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $layanan->nama_layanan }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-900">Rp {{ number_format($layanan->biaya, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $layanan->durasi ?? '-' }}</td>
                <td class="py-3.5 px-4">
                    <x-badge :type="$layanan->status === 'Tersedia' ? 'success' : 'neutral'" :text="$layanan->status" />
                </td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-layanan')" />
                        <x-button-action type="delete" onclick="if(confirm('Hapus layanan ini?')){{}}" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data layanan</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Layanan --}}
    <x-modal 
        id="modal-tambah-layanan" 
        title="Tambah Jasa / Layanan Baru" 
        subtitle="Masukkan nama servis dan tarif jasa mekanik"
    >
        <x-input label="Kode Layanan" name="kode_layanan" :value="'SRV-' . str_pad(($layanans->count() + 1), 2, '0', STR_PAD_LEFT)" :required="true" />
        <x-input label="Nama Layanan / Servis" name="nama_layanan" placeholder="Contoh: Bongkar Pasang Ban Tubeless" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Biaya Jasa (Rp)" name="biaya" type="number" placeholder="0" :required="true" />
            <x-input label="Estimasi Durasi Pengerjaan" name="durasi" placeholder="Contoh: 30 Menit" />
        </div>
        <x-input label="Keterangan Pengerjaan" name="keterangan" placeholder="Termasuk pembersihan CVT, pengecekan roller, dll" />
    </x-modal>
@endsection
