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
                    id="pos-catalogue-search"
                />

                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl text-xs font-semibold">
                    <button type="button" data-pos-filter="all" aria-pressed="true" class="px-3 py-1 bg-white text-blue-600 rounded-lg shadow-2xs">Semua</button>
                    <button type="button" data-pos-filter="barang" aria-pressed="false" class="px-3 py-1 text-gray-500 hover:text-gray-800">Barang</button>
                    <button type="button" data-pos-filter="jasa" aria-pressed="false" class="px-3 py-1 text-gray-500 hover:text-gray-800">Jasa Servis</button>
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
                @endforelse
                <div id="pos-catalogue-empty" class="hidden col-span-2 py-8 text-center text-xs text-gray-400">
                    Tidak ada barang atau jasa yang cocok dengan pencarian.
                </div>
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
                        <div class="p-3 bg-gray-50/70 rounded-xl flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-gray-900">{{ $item['name'] }}</p>
                                <p class="text-[10px] text-gray-400">Rp {{ number_format($item['price'], 0, ',', '.') }} per item</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <form method="POST" action="{{ route('kasir.transaksi.cart.quantity', ['type' => $item['type'], 'id' => $item['id']]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="decrease">
                                    <button
                                        type="submit"
                                        aria-label="Kurangi {{ $item['name'] }}"
                                        title="Kurangi jumlah"
                                        @disabled($item['quantity'] <= 1)
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40"
                                    >
                                        @include('components.icon', ['name' => 'minus', 'size' => 14])
                                    </button>
                                </form>
                                <span class="min-w-6 text-center font-semibold text-gray-800">{{ $item['quantity'] }}</span>
                                <form method="POST" action="{{ route('kasir.transaksi.cart.quantity', ['type' => $item['type'], 'id' => $item['id']]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="increase">
                                    <button
                                        type="submit"
                                        aria-label="Tambah {{ $item['name'] }}"
                                        title="Tambah jumlah"
                                        @disabled($item['stock'] !== null && $item['quantity'] >= $item['stock'])
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40"
                                    >
                                        @include('components.icon', ['name' => 'plus', 'size' => 14])
                                    </button>
                                </form>
                                <span class="min-w-24 text-right font-bold text-gray-900">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                <form method="POST" action="{{ route('kasir.transaksi.cart.remove', ['type' => $item['type'], 'id' => $item['id']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700" title="Hapus {{ $item['name'] }}" aria-label="Hapus {{ $item['name'] }}">
                                        @include('components.icon', ['name' => 'trash', 'size' => 15])
                                    </button>
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
                    onclick="openModal('payment-modal')"
                    @disabled($totalBayar <= 0)
                    class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-500"
                >
                    Proses Pembayaran &amp; Cetak Struk
                </button>
            </div>
        </div>
    </div>

    <div id="payment-modal" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-start justify-between">
                <div>
                    <h2 id="payment-modal-title" class="text-lg font-bold text-gray-900">Pembayaran Tunai</h2>
                    <p class="mt-1 text-xs text-gray-500">Masukkan nominal uang tunai yang diterima.</p>
                </div>
                <button type="button" onclick="closeModal('payment-modal')" aria-label="Tutup modal" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                    @include('components.icon', ['name' => 'close', 'size' => 18])
                </button>
            </div>

            <form method="POST" action="{{ route('kasir.transaksi.checkout') }}" id="payment-form" data-total="{{ $totalBayar }}">
                @csrf
                <input type="hidden" name="_payment_modal" value="1">
                <input type="hidden" name="id_pelanggan" id="payment-customer">

                @if($errors->has('cart') || $errors->has('id_pelanggan'))
                    <div role="alert" class="mb-4 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-700">
                        {{ $errors->first('cart') ?: $errors->first('id_pelanggan') }}
                    </div>
                @endif

                <div class="mb-4 rounded-xl bg-blue-50 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-600">Total Pembayaran</span>
                        <span class="text-lg font-bold text-blue-700">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <x-input
                        label="Uang Tunai Dibayarkan (Rp)"
                        name="nominal_bayar_display"
                        id="nominal-bayar-display"
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="Contoh: 100.000"
                        :value="old('nominal_bayar')"
                        required
                    />
                    <input type="hidden" name="nominal_bayar" id="nominal-bayar" value="{{ old('nominal_bayar') }}">
                    @if($errors->has('nominal_bayar'))
                        <p role="alert" class="-mt-3 text-xs text-rose-600">{{ $errors->first('nominal_bayar') }}</p>
                    @endif

                    <x-input
                        label="Kembalian (Rp)"
                        name="kembalian_preview"
                        id="kembalian-preview"
                        type="number"
                        value="0"
                        readonly
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('payment-modal')" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
                    <button
                        type="submit"
                        id="print-receipt-button"
                        disabled
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300"
                    >
                        Proses Pembayaran &amp; Cetak Struk
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const catalogue = document.getElementById('pos-catalogue');
        const searchInput = document.getElementById('pos-catalogue-search');

        if (catalogue && searchInput) {
            const items = Array.from(catalogue.querySelectorAll('.pos-item'));
            const filterButtons = Array.from(document.querySelectorAll('[data-pos-filter]'));
            const emptyMessage = document.getElementById('pos-catalogue-empty');
            let activeFilter = 'all';

            const updateCatalogue = () => {
                const query = searchInput.value.trim().toLocaleLowerCase();
                let visibleItems = 0;

                items.forEach((item) => {
                    const matchesType = activeFilter === 'all' || item.dataset.type === activeFilter;
                    const matchesSearch = item.dataset.name.includes(query);
                    const isVisible = matchesType && matchesSearch;

                    item.classList.toggle('hidden', !isVisible);
                    visibleItems += Number(isVisible);
                });

                emptyMessage?.classList.toggle('hidden', visibleItems > 0);
            };

            filterButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    activeFilter = button.dataset.posFilter;

                    filterButtons.forEach((filterButton) => {
                        const isActive = filterButton === button;

                        filterButton.setAttribute('aria-pressed', String(isActive));
                        filterButton.classList.toggle('bg-white', isActive);
                        filterButton.classList.toggle('text-blue-600', isActive);
                        filterButton.classList.toggle('rounded-lg', isActive);
                        filterButton.classList.toggle('shadow-2xs', isActive);
                        filterButton.classList.toggle('text-gray-500', !isActive);
                        filterButton.classList.toggle('hover:text-gray-800', !isActive);
                    });

                    updateCatalogue();
                });
            });

            searchInput.addEventListener('input', updateCatalogue);
            updateCatalogue();
        }

        const paymentForm = document.getElementById('payment-form');

        if (paymentForm) {
            const total = Number(paymentForm.dataset.total);
            const cashInput = document.getElementById('nominal-bayar');
            const cashDisplay = document.getElementById('nominal-bayar-display');
            const changeInput = document.getElementById('kembalian-preview');
            const printButton = document.getElementById('print-receipt-button');
            const customerSelect = document.getElementById('id_pelanggan');
            const customerInput = document.getElementById('payment-customer');
            const rupiahFormatter = new Intl.NumberFormat('id-ID');

            const formatCashInput = () => {
                const cursorPosition = cashDisplay.selectionStart ?? cashDisplay.value.length;
                const digitsBeforeCursor = cashDisplay.value.slice(0, cursorPosition).replace(/\D/g, '').length;
                const digits = cashDisplay.value.replace(/\D/g, '');

                cashInput.value = digits;
                cashDisplay.value = digits === '' ? '' : rupiahFormatter.format(Number(digits));

                let nextCursorPosition = 0;
                let countedDigits = 0;

                while (nextCursorPosition < cashDisplay.value.length && countedDigits < digitsBeforeCursor) {
                    if (/\d/.test(cashDisplay.value[nextCursorPosition])) {
                        countedDigits++;
                    }

                    nextCursorPosition++;
                }

                cashDisplay.setSelectionRange(nextCursorPosition, nextCursorPosition);
            };

            const updatePayment = () => {
                const cash = cashInput.value === '' ? null : Number(cashInput.value);

                changeInput.value = cash === null ? 0 : cash - total;
                printButton.disabled = cash === null || !Number.isSafeInteger(cash) || cash > 2147483647 || cash < total;
            };

            cashDisplay.addEventListener('input', () => {
                formatCashInput();
                updatePayment();
            });
            formatCashInput();
            updatePayment();
            customerSelect?.addEventListener('change', () => {
                customerInput.value = customerSelect.value;
            });
            paymentForm.addEventListener('submit', () => {
                customerInput.value = customerSelect?.value ?? '';
            });
        }

        @if($errors->any() && old('_payment_modal'))
            openModal('payment-modal');
        @endif
    </script>
@endpush
