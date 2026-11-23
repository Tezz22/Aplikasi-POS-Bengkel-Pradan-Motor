@extends('layouts.app')

@section('title', 'Riwayat Kasir')

@section('content')
    <x-page-header 
        title="Riwayat Transaksi Kasir" 
        subtitle="Daftar transaksi penjualan dan servis yang dilayani bengkel" 
        icon="riwayat" 
    />

    <x-table 
        id="table-riwayat-kasir" 
        :searchable="true" 
        searchPlaceholder="Cari no transaksi atau pelanggan..." 
        :headers="['NO. TRANSAKSI', 'TANGGAL & WAKTU', 'PELANGGAN', 'TOTAL', 'NOMINAL BAYAR', 'KEMBALIAN', 'AKSI']"
    >
        @forelse($transaksis as $trx)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $trx->no_transaksi }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y, H:i') }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                <td class="py-3.5 px-4 font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-600">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 font-semibold text-emerald-600">Rp {{ number_format($trx->kembalian ?? 0, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="info" onclick="openModal('modal-detail-transaksi-{{ $trx->id_transaksi }}')" title="Lihat Struk" />
                        <x-button-action type="print" onclick="printStruk('{{ $trx->id_transaksi }}')" title="Cetak Struk ke Printer Thermal POS" />
                        <x-button-action type="delete" onclick="openModal('modal-hapus-transaksi-{{ $trx->id_transaksi }}')" title="Hapus Riwayat Transaksi" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada riwayat transaksi</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Detail Transaksi & Template Struk Cetak Thermal POS --}}
    @foreach($transaksis as $trx)
        {{-- Modal Detail Transaksi --}}
        <x-modal 
            id="modal-detail-transaksi-{{ $trx->id_transaksi }}" 
            title="Struk Transaksi #{{ $trx->no_transaksi }}" 
            subtitle="Waktu: {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d F Y, H:i') }}"
            size="lg"
        >
            <div class="mb-4 text-sm text-gray-600 grid grid-cols-2 gap-2">
                <div>
                    <span class="font-semibold text-gray-800">Pelanggan:</span> 
                    {{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}
                    @if($trx->pelanggan && $trx->pelanggan->plat_nomor)
                        <span class="text-xs text-gray-500">({{ $trx->pelanggan->plat_nomor }})</span>
                    @endif
                </div>
                <div><span class="font-semibold text-gray-800">Kasir:</span> {{ $trx->user->nama ?? $trx->user->name ?? 'Kasir' }}</div>
            </div>
            <div class="overflow-x-auto border border-gray-100 rounded-xl mb-4">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold">
                        <tr>
                            <th class="p-3">Item</th>
                            <th class="p-3">Tipe</th>
                            <th class="p-3 text-right">Harga Satuan</th>
                            <th class="p-3 text-center">Qty</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($trx->details as $d)
                            <tr>
                                <td class="p-3 font-semibold text-gray-800">{{ $d->nama_item }}</td>
                                <td class="p-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $d->jenis_item === 'barang' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $d->jenis_item }}
                                    </span>
                                </td>
                                <td class="p-3 text-right text-gray-600">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                <td class="p-3 text-center font-bold">{{ $d->qty }}</td>
                                <td class="p-3 text-right font-bold text-gray-900">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 p-3 rounded-xl text-sm flex justify-between items-center font-bold">
                <span class="text-gray-700">Total Transaksi:</span>
                <span class="text-blue-700 text-base">Rp {{ number_format($trx->total, 0, ',', '.') }}</span>
            </div>

            <x-slot:footer>
                <x-button variant="secondary" size="sm" onclick="closeModal('modal-detail-transaksi-{{ $trx->id_transaksi }}')">
                    Tutup
                </x-button>
                <x-button variant="danger" size="sm" onclick="closeModal('modal-detail-transaksi-{{ $trx->id_transaksi }}'); openModal('modal-hapus-transaksi-{{ $trx->id_transaksi }}')">
                    Hapus
                </x-button>
                <button 
                    type="button" 
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-xs rounded-lg shadow-xs transition-colors cursor-pointer"
                    onclick="printStruk('{{ $trx->id_transaksi }}')"
                >
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak Struk</span>
                </button>
            </x-slot:footer>
        </x-modal>

        {{-- Modal Konfirmasi Hapus Riwayat Transaksi --}}
        <x-modal 
            id="modal-hapus-transaksi-{{ $trx->id_transaksi }}" 
            title="Konfirmasi Hapus Transaksi" 
            subtitle="Tindakan ini tidak dapat dibatalkan"
            :formAction="route('kasir.riwayat.destroy', $trx->id_transaksi)"
            formMethod="DELETE"
            submitText="Ya, Hapus Transaksi"
            submitVariant="danger"
            size="sm"
        >
            <div class="text-center py-2">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <x-icon name="alert" :size="24" />
                </div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Hapus Riwayat Transaksi Ini?</h4>
                <p class="text-xs text-gray-600 leading-relaxed mb-3">
                    Apakah Anda yakin ingin menghapus data transaksi <span class="font-bold text-gray-900">#{{ $trx->no_transaksi }}</span>
                    @if($trx->pelanggan)
                        atas nama <span class="font-bold text-gray-900">{{ $trx->pelanggan->nama }}</span>
                    @endif
                    dengan total <span class="font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</span>?
                </p>
                <div class="bg-amber-50 border border-amber-200/80 rounded-lg p-2.5 text-[11px] text-amber-800 text-left">
                    <div class="flex items-start gap-1.5">
                        <span class="shrink-0 mt-0.5">⚠️</span>
                        <span>Stok suku cadang/barang pada transaksi ini akan <strong>otomatis dikembalikan ke stok sistem</strong>.</span>
                    </div>
                </div>
            </div>
        </x-modal>
    @endforeach
@endsection

@push('scripts')
    <script>
        function printStruk(id) {
            const url = `/kasir/transaksi/${id}/struk?autoprint=1`;
            let iframe = document.getElementById('receipt-print-frame');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'receipt-print-frame';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                document.body.appendChild(iframe);
            }
            iframe.src = url;
        }
    </script>
@endpush

