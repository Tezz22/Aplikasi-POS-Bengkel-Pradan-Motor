@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
    <x-page-header 
        title="Data Pelanggan" 
        subtitle="Daftar pelanggan dan riwayat kendaraan bengkel" 
        icon="pelanggan"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Tambah Pelanggan" onclick="openModal('modal-tambah-pelanggan')" />
        </x-slot:actions>
    </x-page-header>

    <x-table 
        id="table-pelanggan" 
        :searchable="true" 
        searchPlaceholder="Cari nama, plat nomor, atau no hp..." 
        :headers="['KODE', 'NAMA PELANGGAN', 'NO. TELEPON', 'PLAT NOMOR', 'ALAMAT', 'KUNJUNGAN', 'AKSI']"
    >
        @forelse($pelanggans as $pelanggan)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $pelanggan->kode_pelanggan }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $pelanggan->nama }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $pelanggan->no_hp }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-800">{{ $pelanggan->plat_nomor }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $pelanggan->alamat ?? '-' }}</td>
                <td class="py-3.5 px-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                        {{ $pelanggan->penjualan_count ?? $pelanggan->penjualan()->count() }} kali
                    </span>
                </td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-pelanggan')" />
                        <x-button-action type="delete" onclick="if(confirm('Hapus pelanggan ini?')){{}}" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data pelanggan</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Pelanggan --}}
    <x-modal 
        id="modal-tambah-pelanggan" 
        title="Tambah Pelanggan Baru" 
        subtitle="Masukkan data kontak dan data kendaraan pelanggan"
    >
        <x-input label="Nama Lengkap" name="nama" placeholder="Nama pelanggan" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="No. WhatsApp / HP" name="no_hp" placeholder="08xxxxxxxxxx" :required="true" />
            <x-input label="Plat Nomor Kendaraan" name="plat_nomor" placeholder="Contoh: B 1234 ABC" :required="true" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Tipe / Merk Motor" name="tipe_motor" placeholder="Vario 150, NMAX, Beat" />
            <x-input label="Tahun Perakitan" name="tahun" placeholder="2022" />
        </div>
        <x-input label="Alamat Domisili" name="alamat" placeholder="Alamat lengkap" />
    </x-modal>
@endsection
