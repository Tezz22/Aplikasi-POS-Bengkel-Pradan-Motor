@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
    @include('components.page-header', [
        'title'    => 'Kelola Pengguna',
        'subtitle' => 'Daftar akun administrator dan staf kasir sistem POS',
        'icon'     => 'users',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Tambah Pengguna',
            'onclick' => "openModal('modal-tambah-pengguna')"
        ])->render()
    ])

    @include('components.table', [
        'id'                => 'table-pengguna',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari nama atau email pengguna...',
        'headers'           => ['PENGGUNA', 'EMAIL', 'ROLE', 'STATUS', 'TERDAFTAR', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                        T
                    </div>
                    <div>
                        <span class="font-bold text-gray-900 block">Tezzy</span>
                        <span class="text-[10px] text-gray-400">Admin Utama</span>
                    </div>
                </div>
            </td>
            <td class="py-3.5 px-4 text-gray-600">admin@pradanmotor.com</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'info', 'text' => 'Admin'])
            </td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Aktif'])
            </td>
            <td class="py-3.5 px-4 text-gray-400">01 Jan 2026</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-pengguna')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus pengguna?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">
                        J
                    </div>
                    <div>
                        <span class="font-bold text-gray-900 block">Joo</span>
                        <span class="text-[10px] text-gray-400">Kasir Bengkel</span>
                    </div>
                </div>
            </td>
            <td class="py-3.5 px-4 text-gray-600">kasir@pradanmotor.com</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Kasir'])
            </td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Aktif'])
            </td>
            <td class="py-3.5 px-4 text-gray-400">10 Jan 2026</td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-pengguna')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus pengguna?')){}"])
                </div>
            </td>
        </tr>
    </div>

    {{-- Modal Tambah Pengguna --}}
    @component('components.modal', [
        'id'        => 'modal-tambah-pengguna',
        'title'     => 'Tambah Pengguna Baru',
        'subtitle'  => 'Buat akun staf atau administrator sistem POS'
    ])
        @include('components.input', ['label' => 'Nama Lengkap', 'name' => 'name', 'placeholder' => 'Nama staf', 'required' => true])
        @include('components.input', ['label' => 'Alamat Email', 'name' => 'email', 'type' => 'email', 'placeholder' => 'nama@pradanmotor.com', 'required' => true])
        @include('components.select', [
            'label'   => 'Role / Hak Akses',
            'name'    => 'role',
            'options' => ['admin' => 'Admin (Akses Penuh)', 'kasir' => 'Kasir (Transaksi & POS)'],
            'required' => true
        ])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'Password', 'name' => 'password', 'type' => 'password', 'required' => true])
            @include('components.input', ['label' => 'Konfirmasi Password', 'name' => 'password_confirmation', 'type' => 'password', 'required' => true])
        </div>
    @endcomponent
@endsection
