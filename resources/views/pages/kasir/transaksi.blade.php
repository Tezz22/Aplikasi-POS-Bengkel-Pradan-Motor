@extends('layouts.app')

@section('title', 'Transaksi Penjualan (POS)')

@section('content')
    <x-page-header 
        title="Transaksi Kasir (POS)" 
        subtitle="Input transaksi servis kendaraan dan penjualan suku cadang" 
        icon="transaksi" 
    />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Katalog Barang & Jasa (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between gap-3 mb-4">
                <x-search 
                    placeholder="Cari suku cadang atau jenis jasa..." 
                    target="#pos-catalogue" 
                />

                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl text-xs font-semibold">
                    <button type="button" class="px-3 py-1 bg-white text-blue-600 rounded-lg shadow-2xs">Semua</button>
                    <button type="button" class="px-3 py-1 text-gray-500 hover:text-gray-800">Barang</button>
                    <button type="button" class="px-3 py-1 text-gray-500 hover:text-gray-800">Jasa Servis</button>
                </div>
            </div>

            <!-- List Grid Items -->
            <div id="pos-catalogue" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[550px] overflow-y-auto pr-1">
                @forelse($barangs as $barang)
                    <div class="pos-item p-3.5 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/20 transition-all flex flex-col justify-between" data-type="barang" data-name="{{ strtolower($barang->nama_barang) }}">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500 bg-blue-50 px-2 py-0.5 rounded-md">{{ $barang->kategori->nama_kategori ?? 'Suku Cadang' }}</span>
                                <span class="text-[10px] {{ $barang->stok <= 5 ? 'text-rose-500 font-bold' : 'text-gray-400' }}">Stok: {{ $barang->stok }} {{ $barang->satuan }}</span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-xs mt-2">{{ $barang->nama_barang }}</h4>
                            <p class="text-[11px] text-gray-400 font-medium">#{{ $barang->id_barang }} &bull; {{ $barang->satuan }}</p>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                            <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</span>
                            <form method="POST" action="{{ route('kasir.transaksi.cart.add') }}">
                                @csrf
                                <input type="hidden" name="type" value="barang">
                                <input type="hidden" name="id" value="{{ $barang->id_barang }}">
                                @if($barang->stok > 0)
                                    <x-button
                                        type="submit"
                                        size="sm"
                                        variant="primary"
                                        icon="plus"
                                        text="Tambah"
                                    />
                                @else
                                    <button type="button" disabled class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                        Stok Habis
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-8 text-center text-xs text-gray-400">Tidak ada barang tersedia</div>
                @endforelse

                @forelse($jasas as $jasa)
                    <div class="pos-item p-3.5 rounded-xl border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/20 transition-all flex flex-col justify-between" data-type="jasa" data-name="{{ strtolower($jasa->nama_jasa) }}">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-md">Jasa Servis</span>
                                <span class="text-[10px] text-gray-400">Jasa Bengkel</span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-xs mt-2">{{ $jasa->nama_jasa }}</h4>
                            <p class="text-[11px] text-gray-400 font-medium">#{{ $jasa->id_jasa }}</p>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                            <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($jasa->tarif, 0, ',', '.') }}</span>
                            <form method="POST" action="{{ route('kasir.transaksi.cart.add') }}">
                                @csrf
                                <input type="hidden" name="type" value="jasa">
                                <input type="hidden" name="id" value="{{ $jasa->id_jasa }}">
                                <x-button
                                    type="submit"
                                    size="sm"
                                    variant="success"
                                    icon="plus"
                                    text="Tambah"
                                />
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-8 text-center text-xs text-gray-400">Tidak ada produk atau jasa tersedia</div>
                @endforelse
            </div>
        </div>

        <!-- Keranjang Transaksi & Checkout (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="font-bold text-sm text-gray-900">Keranjang Kasir</h3>
                    <span class="text-xs text-blue-600 font-semibold">{{ $itemCount }} Item</span>
                </div>

                @if(session('status'))
                    <div role="status" class="mb-3 rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-700">{{ session('status') }}</div>
                @endif
                @if(session('warning'))
                    <div role="status" class="mb-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">{{ session('warning') }}</div>
                @endif
                @if(session('error'))
                    <div role="alert" class="mb-3 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-700">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div role="alert" class="mb-3 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-700">{{ $errors->first() }}</div>
                @endif

                <!-- Pilih Pelanggan -->
                @php
                    $pelangganOptions = ['' => 'Pelanggan Umum (Tanpa Member)'];
                    foreach($pelanggans as $p) {
                        $pelangganOptions[$p->id_pelanggan] = "{$p->nama} " . ($p->plat_nomor ? "({$p->plat_nomor})" : '');
                    }
                @endphp
                <div class="mb-4">
                    <x-select 
                        label="Pilih Pelanggan / Plat Nomor" 
                        name="id_pelanggan" 
                        :options="$pelangganOptions" 
                    />
                </div>

                <!-- Items di Keranjang -->
                <div class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                    @forelse($cartItems as $item)
                        <div class="p-3 bg-gray-50/70 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <p class="font-bold text-gray-900">{{ $item['name'] }}</p>
                                <p class="text-[10px] text-gray-400">Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-gray-900">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                <form method="POST" action="{{ route('kasir.transaksi.cart.remove', ['type' => $item['type'], 'id' => $item['id']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700" title="Hapus {{ $item['name'] }}" aria-label="Hapus {{ $item['name'] }}">&times;</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-gray-400">Keranjang masih kosong</div>
                    @endforelse
                </div>
            </div>

            <!-- Total & Checkout -->
            <div class="pt-5 mt-5 border-t border-gray-100">
                <div class="space-y-2 text-xs mb-4">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal Barang</span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($subtotalBarang, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Tarif Jasa</span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($subtotalJasa, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200">
                        <span>TOTAL</span>
                        <span class="text-blue-600 text-lg">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button
                    type="button"
                    disabled
                    title="Fungsi pembayaran dan cetak struk belum tersedia"
                    class="w-full rounded-xl bg-gray-200 px-5 py-3 text-sm font-semibold text-gray-500 cursor-not-allowed"
                >
                    Proses Pembayaran &amp; Cetak Struk
                </button>
            </div>
        </div>
    </div>
@endsection
