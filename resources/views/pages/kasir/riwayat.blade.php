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
                        <x-button-action type="print" onclick="printStruk('{{ $trx->id_transaksi }}', '58mm')" title="Cetak Struk ke Printer Thermal POS" />
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
                <x-button variant="secondary" size="sm" icon="printer" onclick="printStruk('{{ $trx->id_transaksi }}', '80mm')">
                    Cetak (80mm)
                </x-button>
                <x-button variant="primary" size="sm" icon="printer" onclick="printStruk('{{ $trx->id_transaksi }}', '58mm')">
                    Cetak Struk (58mm)
                </x-button>
            </x-slot:footer>
        </x-modal>

        {{-- Hidden Container Khusus Format Alat Cetak Struk (Thermal POS 58mm/80mm) --}}
        <div id="struk-content-{{ $trx->id_transaksi }}" class="hidden">
            <div class="text-center">
                <div class="font-bold" style="font-size: 13px; letter-spacing: 0.5px;">BENGKEL PRADAN MOTOR</div>
                <div style="font-size: 9.5px; margin-top: 1px;">Spesialis Servis Motor & Suku Cadang Resmi</div>
                <div style="font-size: 8px; margin-top: 1px;">Dkh.jinontro, RT.03/RW.02, Gendol, Tegalsari, Jetis, Ponorogo</div>
            </div>
            
            <div class="double-divider"></div>
            
            <table>
                <tr>
                    <td style="width: 58px;">No.Trx</td>
                    <td style="width: 6px;">:</td>
                    <td class="font-bold">#{{ $trx->no_transaksi }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>:</td>
                    <td>{{ $trx->user->nama ?? $trx->user->name ?? 'Kasir' }}</td>
                </tr>
                <tr>
                    <td>Pelanggan</td>
                    <td>:</td>
                    <td>
                        {{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}
                        @if($trx->pelanggan && $trx->pelanggan->plat_nomor)
                            ({{ $trx->pelanggan->plat_nomor }})
                        @endif
                    </td>
                </tr>
            </table>
            
            <div class="divider"></div>
            
            <table>
                <thead>
                    <tr style="border-bottom: 1px dashed #000; font-weight: bold;">
                        <td class="text-left" style="padding-bottom: 2px;">ITEM / LAYANAN</td>
                        <td class="text-right" style="padding-bottom: 2px;">TOTAL</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trx->details as $d)
                        <tr>
                            <td colspan="2" class="font-bold" style="padding-top: 3px;">
                                {{ $d->nama_item }}
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px dashed #000;">
                            <td style="padding-left: 6px; font-size: 9px;">
                                {{ $d->qty }} x {{ number_format($d->harga_satuan, 0, ',', '.') }}
                                <span style="font-size: 8px;">({{ ucfirst($d->jenis_item) }})</span>
                            </td>
                            <td class="text-right font-bold" style="font-size: 10px;">
                                {{ number_format($d->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="divider"></div>
            
            <table>
                <tr class="font-bold">
                    <td style="font-size: 11px;">TOTAL</td>
                    <td style="width: 6px;">:</td>
                    <td class="text-right" style="font-size: 12px;">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>TUNAI</td>
                    <td>:</td>
                    <td class="text-right">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>KEMBALI</td>
                    <td>:</td>
                    <td class="text-right">Rp {{ number_format($trx->kembalian ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            <div class="double-divider"></div>
            
            <div class="text-center" style="margin-top: 4px; font-size: 9px;">
                <p class="font-bold">*** TERIMA KASIH ***</p>
                <p style="margin-top: 1px;">Servis & Suku Cadang Terpercaya</p>
                <p style="font-size: 8px; margin-top: 1px;">Barang yg dibeli tdk dpt ditukar/kembali</p>
            </div>

            {{-- Jarak potong kertas (Tear-off space) agar struk tidak terpotong pisau printer --}}
            <div style="height: 20mm;"></div>
        </div>
    @endforeach
@endsection

@push('scripts')
    {{-- Script Cetak Struk Kompatibel dengan Printer Thermal POS (Alat Cetak Struk 58mm / 80mm) --}}
    <script>
        function printStruk(id, paperSize) {
            paperSize = paperSize || '58mm';
            var content = document.getElementById('struk-content-' + id);
            if (!content) return;

            var is80 = paperSize === '80mm';
            var bodyWidth = is80 ? '72mm' : '48mm';
            var fontSize = is80 ? '11.5px' : '10px';
            var popupWidth = is80 ? 420 : 360;

            var printWindow = window.open('', '_blank', 'width=' + popupWidth + ',height=650,top=80,left=100,scrollbars=yes');

            function writeReceipt(doc, isPopup) {
                doc.open();
                doc.write('<!DOCTYPE html><html><head><meta charset="utf-8">');
                doc.write('<title>Cetak Struk #' + id + '<\/title>');
                doc.write('<style>');
                doc.write('@page { margin: 0; size: auto; }');
                doc.write('@media print { .no-print { display: none !important; } body { margin: 0 !important; padding: 2mm 2mm 15mm 2mm !important; } }');
                doc.write('* { box-sizing: border-box; margin: 0; padding: 0; color: #000 !important; background: transparent !important; }');
                doc.write('body { font-family: "Courier New", Courier, monospace, "Lucida Console"; font-size: ' + fontSize + '; line-height: 1.25; color: #000; background: #fff !important; width: ' + bodyWidth + '; margin: 0 auto; padding: 6px 4px 20mm 4px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }');
                doc.write('.text-center { text-align: center; } .text-right { text-align: right; } .text-left { text-align: left; }');
                doc.write('.font-bold { font-weight: bold; }');
                doc.write('.divider { border-top: 1px dashed #000; margin: 4px 0; }');
                doc.write('.double-divider { border-top: 2px dashed #000; margin: 5px 0; }');
                doc.write('table { width: 100%; border-collapse: collapse; }');
                doc.write('table td { padding: 1.5px 0; vertical-align: top; }');
                if (isPopup) {
                    doc.write('.no-print { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; margin-bottom: 12px; text-align: center; }');
                    doc.write('.no-print button { padding: 5px 10px; margin: 0 3px; font-size: 11px; font-weight: 600; border-radius: 6px; cursor: pointer; border: 1px solid #cbd5e1; background: #fff; }');
                    doc.write('.no-print .btn-print { background: #2563eb; color: #fff !important; border-color: #1d4ed8; }');
                    doc.write('.no-print .btn-active { background: #e2e8f0; font-weight: bold; border-color: #64748b; }');
                }
                doc.write('<\/style>');
                doc.write('<\/head><body>');

                if (isPopup) {
                    doc.write('<div class="no-print">');
                    doc.write('<div style="font-size: 11px; font-weight: bold; color: #334155; margin-bottom: 6px;">Format Printer Struk (Thermal POS):<\/div>');
                    doc.write('<button type="button" class="' + (!is80 ? 'btn-active' : '') + '" onclick="window.setPaper(\'58mm\')">Roll 58mm (Mini)<\/button>');
                    doc.write('<button type="button" class="' + (is80 ? 'btn-active' : '') + '" onclick="window.setPaper(\'80mm\')">Roll 80mm (Standar)<\/button>');
                    doc.write('<button type="button" class="btn-print" onclick="window.print()">🖨️ Cetak Struk<\/button>');
                    doc.write('<\/div>');
                }

                doc.write('<div id="receipt-content">');
                doc.write(content.innerHTML);
                doc.write('<\/div>');

                if (isPopup) {
                    doc.write('<script>');
                    doc.write('window.setPaper = function(sz) {');
                    doc.write('    if (sz === "80mm") {');
                    doc.write('        document.body.style.width = "72mm";');
                    doc.write('        document.body.style.fontSize = "11.5px";');
                    doc.write('    } else {');
                    doc.write('        document.body.style.width = "48mm";');
                    doc.write('        document.body.style.fontSize = "10px";');
                    doc.write('    }');
                    doc.write('};');
                    doc.write('window.onload = function() {');
                    doc.write('    window.focus();');
                    doc.write('    setTimeout(function() { window.print(); }, 400);');
                    doc.write('};');
                    doc.write('<\/script>');
                }

                doc.write('<\/body><\/html>');
                doc.close();
            }

            if (printWindow && !printWindow.closed && typeof printWindow.closed !== 'undefined') {
                writeReceipt(printWindow.document, true);
            } else {
                var iframe = document.getElementById('receipt-print-frame');
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
                writeReceipt(iframe.contentWindow.document, false);
                setTimeout(function() {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                }, 300);
            }
        }
    </script>
@endpush
