@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
    <x-page-header 
        title="Kelola Pengguna" 
        subtitle="Daftar akun administrator dan staf kasir sistem POS" 
        icon="users"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Tambah Pengguna" onclick="openModal('modal-tambah-pengguna')" />
        </x-slot:actions>
    </x-page-header>

    <x-table 
        id="table-pengguna" 
        :searchable="true" 
        searchPlaceholder="Cari nama atau email pengguna..." 
        :headers="['PENGGUNA', 'EMAIL', 'ROLE', 'STATUS', 'TERDAFTAR', 'AKSI']"
    >
        @forelse($users as $user)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }} font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr($user->nama ?? $user->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="font-bold text-gray-900 block">{{ $user->nama ?? $user->name }}</span>
                            <span class="text-[10px] text-gray-400">{{ $user->role === 'admin' ? 'Administrator' : 'Kasir Bengkel' }}</span>
                        </div>
                    </div>
                </td>
                <td class="py-3.5 px-4 text-gray-600">{{ $user->email }}</td>
                <td class="py-3.5 px-4">
                    <x-badge :type="$user->role === 'admin' ? 'info' : 'success'" :text="ucfirst($user->role)" />
                </td>
                <td class="py-3.5 px-4">
                    <x-badge :type="$user->status === 'aktif' ? 'success' : 'danger'" :text="ucfirst($user->status ?? 'aktif')" />
                </td>
                <td class="py-3.5 px-4 text-gray-400">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="edit" onclick="openModal('modal-edit-pengguna-{{ $user->id_user }}')" />
                        @if(($user->id_user ?? $user->id) !== auth()->id())
                            <form action="{{ route('admin.pengguna.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->nama }}?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <x-button-action type="delete" buttonType="submit" />
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data pengguna</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Tambah Pengguna --}}
    <x-modal 
        id="modal-tambah-pengguna" 
        title="Tambah Pengguna Baru" 
        subtitle="Buat akun staf atau administrator sistem POS"
        :formAction="route('admin.pengguna.store')"
        formMethod="POST"
        submitText="Simpan Pengguna"
    >
        <x-input label="Nama Lengkap" name="nama" placeholder="Contoh: Budi Santoso (Maks. 50 Karakter)" maxlength="50" :required="true" />
        <x-input label="Alamat Email" name="email" type="email" placeholder="nama@pradanmotor.com (Maks. 50 Karakter)" maxlength="50" :required="true" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-select 
                label="Role / Hak Akses" 
                name="role" 
                :options="['admin' => 'Admin (Akses Penuh)', 'kasir' => 'Kasir (Transaksi POS)']" 
                :required="true" 
            />
            <x-select 
                label="Status Akun" 
                name="status" 
                :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" 
                :required="true" 
            />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Password" name="password" type="password" placeholder="Minimal 6 karakter" :required="true" />
            <x-input label="Konfirmasi Password" name="password_confirmation" type="password" placeholder="Ulangi password" :required="true" />
        </div>
    </x-modal>

    {{-- Modals Edit Pengguna --}}
    @foreach($users as $user)
        <x-modal 
            id="modal-edit-pengguna-{{ $user->id_user }}" 
            title="Edit Pengguna #{{ $user->id_user }}" 
            subtitle="Perbarui data akun {{ $user->nama }}"
            :formAction="route('admin.pengguna.update', $user->id_user)"
            formMethod="PUT"
            submitText="Perbarui Pengguna"
        >
            <x-input label="Nama Lengkap" name="nama" :value="$user->nama" maxlength="50" :required="true" />
            <x-input label="Alamat Email" name="email" type="email" :value="$user->email" maxlength="50" :required="true" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-select 
                    label="Role / Hak Akses" 
                    name="role" 
                    :options="['admin' => 'Admin (Akses Penuh)', 'kasir' => 'Kasir (Transaksi POS)']" 
                    :value="$user->role"
                    :required="true" 
                />
                <x-select 
                    label="Status Akun" 
                    name="status" 
                    :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" 
                    :value="$user->status ?? 'aktif'"
                    :required="true" 
                />
            </div>
            <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-100 text-[11px] text-amber-800">
                Kosongkan input password di bawah jika Anda tidak ingin mengubah password akun ini.
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="Password Baru (Opsional)" name="password" type="password" placeholder="Minimal 6 karakter" />
                <x-input label="Konfirmasi Password Baru" name="password_confirmation" type="password" placeholder="Ulangi password baru" />
            </div>
        </x-modal>
    @endforeach
@endsection
