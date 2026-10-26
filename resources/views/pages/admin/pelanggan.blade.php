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
                        <x-button-action type="delete" onclick="openModal('modal-hapus-pelanggan-{{ $pelanggan->id_pelanggan }}')" />
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
        <x-input 
            label="Nama Lengkap" 
            name="nama" 
            placeholder="Contoh: Budi Santoso (2-50 Karakter)" 
            minlength="2" 
            maxlength="50" 
            :required="true" 
        />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input 
                label="No. Telepon / WhatsApp" 
                name="no_telepon" 
                type="tel" 
                placeholder="Contoh: 08123456789" 
                minlength="8" 
                maxlength="15" 
                pattern="[0-9+\-\s]{8,15}" 
                title="Nomor telepon minimal 8-15 digit" 
            />
            <x-input 
                label="Plat Nomor Kendaraan" 
                name="plat_nomor" 
                placeholder="Contoh: B 1234 ABC" 
                minlength="3" 
                maxlength="12" 
                oninput="this.value = this.value.toUpperCase()" 
            />
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
            <x-input 
                label="Nama Lengkap" 
                name="nama" 
                :value="$pelanggan->nama" 
                minlength="2" 
                maxlength="50" 
                :required="true" 
            />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input 
                    label="No. Telepon / WhatsApp" 
                    name="no_telepon" 
                    type="tel" 
                    :value="$pelanggan->no_telepon" 
                    minlength="8" 
                    maxlength="15" 
                    pattern="[0-9+\-\s]{8,15}" 
                    title="Nomor telepon minimal 8-15 digit" 
                />
                <x-input 
                    label="Plat Nomor Kendaraan" 
                    name="plat_nomor" 
                    :value="$pelanggan->plat_nomor" 
                    minlength="3" 
                    maxlength="12" 
                    oninput="this.value = this.value.toUpperCase()" 
                />
            </div>
        </x-modal>

        {{-- Modal Konfirmasi Hapus Pelanggan --}}
        <x-modal 
            id="modal-hapus-pelanggan-{{ $pelanggan->id_pelanggan }}" 
            title="Konfirmasi Hapus Pelanggan" 
            subtitle="Tindakan ini tidak dapat dibatalkan"
            :formAction="route('admin.pelanggan.destroy', $pelanggan->id_pelanggan)"
            formMethod="DELETE"
            submitText="Ya, Hapus Pelanggan"
            submitVariant="danger"
            size="sm"
        >
            <div class="text-center py-2">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <x-icon name="alert" :size="24" />
                </div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Hapus Pelanggan Ini?</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus data pelanggan <span class="font-bold text-gray-900">"{{ $pelanggan->nama }}"</span>
                    @if($pelanggan->plat_nomor)
                        dengan plat kendaraan <span class="font-bold text-gray-900">[{{ $pelanggan->plat_nomor }}]</span>
                    @endif
                    ?
                </p>
            </div>
        </x-modal>
    @endforeach
@endsection
