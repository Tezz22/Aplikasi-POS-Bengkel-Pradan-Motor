@extends('layouts.app')

@section('title', 'Transaksi Penjualan (POS)')

@section('content')
    @include('components.page-header', [
        'title'    => 'Transaksi Kasir (POS)',
        'subtitle' => 'Input transaksi servis kendaraan dan penjualan suku cadang',
        'icon'     => 'transaksi'
    ])

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Katalog Barang & Jasa (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between gap-3 mb-4">
                @include('components.search', [
                    'placeholder' => 'Cari suku cadang atau jenis jasa...',
                    'target'      => '#pos-catalogue'
                ])

                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl text-xs font-semibold">
                    <button type="button" class="px-3 py-1 bg-white text-blue-600 rounded-lg shadow-2xs">Semua</button>
                    <button type="button" class="px-3 py-1 text-gray-500 hover:text-gray-800">Barang</button>
                    <button type="button" class="px-3 py-1 text-gray-500 hover:text-gray-800">Jasa Servis</button>
                </div>
            </div>

            <!-- List Grid Items -->
            <div id="pos-catalogue" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[550px] overflow-y-auto pr-1">
                <!-- Item 1 -->
                <div class="p-3.5 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/20 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500 bg-blue-50 px-2 py-0.5 rounded-md">Oli</span>
                            <span class="text-[10px] text-gray-400">Stok: 9</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-xs mt-2">Oli Mesin 1L</h4>
                        <p class="text-[11px] text-gray-400">BRG-001</p>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                        <span class="font-bold text-gray-900 text-sm">Rp 20.000</span>
                        @include('components.button', [
                            'size'    => 'sm',
                            'variant' => 'primary',
                            'icon'    => 'plus',
                            'text'    => 'Tambah',
                            'onclick' => "alert('Ditambahkan ke keranjang')"
                        ])
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="p-3.5 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/20 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-500 bg-purple-50 px-2 py-0.5 rounded-md">Sparepart</span>
                            <span class="text-[10px] text-gray-400">Stok: 100</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-xs mt-2">Kampas Rem Depan</h4>
                        <p class="text-[11px] text-gray-400">BRG-002</p>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                        <span class="font-bold text-gray-900 text-sm">Rp 8.500</span>
                        @include('components.button', [
                            'size'    => 'sm',
                            'variant' => 'primary',
                            'icon'    => 'plus',
                            'text'    => 'Tambah',
                            'onclick' => "alert('Ditambahkan ke keranjang')"
                        ])
                    </div>
                </div>

                <!-- Item 3 (Jasa) -->
                <div class="p-3.5 rounded-xl border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/20 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-md">Jasa Servis</span>
                            <span class="text-[10px] text-gray-400">30 Menit</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-xs mt-2">Servis Ringan / Tune Up</h4>
                        <p class="text-[11px] text-gray-400">SRV-01</p>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                        <span class="font-bold text-gray-900 text-sm">Rp 45.000</span>
                        @include('components.button', [
                            'size'    => 'sm',
                            'variant' => 'success',
                            'icon'    => 'plus',
                            'text'    => 'Tambah',
                            'onclick' => "alert('Ditambahkan ke keranjang')"
                        ])
                    </div>
                </div>

                <!-- Item 4 (Jasa) -->
                <div class="p-3.5 rounded-xl border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/20 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-md">Jasa Servis</span>
                            <span class="text-[10px] text-gray-400">15 Menit</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-xs mt-2">Ganti Oli Mesin & Gardan</h4>
                        <p class="text-[11px] text-gray-400">SRV-02</p>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                        <span class="font-bold text-gray-900 text-sm">Rp 10.000</span>
                        @include('components.button', [
                            'size'    => 'sm',
                            'variant' => 'success',
                            'icon'    => 'plus',
                            'text'    => 'Tambah',
                            'onclick' => "alert('Ditambahkan ke keranjang')"
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- Keranjang Transaksi & Checkout (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="font-bold text-sm text-gray-900">Keranjang Kasir</h3>
                    <span class="text-xs text-blue-600 font-semibold">2 Item</span>
                </div>

                <!-- Pilih Pelanggan -->
                <div class="mb-4">
                    @include('components.select', [
                        'label'   => 'Pilih Pelanggan / Plat Nomor',
                        'name'    => 'pelanggan_id',
                        'options' => [
                            '1' => 'Andi Saputra - B 1234 ABC (Vario 150)',
                            '2' => 'Budi Wicaksono - B 5678 XYZ (Beat)',
                            '0' => 'Pelanggan Umum / Non-Member'
                        ]
                    ])
                </div>

                <!-- Items di Keranjang -->
                <div class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                    <div class="p-3 bg-gray-50/70 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <p class="font-bold text-gray-900">Oli Mesin 1L</p>
                            <p class="text-[10px] text-gray-400">Rp 20.000 x 1</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-900">Rp 20.000</span>
                            <button type="button" class="text-rose-500 hover:text-rose-700" title="Hapus">&times;</button>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50/70 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <p class="font-bold text-gray-900">Servis Ringan / Tune Up</p>
                            <p class="text-[10px] text-gray-400">Rp 45.000 x 1</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-900">Rp 45.000</span>
                            <button type="button" class="text-rose-500 hover:text-rose-700" title="Hapus">&times;</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total & Checkout -->
            <div class="pt-5 mt-5 border-t border-gray-100">
                <div class="space-y-2 text-xs mb-4">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal Produk</span>
                        <span class="font-medium text-gray-800">Rp 20.000</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Biaya Jasa</span>
                        <span class="font-medium text-gray-800">Rp 45.000</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200">
                        <span>TOTAL BAYAR</span>
                        <span class="text-blue-600 text-lg">Rp 65.000</span>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="grid grid-cols-3 gap-2 mb-4 text-xs font-semibold">
                    <button type="button" class="py-2 rounded-xl border-2 border-blue-600 bg-blue-50/40 text-blue-700 text-center">Tunai</button>
                    <button type="button" class="py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-600 text-center">QRIS</button>
                    <button type="button" class="py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-600 text-center">Transfer</button>
                </div>

                @include('components.button', [
                    'variant' => 'primary',
                    'size'    => 'lg',
                    'class'   => 'w-full py-3',
                    'text'    => 'Bayar & Cetak Struk (F9)',
                    'onclick' => "alert('Transaksi berhasil disimpan! Mencetak struk...')"
                ])
            </div>
        </div>
    </div>
@endsection
