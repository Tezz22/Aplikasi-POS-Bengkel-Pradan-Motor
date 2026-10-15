@extends('layouts.app')

@section('title', 'Data Layanan & Jasa')

@section('content')
    @include('components.page-header', [
        'title'    => 'Data Layanan & Jasa',
        'subtitle' => 'Daftar tarif jasa servis mekanik dan perawatan motor',
        'icon'     => 'layanan',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Tambah Layanan',
            'onclick' => "openModal('modal-tambah-layanan')"
        ])->render()
    ])

    @include('components.table', [
        'id'                => 'table-layanan',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari jenis servis / jasa...',
        'headers'           => ['KODE', 'NAMA LAYANAN', 'BIAYA JASA', 'ESTIMASI WAKTU', 'STATUS', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">SRV-01</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Servis Ringan / Tune Up</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 45.000</td>
            <td class="py-3.5 px-4 text-gray-500">30 Menit</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Tersedia'])
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-layanan')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus layanan?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">SRV-02</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Ganti Oli Mesin & Gardan</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 10.000</td>
            <td class="py-3.5 px-4 text-gray-500">15 Menit</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Tersedia'])
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-layanan')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus layanan?')){}"])
                </div>
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">SRV-03</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Servis CVT Lengkap</td>
            <td class="py-3.5 px-4 font-medium text-gray-900">Rp 65.000</td>
            <td class="py-3.5 px-4 text-gray-500">45 Menit</td>
            <td class="py-3.5 px-4">
                @include('components.badge', ['type' => 'success', 'text' => 'Tersedia'])
            </td>
            <td class="py-3.5 px-4 last:pr-6">
                <div class="flex items-center gap-1">
                    @include('components.button-action', ['type' => 'edit', 'onclick' => "openModal('modal-edit-layanan')"])
                    @include('components.button-action', ['type' => 'delete', 'onclick' => "if(confirm('Hapus layanan?')){}"])
                </div>
            </td>
        </tr>
    </div>

    {{-- Modal Tambah Layanan --}}
    @component('components.modal', [
        'id'        => 'modal-tambah-layanan',
        'title'     => 'Tambah Jasa / Layanan Baru',
        'subtitle'  => 'Masukkan nama servis dan tarif jasa mekanik'
    ])
        @include('components.input', ['label' => 'Kode Layanan', 'name' => 'kode_layanan', 'value' => 'SRV-04', 'required' => true])
        @include('components.input', ['label' => 'Nama Layanan / Servis', 'name' => 'nama_layanan', 'placeholder' => 'Contoh: Bongkar Pasang Ban Tubeless', 'required' => true])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'Biaya Jasa (Rp)', 'name' => 'biaya', 'type' => 'number', 'placeholder' => '0', 'required' => true])
            @include('components.input', ['label' => 'Estimasi Durasi Pengerjaan', 'name' => 'durasi', 'placeholder' => 'Contoh: 30 Menit'])
        </div>
        @include('components.input', ['label' => 'Keterangan Pengerjaan', 'name' => 'keterangan', 'placeholder' => 'Termasuk pembersihan CVT, pengecekan roller, dll'])
    @endcomponent
@endsection
