@extends('layouts.app')

@section('title', 'Cek Stok Barang')

@section('content')
    <x-page-header 
        title="Cek Stok Barang" 
        subtitle="Pengecekan ketersediaan suku cadang dan harga jual di etalase toko" 
        icon="stok" 
    />

    <x-table 
        id="table-stok-kasir" 
        :searchable="true" 
        searchPlaceholder="Cari nama barang..." 
        :headers="['ID', 'NAMA PRODUK', 'KATEGORI', 'SATUAN', 'HARGA JUAL', 'STOK', 'STATUS']"
    >
        @forelse($barangs as $barang)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">#{{ $barang->id_barang }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $barang->nama_barang }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $barang->satuan }}</td>
                <td class="py-3.5 px-4 font-bold text-gray-900">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 font-bold {{ $barang->stok <= 10 ? 'text-rose-500' : 'text-gray-800' }}">{{ $barang->stok }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    @if($barang->stok <= 0)
                        <x-badge type="danger" text="Habis" />
                    @elseif($barang->stok <= 10)
                        <x-badge type="warning" text="Stok Menipis" />
                    @else
                        <x-badge type="success" text="Tersedia" />
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data barang</td>
            </tr>
        @endforelse
    </x-table>
@endsection
