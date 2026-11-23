@extends('layouts.app')

@section('title', 'Transaksi Penjualan (POS)')

@section('content')
    <x-page-header 
        title="Transaksi Kasir (POS)" 
        subtitle="Input transaksi servis kendaraan dan penjualan suku cadang" 
        icon="transaksi" 
    />

    @php
        $oldItemsJson = '[]';
        if (old('items')) {
            $oldItemsList = [];
            $barangDict = $barangs->keyBy('id_barang');
            $jasaDict = $jasas->keyBy('id_jasa');

            foreach (old('items') as $oi) {
                $jenis = $oi['jenis'] ?? '';
                $id = (int) ($oi['id'] ?? 0);
                $qty = (int) ($oi['qty'] ?? 1);

                if ($jenis === 'barang' && isset($barangDict[$id])) {
                    $b = $barangDict[$id];
                    $oldItemsList[] = [
                        'jenis' => 'barang',
                        'id' => $id,
                        'nama' => $b->nama_barang,
                        'harga' => (int) $b->harga_jual,
                        'maxStok' => (int) $b->stok,
                        'satuan' => $b->satuan,
                        'qty' => $qty,
                    ];
                } elseif ($jenis === 'jasa' && isset($jasaDict[$id])) {
                    $j = $jasaDict[$id];
                    $oldItemsList[] = [
                        'jenis' => 'jasa',
                        'id' => $id,
                        'nama' => $j->nama_jasa,
                        'harga' => (int) $j->tarif,
                        'maxStok' => 999999,
                        'satuan' => '',
                        'qty' => $qty,
                    ];
                }
            }
            $oldItemsJson = json_encode($oldItemsList);
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Katalog Barang & Jasa (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="relative w-full max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        @include('components.icon', ['name' => 'search', 'size' => 16])
                    </div>
                    <input 
                        type="text" 
                        id="pos-search-input" 
                        placeholder="Cari suku cadang atau jenis jasa..." 
                        oninput="filterCatalogue()"
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    />
                </div>

                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl text-xs font-semibold" id="category-filter">
                    <button type="button" onclick="setCategoryFilter('all', this)" class="filter-btn px-3 py-1 bg-white text-blue-600 rounded-lg shadow-2xs cursor-pointer">Semua</button>
                    <button type="button" onclick="setCategoryFilter('barang', this)" class="filter-btn px-3 py-1 text-gray-500 hover:text-gray-800 cursor-pointer">Barang</button>
                    <button type="button" onclick="setCategoryFilter('jasa', this)" class="filter-btn px-3 py-1 text-gray-500 hover:text-gray-800 cursor-pointer">Jasa Servis</button>
                </div>
            </div>

            <!-- Indikator Koneksi Stok (Halus) -->
            <div id="stale-stock-indicator" class="hidden mb-3 p-2 bg-amber-50 border border-amber-100 rounded-xl text-[11px] text-amber-700 flex items-center gap-1.5">
                <x-icon name="alert" :size="14" />
                <span>Penyelarasan stok latar belakang terhambat. Data stok mungkin belum terbaru.</span>
            </div>

            <!-- List Grid Items -->
            <div id="pos-catalogue" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[580px] overflow-y-auto pr-1">
                @forelse($barangs as $barang)
                    <div 
                        class="pos-item p-3.5 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/20 transition-all flex flex-col justify-between {{ $barang->stok <= 0 ? 'opacity-60 bg-gray-50/70' : '' }}" 
                        data-type="barang" 
                        data-id="{{ $barang->id_barang }}"
                        data-name="{{ strtolower($barang->nama_barang) }}"
                        data-category="{{ strtolower($barang->kategori->nama_kategori ?? '') }}"
                        id="pos-item-barang-{{ $barang->id_barang }}"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md truncate max-w-[140px]">
                                    {{ $barang->kategori->nama_kategori ?? 'Suku Cadang' }}
                                </span>
                                
                                <div class="stock-badge-wrapper" id="stock-badge-{{ $barang->id_barang }}">
                                    @if($barang->stok <= 0)
                                        <x-badge type="danger" icon="alert">Habis (0)</x-badge>
                                    @elseif($barang->stok <= 10)
                                        <x-badge type="warning" icon="alert">Menipis ({{ $barang->stok }})</x-badge>
                                    @else
                                        <x-badge type="success" icon="check">Tersedia ({{ $barang->stok }})</x-badge>
                                    @endif
                                </div>
                            </div>

                            <h4 class="font-bold text-gray-900 text-xs mt-2.5 leading-snug">{{ $barang->nama_barang }}</h4>
                            <p class="text-[11px] text-gray-400 font-medium mt-0.5">Satuan: {{ $barang->satuan }}</p>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-gray-100">
                            <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</span>
                            <button 
                                type="button" 
                                id="btn-add-barang-{{ $barang->id_barang }}"
                                onclick="addToCart('barang', {{ $barang->id_barang }}, '{{ addslashes($barang->nama_barang) }}', {{ $barang->harga_jual }}, '{{ $barang->satuan }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $barang->stok <= 0 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer shadow-2xs' }}"
                                {{ $barang->stok <= 0 ? 'disabled' : '' }}
                            >
                                @if($barang->stok > 0)
                                    <x-icon name="plus" :size="14" />
                                    <span>Tambah</span>
                                @else
                                    <span>Habis</span>
                                @endif
                            </button>
                        </div>
                    </div>
                @empty
                @endforelse

                @forelse($jasas as $jasa)
                    <div 
                        class="pos-item p-3.5 rounded-xl border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/20 transition-all flex flex-col justify-between" 
                        data-type="jasa" 
                        data-id="{{ $jasa->id_jasa }}"
                        data-name="{{ strtolower($jasa->nama_jasa) }}"
                        data-category="jasa servis bengkel"
                        id="pos-item-jasa-{{ $jasa->id_jasa }}"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                    Jasa Servis
                                </span>
                                <x-badge type="neutral">Tarif Tetap</x-badge>
                            </div>
                            <h4 class="font-bold text-gray-900 text-xs mt-2.5 leading-snug">{{ $jasa->nama_jasa }}</h4>
                            <p class="text-[11px] text-gray-400 font-medium mt-0.5">Ongkos Mekanik</p>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-gray-100">
                            <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($jasa->tarif, 0, ',', '.') }}</span>
                            <button 
                                type="button" 
                                onclick="addToCart('jasa', {{ $jasa->id_jasa }}, '{{ addslashes($jasa->nama_jasa) }}', {{ $jasa->tarif }}, '')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-2xs cursor-pointer"
                            >
                                <x-icon name="plus" :size="14" />
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-8 text-center text-xs text-gray-400">Tidak ada produk atau jasa tersedia</div>
                @endforelse
            </div>
        </div>

        <!-- Keranjang Transaksi & Checkout (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between">
            <form id="pos-checkout-form" action="{{ route('kasir.transaksi.store') }}" method="POST">
                @csrf
                <div id="hidden-cart-inputs"></div>

                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-sm text-gray-900">Keranjang Kasir</h3>
                            <button 
                                type="button" 
                                onclick="syncStockFromServer(true)" 
                                title="Segarkan Stok Database"
                                class="p-1 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                            >
                                <x-icon name="refresh" :size="14" />
                            </button>
                        </div>
                        <span id="cart-item-count" class="text-xs text-blue-600 font-semibold">0 Item</span>
                    </div>

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
                            :value="old('id_pelanggan')"
                        />
                    </div>

                    <!-- Notifikasi Pesan Stok Berubah / Error Validasi -->
                    <div id="cart-alert-container">
                        @error('items')
                            <div class="mb-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-start gap-2">
                                <x-icon name="alert" :size="16" />
                                <div>
                                    <span class="font-bold">Transaksi Ditolak:</span>
                                    <p class="mt-0.5">{{ $message }}</p>
                                </div>
                            </div>
                        @enderror
                    </div>

                    <!-- Items di Keranjang -->
                    <div id="cart-items-container" class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                        <div class="p-8 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-2 text-gray-400">
                                <x-icon name="transaksi" :size="20" />
                            </div>
                            <p class="font-medium text-gray-600">Keranjang masih kosong</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Klik tombol <strong>+ Tambah</strong> pada katalog suku cadang atau jasa.</p>
                        </div>
                    </div>
                </div>

                <!-- Total & Checkout -->
                <div class="pt-5 mt-5 border-t border-gray-100">
                    <div class="space-y-2 text-xs mb-4">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal Suku Cadang</span>
                            <span id="subtotal-barang-text" class="font-medium text-gray-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Tarif Jasa Servis</span>
                            <span id="subtotal-jasa-text" class="font-medium text-gray-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200">
                            <span>TOTAL TAGIHAN</span>
                            <span id="total-bayar-text" class="text-blue-600 text-lg">Rp 0</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal Bayar (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                id="nominal_bayar" 
                                name="nominal_bayar" 
                                value="{{ old('nominal_bayar') }}"
                                placeholder="0" 
                                min="0" 
                                required 
                                oninput="calculateChange()"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs text-gray-900 font-bold"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kembalian</label>
                            <input 
                                type="text" 
                                id="kembalian" 
                                readonly 
                                value="Rp 0" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 font-bold"
                            />
                        </div>
                    </div>

                    <!-- Tombol Cepat Nominal Uang Bayar -->
                    <div class="flex items-center gap-1.5 flex-wrap mb-4">
                        <button type="button" onclick="setNominalPas()" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-[11px] font-bold border border-blue-200 transition-colors cursor-pointer">
                            Uang Pas
                        </button>
                        <button type="button" onclick="addNominal(10000)" class="px-2 py-1 bg-gray-50 text-gray-700 hover:bg-gray-100 rounded-lg text-[11px] font-semibold border border-gray-200 transition-colors cursor-pointer">
                            +10rb
                        </button>
                        <button type="button" onclick="addNominal(20000)" class="px-2 py-1 bg-gray-50 text-gray-700 hover:bg-gray-100 rounded-lg text-[11px] font-semibold border border-gray-200 transition-colors cursor-pointer">
                            +20rb
                        </button>
                        <button type="button" onclick="addNominal(50000)" class="px-2 py-1 bg-gray-50 text-gray-700 hover:bg-gray-100 rounded-lg text-[11px] font-semibold border border-gray-200 transition-colors cursor-pointer">
                            +50rb
                        </button>
                        <button type="button" onclick="addNominal(100000)" class="px-2 py-1 bg-gray-50 text-gray-700 hover:bg-gray-100 rounded-lg text-[11px] font-semibold border border-gray-200 transition-colors cursor-pointer">
                            +100rb
                        </button>
                        <button type="button" onclick="resetNominal()" class="px-2 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-[11px] font-semibold border border-rose-200 transition-colors cursor-pointer" title="Kosongkan">
                            Reset
                        </button>
                    </div>

                    @error('nominal_bayar')
                        <p class="text-rose-600 font-semibold text-[11px] mb-3">{{ $message }}</p>
                    @enderror

                    <button 
                        type="submit" 
                        id="btn-submit-pos"
                        disabled
                        class="w-full py-3 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <x-icon name="check" :size="18" />
                        <span>Proses Pembayaran & Catat Penjualan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Pop-up Sukses Pembayaran & Cetak Struk --}}
    @if(session('transaksi_sukses'))
        <x-modal 
            id="modal-transaksi-sukses" 
            title="Pembayaran Berhasil!" 
            subtitle="Faktur #{{ session('transaksi_sukses')['no_transaksi'] }}"
            size="sm"
            :footer="false"
        >
            <div class="text-center py-2">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                    <x-icon name="check" :size="28" />
                </div>
                
                <h4 class="text-base font-bold text-gray-900 mb-0.5">Transaksi Sukses</h4>
                <p class="text-xs text-gray-500 mb-4">No. Faktur: <strong>{{ session('transaksi_sukses')['no_transaksi'] }}</strong></p>

                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100 space-y-2 text-xs mb-4 text-left">
                    <div class="flex justify-between text-gray-600">
                        <span>Total Tagihan</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format(session('transaksi_sukses')['total'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Uang Diterima</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format(session('transaksi_sukses')['nominal_bayar'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-700 pt-2 border-t border-gray-200/80 font-bold text-sm">
                        <span>KEMBALIAN</span>
                        <span>Rp {{ number_format(session('transaksi_sukses')['kembalian'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Toggle Auto Print -->
                <label class="flex items-center justify-center gap-2 cursor-pointer text-xs text-gray-600 mb-4 select-none">
                    <input 
                        type="checkbox" 
                        id="toggle-auto-print" 
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                        onchange="toggleAutoPrint(this.checked)"
                    >
                    <span>Cetak otomatis setelah transaksi</span>
                </label>

                <!-- Tombol Aksi -->
                <div class="flex flex-col gap-2">
                    <button 
                        type="button" 
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm rounded-xl shadow-xs transition-colors duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        onclick="printStrukDirect('{{ session('transaksi_sukses')['struk_url'] }}')"
                    >
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Cetak Struk</span>
                    </button>

                    <button 
                        type="button" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 font-medium text-xs rounded-xl border border-gray-200 shadow-2xs transition-colors duration-150 cursor-pointer focus:outline-none"
                        onclick="closeSuccessModal()"
                    >
                        <span>Transaksi Baru</span>
                    </button>
                </div>
            </div>
        </x-modal>
    @endif

    <script>
        // Database Stok Real-time State
        let dbStock = {
            @foreach($barangs as $b)
                {{ $b->id_barang }}: {{ (int) $b->stok }},
            @endforeach
        };

        // Keranjang belanja (Rehidrasi dari old('items') bila ada)
        let cart = {!! $oldItemsJson !!};
        let activeCategoryFilter = 'all';

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        }

        function parseCurrency(str) {
            let num = str.replace(/[^0-9]/g, '');
            return parseFloat(num) || 0;
        }

        // Filter Kategori & Pencarian Instan (Tanpa mereset keranjang)
        function setCategoryFilter(type, btn) {
            activeCategoryFilter = type;
            document.querySelectorAll('#category-filter .filter-btn').forEach(b => {
                b.className = 'filter-btn px-3 py-1 text-gray-500 hover:text-gray-800 cursor-pointer';
            });
            btn.className = 'filter-btn px-3 py-1 bg-white text-blue-600 rounded-lg shadow-2xs cursor-pointer';

            filterCatalogue();
        }

        function filterCatalogue() {
            const searchInput = document.getElementById('pos-search-input');
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

            document.querySelectorAll('#pos-catalogue .pos-item').forEach(item => {
                const type = item.getAttribute('data-type');
                const name = item.getAttribute('data-name') || '';
                const category = item.getAttribute('data-category') || '';

                const matchType = (activeCategoryFilter === 'all' || type === activeCategoryFilter);
                const matchQuery = !query || name.includes(query) || category.includes(query);

                item.style.display = (matchType && matchQuery) ? '' : 'none';
            });
        }

        // Hitung stok yang masih tersedia (stok DB - kuantitas di keranjang)
        function getAvailableStock(idBarang) {
            const rawDb = dbStock[idBarang] !== undefined ? dbStock[idBarang] : 0;
            const inCart = cart.find(i => i.jenis === 'barang' && i.id === idBarang);
            const cartQty = inCart ? inCart.qty : 0;
            return Math.max(0, rawDb - cartQty);
        }

        // Perbarui badge stok dan status disabled tombol di katalog
        function refreshCatalogStockUI() {
            for (const [id, totalDb] of Object.entries(dbStock)) {
                const numId = parseInt(id);
                const available = getAvailableStock(numId);
                const badgeContainer = document.getElementById(`stock-badge-${numId}`);
                const card = document.getElementById(`pos-item-barang-${numId}`);
                const addBtn = document.getElementById(`btn-add-barang-${numId}`);

                if (badgeContainer) {
                    if (available <= 0) {
                        badgeContainer.innerHTML = `
                            <span class="inline-flex items-center gap-1 font-semibold rounded-full bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 px-2.5 py-0.5 text-[11px]">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Habis (0)</span>
                            </span>
                        `;
                    } else if (available <= 10) {
                        badgeContainer.innerHTML = `
                            <span class="inline-flex items-center gap-1 font-semibold rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-600/20 px-2.5 py-0.5 text-[11px]">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Menipis (${available})</span>
                            </span>
                        `;
                    } else {
                        badgeContainer.innerHTML = `
                            <span class="inline-flex items-center gap-1 font-semibold rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 px-2.5 py-0.5 text-[11px]">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Tersedia (${available})</span>
                            </span>
                        `;
                    }
                }

                if (card && addBtn) {
                    if (available <= 0) {
                        card.classList.add('opacity-60', 'bg-gray-50/70');
                        addBtn.disabled = true;
                        addBtn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-gray-500 rounded-xl text-xs font-semibold cursor-not-allowed';
                        addBtn.innerHTML = `<span>Habis</span>`;
                    } else {
                        card.classList.remove('opacity-60', 'bg-gray-50/70');
                        addBtn.disabled = false;
                        addBtn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition-colors shadow-2xs cursor-pointer';
                        addBtn.innerHTML = `
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah</span>
                        `;
                    }
                }
            }
        }

        // Tambah Item ke Keranjang
        function addToCart(jenis, id, nama, harga, satuan) {
            let existing = cart.find(item => item.jenis === jenis && item.id === id);

            if (jenis === 'barang') {
                const totalStock = dbStock[id] || 0;
                const currentQty = existing ? existing.qty : 0;

                if (totalStock <= 0) {
                    showAlert(`Stok untuk '${nama}' saat ini kosong di gudang!`);
                    return;
                }

                if (currentQty + 1 > totalStock) {
                    showAlert(`Stok tidak mencukupi! Maksimal tersedia untuk '${nama}' adalah ${totalStock} ${satuan}.`);
                    return;
                }
            }

            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({
                    jenis: jenis,
                    id: id,
                    nama: nama,
                    harga: harga,
                    satuan: satuan,
                    qty: 1
                });
            }

            renderCart();
        }

        // Ubah Kuantitas Item di Keranjang
        function updateQty(index, change) {
            let item = cart[index];
            if (!item) return;

            let newQty = item.qty + change;
            if (newQty <= 0) {
                removeFromCart(index);
                return;
            }

            if (item.jenis === 'barang') {
                const totalStock = dbStock[item.id] || 0;
                if (newQty > totalStock) {
                    showAlert(`Stok tidak mencukupi! Maksimal stok tersedia untuk '${item.nama}' adalah ${totalStock} ${item.satuan}.`);
                    return;
                }
            }

            item.qty = newQty;
            renderCart();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        // Render Antarmuka Keranjang Belanja
        function renderCart() {
            let container = document.getElementById('cart-items-container');
            let hiddenContainer = document.getElementById('hidden-cart-inputs');
            let countSpan = document.getElementById('cart-item-count');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="p-8 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <p class="font-medium text-gray-600">Keranjang masih kosong</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Klik tombol <strong>+ Tambah</strong> pada katalog suku cadang atau jasa.</p>
                    </div>
                `;
                hiddenContainer.innerHTML = '';
                countSpan.innerText = '0 Item';
                updateTotals(0, 0);
                document.getElementById('btn-submit-pos').disabled = true;
                refreshCatalogStockUI();
                return;
            }

            let html = '';
            let hiddenHtml = '';
            let subtotalBarang = 0;
            let subtotalJasa = 0;
            let totalQty = 0;

            cart.forEach((item, index) => {
                let subtotal = item.harga * item.qty;
                totalQty += item.qty;

                const isBarang = item.jenis === 'barang';
                const totalStock = isBarang ? (dbStock[item.id] || 0) : 999999;
                const isMax = isBarang && item.qty >= totalStock;

                if (isBarang) {
                    subtotalBarang += subtotal;
                } else {
                    subtotalJasa += subtotal;
                }

                html += `
                    <div class="p-3 bg-gray-50/80 border border-gray-100 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex-1 mr-3">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded ${isBarang ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'}">
                                    ${isBarang ? 'Barang' : 'Jasa'}
                                </span>
                                <p class="font-bold text-gray-900">${item.nama}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <p class="text-[11px] text-gray-500 font-medium">${formatRupiah(item.harga)} x ${item.qty} ${item.satuan}</p>
                                ${isMax ? `<span class="text-[10px] text-amber-600 font-semibold">(Maks. ${totalStock} tersedia)</span>` : ''}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg p-0.5 shadow-2xs">
                                <button type="button" onclick="updateQty(${index}, -1)" class="w-6 h-6 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded font-bold cursor-pointer">&minus;</button>
                                <span class="w-6 text-center font-bold text-xs text-gray-900">${item.qty}</span>
                                <button 
                                    type="button" 
                                    onclick="updateQty(${index}, 1)" 
                                    class="w-6 h-6 flex items-center justify-center rounded font-bold ${isMax ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:bg-gray-100 cursor-pointer'}"
                                    ${isMax ? 'disabled' : ''}
                                >
                                    &plus;
                                </button>
                            </div>
                            <span class="font-bold text-gray-900 w-20 text-right">${formatRupiah(subtotal)}</span>
                            <button type="button" onclick="removeFromCart(${index})" class="text-rose-500 hover:text-rose-700 font-bold text-sm px-1 cursor-pointer" title="Hapus">&times;</button>
                        </div>
                    </div>
                `;

                hiddenHtml += `
                    <input type="hidden" name="items[${index}][jenis]" value="${item.jenis}">
                    <input type="hidden" name="items[${index}][id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                `;
            });

            container.innerHTML = html;
            hiddenContainer.innerHTML = hiddenHtml;
            countSpan.innerText = `${totalQty} Item (${cart.length} Jenis)`;

            updateTotals(subtotalBarang, subtotalJasa);
            refreshCatalogStockUI();
        }

        // Kalkulasi Total & Tombol Bayar
        function updateTotals(subBarang, subJasa) {
            let total = subBarang + subJasa;
            document.getElementById('subtotal-barang-text').innerText = formatRupiah(subBarang);
            document.getElementById('subtotal-jasa-text').innerText = formatRupiah(subJasa);
            document.getElementById('total-bayar-text').innerText = formatRupiah(total);

            let nominalInput = document.getElementById('nominal_bayar');
            let btn = document.getElementById('btn-submit-pos');

            if (total > 0) {
                calculateChange();
            } else {
                btn.disabled = true;
                nominalInput.value = '';
                document.getElementById('kembalian').value = 'Rp 0';
            }
        }

        function calculateChange() {
            let subBarang = parseCurrency(document.getElementById('subtotal-barang-text').innerText);
            let subJasa = parseCurrency(document.getElementById('subtotal-jasa-text').innerText);
            let total = subBarang + subJasa;

            let nominal = parseFloat(document.getElementById('nominal_bayar').value) || 0;
            let change = nominal - total;

            let kembalianInput = document.getElementById('kembalian');
            let btn = document.getElementById('btn-submit-pos');

            if (cart.length === 0 || total === 0) {
                kembalianInput.value = 'Rp 0';
                kembalianInput.className = 'w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-500 font-bold';
                btn.disabled = true;
                return;
            }

            if (nominal >= total) {
                kembalianInput.value = 'Kembalian ' + formatRupiah(change);
                kembalianInput.className = 'w-full px-3.5 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-xs text-emerald-700 font-bold';
                btn.disabled = false;
            } else {
                kembalianInput.value = 'Kurang ' + formatRupiah(Math.abs(change));
                kembalianInput.className = 'w-full px-3.5 py-2.5 rounded-xl border border-rose-200 bg-rose-50 text-xs text-rose-600 font-bold';
                btn.disabled = true;
            }
        }

        // Tombol Cepat Nominal Uang Bayar
        function setNominalPas() {
            let subBarang = parseCurrency(document.getElementById('subtotal-barang-text').innerText);
            let subJasa = parseCurrency(document.getElementById('subtotal-jasa-text').innerText);
            let total = subBarang + subJasa;
            if (total <= 0) return;

            document.getElementById('nominal_bayar').value = total;
            calculateChange();
        }

        function addNominal(amount) {
            let input = document.getElementById('nominal_bayar');
            let current = parseFloat(input.value) || 0;
            input.value = current + amount;
            calculateChange();
        }

        function resetNominal() {
            document.getElementById('nominal_bayar').value = '';
            calculateChange();
        }

        // Helper Modal Alert
        function showAlert(msg) {
            if (window.showAlertModal) {
                window.showAlertModal(msg, 'Pemberitahuan Stok', 'warning');
            } else {
                alert(msg);
            }
        }

        // Sinkronisasi Stok dari Database Berkala (Periodic Background Sync)
        async function syncStockFromServer(showSuccessToast = false) {
            try {
                const response = await fetch("{{ route('kasir.transaksi.stok') }}", {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });

                if (!response.ok) throw new Error('Network response not ok');
                const stockData = await response.json();

                // Perbarui peta dbStock
                stockData.forEach(item => {
                    dbStock[item.id_barang] = parseInt(item.stok);
                });

                // Sembunyikan indikator error jaringan bila sebelumnya muncul
                const staleIndicator = document.getElementById('stale-stock-indicator');
                if (staleIndicator) staleIndicator.classList.add('hidden');

                // Verifikasi apakah stok barang di keranjang mengalami penurunan oleh transaksi lain
                let adjustedNotice = [];
                cart.forEach((item, index) => {
                    if (item.jenis === 'barang') {
                        const currentStock = dbStock[item.id] !== undefined ? dbStock[item.id] : 0;
                        if (item.qty > currentStock) {
                            if (currentStock > 0) {
                                adjustedNotice.push(`Stok '${item.nama}' berubah di sistem, kuantitas disesuaikan menjadi ${currentStock}.`);
                                item.qty = currentStock;
                            } else {
                                adjustedNotice.push(`Stok '${item.nama}' telah habis, item dikeluarkan dari keranjang.`);
                                cart.splice(index, 1);
                            }
                        }
                    }
                });

                if (adjustedNotice.length > 0) {
                    showAlert(adjustedNotice.join('\n'));
                } else if (showSuccessToast && window.showAlertModal) {
                    window.showAlertModal('Data stok berhasil disinkronkan dengan database terkini.', 'Sinkronisasi Selesai', 'info');
                }

                renderCart();
            } catch (err) {
                console.warn('Gagal menyinkronkan stok:', err);
                const staleIndicator = document.getElementById('stale-stock-indicator');
                if (staleIndicator) staleIndicator.classList.remove('hidden');
            }
        }

        // Pengaturan Cetak Struk Otomatis & Helper
        function toggleAutoPrint(checked) {
            try {
                localStorage.setItem('auto_print_receipt', checked ? '1' : '0');
            } catch (e) {}
        }

        function closeSuccessModal() {
            closeModal('modal-transaksi-sukses');
            document.getElementById('pos-search-input')?.focus();
        }

        function printStrukDirect(url) {
            let width = '58';
            try {
                width = localStorage.getItem('receipt_width') || '58';
            } catch(e) {}

            const sep = url.includes('?') ? '&' : '?';
            const fullUrl = `${url}${sep}autoprint=1&lebar=${width}`;

            let iframe = document.getElementById('receipt-print-iframe');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'receipt-print-iframe';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                document.body.appendChild(iframe);
            }
            iframe.src = fullUrl;
        }

        // Inisialisasi Saat Halaman Dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Fokus otomatis ke input pencarian
            const searchInput = document.getElementById('pos-search-input');
            if (searchInput) searchInput.focus();

            // Render isi awal keranjang
            renderCart();

            // Cek session modal sukses
            @if(session('transaksi_sukses'))
                openModal('modal-transaksi-sukses');
                const autoToggle = document.getElementById('toggle-auto-print');
                let isAuto = false;
                try {
                    isAuto = localStorage.getItem('auto_print_receipt') === '1';
                } catch(e) {}
                
                if (autoToggle) autoToggle.checked = isAuto;
                if (isAuto) {
                    setTimeout(function() {
                        printStrukDirect(@json(session('transaksi_sukses')['struk_url']));
                    }, 500);
                }
            @endif

            // Polling sinkronisasi stok tiap 20 detik (jika tab aktif)
            setInterval(function() {
                if (document.visibilityState === 'visible') {
                    syncStockFromServer(false);
                }
            }, 20000);

            // Segarkan saat tab kembali difokuskan
            window.addEventListener('focus', function() {
                syncStockFromServer(false);
            });
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    syncStockFromServer(false);
                }
            });
        });
    </script>
@endsection
