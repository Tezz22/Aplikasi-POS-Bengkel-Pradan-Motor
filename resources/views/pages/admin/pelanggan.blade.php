@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
    @include('components.page-header', [
        'title'    => 'Data Pelanggan',
        'subtitle' => 'Daftar pelanggan dan riwayat kendaraan bengkel',
        'icon'     => 'pelanggan',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Tambah Pelanggan',
            'onclick' => "openModal('modal-tambah-pelanggan')"
        ])->render()
    ])

    @include('components.table', [
        'id'                => 'table-pelanggan',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari nama, plat nomor, atau no hp...',
        'headers'           => ['KODE', 'NAMA PELANGGAN', 'NO. TELEPON', 'PLAT NOMOR', 'ALAMAT', 'KUNJUNGAN', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">PLG-001</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Andi Saputra</td>
            <td class="py-3.5 px-4 text-gray-600">0812-3456-7890</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">B 1234 ABC</td>
            <td class="py-3.5 px-4 text-gray-500">Jl. Merdeka No. 10</td>
            <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">8 kali</span>
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-pelanggan')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus pelanggan?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">PLG-002</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Budi Wicaksono</td>
            <td class="py-3.5 px-4 text-gray-600">0813-9876-5432</td>
            <td class="py-3.5 px-4 font-medium text-gray-800">B 5678 XYZ</td>
            <td class="py-3.5 px-4 text-gray-500">Jl. Sudirman No. 45</td>
            <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">3 kali</span>
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-pelanggan')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus pelanggan?')){}"])
                </div>
            </td>
        </tr>
    </div>

    {{-- Modal Tambah Pelanggan --}}
    @component('components.modal', [
        'id'        => 'modal-tambah-pelanggan',
        'title'     => 'Tambah Pelanggan Baru',
        'subtitle'  => 'Masukkan data kontak dan data kendaraan pelanggan'
    ])
        @include('components.input', ['label' => 'Nama Lengkap', 'name' => 'nama', 'placeholder' => 'Nama pelanggan', 'required' => true])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'No. WhatsApp / HP', 'name' => 'no_hp', 'placeholder' => '08xxxxxxxxxx', 'required' => true])
            @include('components.input', ['label' => 'Plat Nomor Kendaraan', 'name' => 'plat_nomor', 'placeholder' => 'Contoh: B 1234 ABC', 'required' => true])
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'Tipe / Merk Motor', 'name' => 'tipe_motor', 'placeholder' => 'Vario 150, NMAX, Beat'])
            @include('components.input', ['label' => 'Tahun Perakitan', 'name' => 'tahun', 'placeholder' => '2022'])
        </div>
        @include('components.input', ['label' => 'Alamat Domisili', 'name' => 'alamat', 'placeholder' => 'Alamat lengkap'])
    @endcomponent
@endsection
