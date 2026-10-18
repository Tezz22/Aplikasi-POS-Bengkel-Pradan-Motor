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
        searchPlaceholder="Cari nama, plat nomor, atau no telepon..." 
        :headers="['ID', 'NAMA PELANGGAN', 'NO. TELEPON', 'PLAT NOMOR', 'TOTAL TRANSAKSI', 'AKSI']"
    >
        @forelse($pelanggans as $pelanggan)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">#{{ $pelanggan->id_pelanggan }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $pelanggan->nama }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $pelanggan->no_telepon ?? '-' }}</td>
                <td class="py-3.5 px-4 font-medium text-gray-800">{{ $pelanggan->plat_nomor ?? '-' }}</td>
                <td class="py-3.5 px-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                        {{ $pelanggan->penjualan_count ?? $pelanggan->penjualan()->count() }} kali
                    </span>
                </td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-pelanggan-{{ $pelanggan->id_pelanggan }}')" />
                        
                        <form action="{{ route('admin.pelanggan.destroy', $pelanggan->id_pelanggan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan \'{{ $pelanggan->nama }}\'?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button-action type="delete" buttonType="submit" />
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data pelanggan</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Pelanggan --}}
    <x-modal 
        id="modal-tambah-pelanggan" 
        title="Tambah Pelanggan Baru" 
        subtitle="Masukkan nama pelanggan, no telepon, dan plat kendaraan"
        :formAction="route('admin.pelanggan.store')"
        formMethod="POST"
        submitText="Simpan Pelanggan"
    >
        <x-input label="Nama Lengkap" name="nama" placeholder="Contoh: Budi Santoso (Maks. 50 Karakter)" maxlength="50" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="No. Telepon / WhatsApp" name="no_telepon" placeholder="Contoh: 08123456789 (Maks. 15 Karakter)" maxlength="15" />
            <x-input label="Plat Nomor Kendaraan" name="plat_nomor" placeholder="Contoh: B 1234 ABC (Maks. 12 Karakter)" maxlength="12" />
        </div>
    </x-modal>

    {{-- Modals Edit Pelanggan --}}
    @foreach($pelanggans as $pelanggan)
        <x-modal 
            id="modal-edit-pelanggan-{{ $pelanggan->id_pelanggan }}" 
            title="Edit Pelanggan #{{ $pelanggan->id_pelanggan }}" 
            subtitle="Perbarui data kontak pelanggan {{ $pelanggan->nama }}"
            :formAction="route('admin.pelanggan.update', $pelanggan->id_pelanggan)"
            formMethod="PUT"
            submitText="Perbarui Pelanggan"
        >
            <x-input label="Nama Lengkap" name="nama" :value="$pelanggan->nama" maxlength="50" :required="true" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="No. Telepon / WhatsApp" name="no_telepon" :value="$pelanggan->no_telepon" maxlength="15" />
                <x-input label="Plat Nomor Kendaraan" name="plat_nomor" :value="$pelanggan->plat_nomor" maxlength="12" />
            </div>
        </x-modal>
    @endforeach
@endsection
