@extends('layouts.app')

@section('title', 'Kategori Barang')

@section('content')
    <x-page-header 
        title="Kategori Barang" 
        subtitle="Klasifikasi dan pengelompokan suku cadang bengkel" 
        icon="kategori"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Tambah Kategori" onclick="openModal('modal-tambah-kategori')" />
        </x-slot:actions>
    </x-page-header>

    <x-table 
        id="table-kategori" 
        :searchable="true" 
        searchPlaceholder="Cari kategori..." 
        :headers="['ID', 'NAMA KATEGORI', 'JUMLAH ITEM', 'AKSI']"
    >
        @forelse($kategoris as $kategori)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">#{{ $kategori->id_kategori }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $kategori->nama_kategori }}</td>
                <td class="py-3.5 px-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                        {{ $kategori->barang_count ?? $kategori->barang()->count() }} Item
                    </span>
                </td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-kategori-{{ $kategori->id_kategori }}')" />
                        <x-button-action type="delete" onclick="openModal('modal-hapus-kategori-{{ $kategori->id_kategori }}')" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-400">Belum ada data kategori</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Kategori --}}
    <x-modal 
        id="modal-tambah-kategori" 
        title="Tambah Kategori Barang" 
        subtitle="Buat kategori baru untuk pengelompokan suku cadang"
        :formAction="route('admin.kategori.store')"
        formMethod="POST"
        submitText="Simpan Kategori"
    >
        <x-input 
            label="Nama Kategori" 
            name="nama_kategori" 
            placeholder="Contoh: Ban & Roda (Maks. 25 Karakter)" 
            maxlength="25" 
            :required="true" 
        />
    </x-modal>

    {{-- Modals Edit Kategori --}}
    @foreach($kategoris as $kategori)
        <x-modal 
            id="modal-edit-kategori-{{ $kategori->id_kategori }}" 
            title="Edit Kategori #{{ $kategori->id_kategori }}" 
            subtitle="Perbarui nama kategori suku cadang"
            :formAction="route('admin.kategori.update', $kategori->id_kategori)"
            formMethod="PUT"
            submitText="Perbarui Kategori"
        >
            <x-input 
                label="Nama Kategori" 
                name="nama_kategori" 
                :value="$kategori->nama_kategori" 
                maxlength="25" 
                :required="true" 
            />
        </x-modal>

        {{-- Modal Konfirmasi Hapus Kategori --}}
        <x-modal 
            id="modal-hapus-kategori-{{ $kategori->id_kategori }}" 
            title="Konfirmasi Hapus Kategori" 
            subtitle="Tindakan ini tidak dapat dibatalkan"
            :formAction="route('admin.kategori.destroy', $kategori->id_kategori)"
            formMethod="DELETE"
            submitText="Ya, Hapus Kategori"
            submitVariant="danger"
            size="sm"
        >
            <div class="text-center py-2">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <x-icon name="alert" :size="24" />
                </div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Hapus Kategori Ini?</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus kategori <span class="font-bold text-gray-900">"{{ $kategori->nama_kategori }}"</span>?
                </p>
            </div>
        </x-modal>
    @endforeach
@endsection
