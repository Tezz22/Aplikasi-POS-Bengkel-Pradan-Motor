@extends('layouts.app')

@section('title', 'Cek Stok Barang')

@section('content')
    @include('components.page-header', [
        'title'    => 'Cek Stok Barang',
        'subtitle' => 'Pengecekan ketersediaan suku cadang dan harga jual di etalase toko',
        'icon'     => 'stok'
    ])

    @include('components.table', [
        'id'                => 'table-stok-kasir',
        'searchable'        => true,
        'searchPlaceholder' => 'Cari nama barang atau barcode...',
        'headers'           => ['KODE', 'NAMA PRODUK', 'KATEGORI', 'SATUAN', 'HARGA JUAL', 'STOK', 'STATUS']
    ])
        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BRG-001</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Oli Mesin 1L</td>
            <td class="py-3.5 px-4 text-gray-600">Oli</td>
            <td class="py-3.5 px-4 text-gray-500">Botol</td>
            <td class="py-3.5 px-4 font-bold text-gray-900">Rp 20.000</td>
            <td class="py-3.5 px-4 font-bold text-rose-500">9</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.badge', ['type' => 'warning', 'text' => 'Stok Menipis'])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BRG-002</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Kampas Rem</td>
            <td class="py-3.5 px-4 text-gray-600">Rem</td>
            <td class="py-3.5 px-4 text-gray-500">Pcs</td>
            <td class="py-3.5 px-4 font-bold text-gray-900">Rp 8.500</td>
            <td class="py-3.5 px-4 font-bold text-gray-800">100</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.badge', ['type' => 'success', 'text' => 'Tersedia'])
            </td>
        </tr>

        <tr class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BRG-003</td>
            <td class="py-3.5 px-4 font-semibold text-gray-900">Busi Standar</td>
            <td class="py-3.5 px-4 text-gray-600">Kelistrikan</td>
            <td class="py-3.5 px-4 text-gray-500">Pcs</td>
            <td class="py-3.5 px-4 font-bold text-gray-900">Rp 25.000</td>
            <td class="py-3.5 px-4 font-bold text-gray-800">50</td>
            <td class="py-3.5 px-4 last:pr-6">
                @include('components.badge', ['type' => 'success', 'text' => 'Tersedia'])
            </td>
        </tr>
    </div>
@endsection
