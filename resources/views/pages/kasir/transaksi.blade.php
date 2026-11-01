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

                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl text-xs font-semibold" id="category-filter">
                    <button type="button" onclick="filterCatalogue('all', this)" class="filter-btn px-3 py-1 bg-white text-blue-600 rounded-lg shadow-2xs">Semua</button>
                    <button type="button" onclick="filterCatalogue('barang', this)" class="filter-btn px-3 py-1 text-gray-500 hover:text-gray-800">Barang</button>
                    <button type="button" onclick="filterCatalogue('jasa', this)" class="filter-btn px-3 py-1 text-gray-500 hover:text-gray-800">Jasa Servis</button>
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
                            <button 
                                type="button" 
                                onclick="addToCart('barang', {{ $barang->id_barang }}, '{{ addslashes($barang->nama_barang) }}', {{ $barang->harga_jual }}, {{ $barang->stok }}, '{{ $barang->satuan }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition-colors {{ $barang->stok <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $barang->stok <= 0 ? 'disabled' : '' }}
                            >
                                <x-icon name="plus" :size="14" />
                                <span>{{ $barang->stok <= 0 ? 'Habis' : 'Tambah' }}</span>
                            </button>
                        </div>
                    </div>
                @empty
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
                            <button 
                                type="button" 
                                onclick="addToCart('jasa', {{ $jasa->id_jasa }}, '{{ addslashes($jasa->nama_jasa) }}', {{ $jasa->tarif }}, 9999, '')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-700 transition-colors"
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
                        <h3 class="font-bold text-sm text-gray-900">Keranjang Kasir</h3>
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
                        />
                    </div>

                    <!-- Items di Keranjang -->
                    <div id="cart-items-container" class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                        <div class="p-6 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
                            Keranjang masih kosong. Klik tombol <strong>+ Tambah</strong> pada katalog barang atau jasa.
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

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal Bayar (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                id="nominal_bayar" 
                                name="nominal_bayar" 
                                placeholder="0" 
                                min="0" 
                                required 
                                oninput="calculateChange()"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs text-gray-900 font-semibold"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Kembalian (Rp)</label>
                            <input 
                                type="text" 
                                id="kembalian" 
                                readonly 
                                value="Rp 0" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 font-bold"
                            />
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        id="btn-submit-pos"
                        disabled
                        class="w-full py-3 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
                    >
                        <x-icon name="check" :size="18" />
                        <span>Proses Pembayaran & Catat Penjualan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let cart = [];

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        }

        function filterCatalogue(type, btn) {
            document.querySelectorAll('#category-filter .filter-btn').forEach(b => {
                b.className = 'filter-btn px-3 py-1 text-gray-500 hover:text-gray-800';
            });
            btn.className = 'filter-btn px-3 py-1 bg-white text-blue-600 rounded-lg shadow-2xs';

            document.querySelectorAll('#pos-catalogue .pos-item').forEach(item => {
                if (type === 'all' || item.getAttribute('data-type') === type) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function addToCart(jenis, id, nama, harga, maxStok, satuan) {
            let existing = cart.find(item => item.jenis === jenis && item.id === id);

            if (existing) {
                if (jenis === 'barang' && existing.qty + 1 > maxStok) {
                    alert(`Stok tidak mencukupi! Maksimal stok tersedia untuk '${nama}' adalah ${maxStok} ${satuan}.`);
                    return;
                }
                existing.qty += 1;
            } else {
                if (jenis === 'barang' && maxStok <= 0) {
                    alert(`Stok untuk '${nama}' sedang kosong!`);
                    return;
                }
                cart.push({
                    jenis: jenis,
                    id: id,
                    nama: nama,
                    harga: harga,
                    maxStok: maxStok,
                    satuan: satuan,
                    qty: 1
                });
            }

            renderCart();
        }

        function updateQty(index, change) {
            let item = cart[index];
            if (!item) return;

            let newQty = item.qty + change;
            if (newQty <= 0) {
                removeFromCart(index);
                return;
            }

            if (item.jenis === 'barang' && newQty > item.maxStok) {
                alert(`Stok tidak mencukupi! Maksimal stok tersedia adalah ${item.maxStok} ${item.satuan}.`);
                return;
            }

            item.qty = newQty;
            renderCart();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            let container = document.getElementById('cart-items-container');
            let hiddenContainer = document.getElementById('hidden-cart-inputs');
            let countSpan = document.getElementById('cart-item-count');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="p-6 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
                        Keranjang masih kosong. Klik tombol <strong>+ Tambah</strong> pada katalog barang atau jasa.
                    </div>
                `;
                hiddenContainer.innerHTML = '';
                countSpan.innerText = '0 Item';
                updateTotals(0, 0);
                document.getElementById('btn-submit-pos').disabled = true;
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

                if (item.jenis === 'barang') {
                    subtotalBarang += subtotal;
                } else {
                    subtotalJasa += subtotal;
                }

                html += `
                    <div class="p-3 bg-gray-50/80 border border-gray-100 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex-1 mr-3">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded ${item.jenis === 'barang' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'}">
                                    ${item.jenis === 'barang' ? 'Barang' : 'Jasa'}
                                </span>
                                <p class="font-bold text-gray-900">${item.nama}</p>
                            </div>
                            <p class="text-[11px] text-gray-500 font-medium">${formatRupiah(item.harga)} x ${item.qty} ${item.satuan}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg p-0.5">
                                <button type="button" onclick="updateQty(${index}, -1)" class="w-6 h-6 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded font-bold">&minus;</button>
                                <span class="w-6 text-center font-bold text-xs text-gray-900">${item.qty}</span>
                                <button type="button" onclick="updateQty(${index}, 1)" class="w-6 h-6 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded font-bold">&plus;</button>
                            </div>
                            <span class="font-bold text-gray-900 w-20 text-right">${formatRupiah(subtotal)}</span>
                            <button type="button" onclick="removeFromCart(${index})" class="text-rose-500 hover:text-rose-700 font-bold text-sm px-1" title="Hapus">&times;</button>
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
        }

        function updateTotals(subBarang, subJasa) {
            let total = subBarang + subJasa;
            document.getElementById('subtotal-barang-text').innerText = formatRupiah(subBarang);
            document.getElementById('subtotal-jasa-text').innerText = formatRupiah(subJasa);
            document.getElementById('total-bayar-text').innerText = formatRupiah(total);

            let nominalInput = document.getElementById('nominal_bayar');
            let btn = document.getElementById('btn-submit-pos');

            if (total > 0) {
                btn.disabled = false;
                if (!nominalInput.value || parseFloat(nominalInput.value) < total) {
                    nominalInput.value = total;
                }
            } else {
                btn.disabled = true;
                nominalInput.value = 0;
            }

            calculateChange();
        }

        function calculateChange() {
            let totalText = document.getElementById('total-bayar-text').innerText;
            let subBarang = parseCurrency(document.getElementById('subtotal-barang-text').innerText);
            let subJasa = parseCurrency(document.getElementById('subtotal-jasa-text').innerText);
            let total = subBarang + subJasa;

            let nominal = parseFloat(document.getElementById('nominal_bayar').value) || 0;
            let change = nominal - total;

            let kembalianInput = document.getElementById('kembalian');
            let btn = document.getElementById('btn-submit-pos');

            if (change >= 0) {
                kembalianInput.value = formatRupiah(change);
                kembalianInput.classList.remove('text-rose-600');
                kembalianInput.classList.add('text-gray-900');
                if (cart.length > 0) btn.disabled = false;
            } else {
                kembalianInput.value = 'Kurang ' + formatRupiah(Math.abs(change));
                kembalianInput.classList.add('text-rose-600');
                kembalianInput.classList.remove('text-gray-900');
                btn.disabled = true;
            }
        }

        function parseCurrency(str) {
            let num = str.replace(/[^0-9]/g, '');
            return parseFloat(num) || 0;
        }
    </script>
@endsection
