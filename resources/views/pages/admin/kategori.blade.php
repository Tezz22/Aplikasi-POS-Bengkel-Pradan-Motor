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
                        
                        <form action="{{ route('admin.kategori.destroy', $kategori->id_kategori) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $kategori->nama_kategori }}\'?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button-action type="delete" buttonType="submit" />
                        </form>
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
    @endforeach
@endsection
