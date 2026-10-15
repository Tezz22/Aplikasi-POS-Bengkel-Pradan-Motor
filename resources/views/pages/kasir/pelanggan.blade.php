@extends('layouts.app')

@section('title', 'Pelanggan Kasir')

@section('content')
    @include('components.page-header', [
        'title'    => 'Data Pelanggan',
        'subtitle' => 'Cari dan daftarkan data pemilik serta kendaraan motor pelanggan baru',
        'icon'     => 'pelanggan',
        'actions'  => view('components.button', [
            'variant' => 'primary',
            'icon'    => 'plus',
            'text'    => 'Pelanggan Baru',
            'onclick' => "openModal('modal-tambah-pelanggan')"
        ])->render()
    ])

    @include('components.table', [
        'id'                => 'table-pelanggan-kasir',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari plat nomor atau nama pelanggan...',
        'headers'           => ['KODE', 'NAMA PELANGGAN', 'PLAT NOMOR', 'MOTOR', 'NO. WHATSAPP', 'AKSI']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">PLG-001</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Andi Saputra</td>
            <td class="py-3.5 px-4 font-bold text-blue-600">B 1234 ABC</td>
            <td class="py-3.5 px-4 text-gray-600">Vario 150 (2021)</td>
            <td class="py-3.5 px-4 text-gray-600">0812-3456-7890</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button', [
                    'variant' => 'primary',
                    'size'    => 'sm',
                    'text'    => 'Pilih Servis',
                    'href'    => route('kasir.transaksi')
                ])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">PLG-002</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Budi Wicaksono</td>
            <td class="py-3.5 px-4 font-bold text-blue-600">B 5678 XYZ</td>
            <td class="py-3.5 px-4 text-gray-600">Beat Street (2023)</td>
            <td class="py-3.5 px-4 text-gray-600">0813-9876-5432</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.button', [
                    'variant' => 'primary',
                    'size'    => 'sm',
                    'text'    => 'Pilih Servis',
                    'href'    => route('kasir.transaksi')
                ])
            </td>
        </tr>
    </div>

    {{-- Modal Tambah Pelanggan Kasir --}}
    @component('components.modal', [
        'id'        => 'modal-tambah-pelanggan',
        'title'     => 'Daftarkan Pelanggan Cepat',
        'subtitle'  => 'Input data motor dan kontak pemilik untuk servis hari ini'
    ])
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'Plat Nomor Motor', 'name' => 'plat_nomor', 'placeholder' => 'Contoh: B 1234 ABC', 'required' => true])
            @include('components.input', ['label' => 'Nama Pemilik', 'name' => 'nama', 'placeholder' => 'Nama pelanggan', 'required' => true])
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @include('components.input', ['label' => 'No. WhatsApp / HP', 'name' => 'no_hp', 'placeholder' => '08xxxxxxxxxx', 'required' => true])
            @include('components.input', ['label' => 'Tipe Motor', 'name' => 'tipe_motor', 'placeholder' => 'Vario, NMAX, Beat, Scoopy'])
        </div>
        @include('components.input', ['label' => 'Keluhan / Catatan Awal Servis', 'name' => 'keluhan', 'placeholder' => 'Contoh: Rem bunyi, tarikan berat'])
    @endcomponent
@endsection
