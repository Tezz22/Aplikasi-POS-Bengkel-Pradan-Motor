<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $penjualan->no_transaksi }} - {{ config('toko.nama', 'Pradan Motor') }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            color: #000;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f1f5f9;
            color: #000;
            line-height: 1.25;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        /* Toolbar Pratinjau Layar (User-Friendly Modern Floating Bar) */
        .no-print-toolbar {
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .toolbar-container {
            max-width: 860px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toolbar-badge {
            background: #1e293b;
            color: #94a3b8;
            border: 1px solid #334155;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 9999px;
            letter-spacing: 0.3px;
        }

        .toolbar-title {
            color: #f8fafc;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            outline: none;
            font-family: inherit;
        }

        .btn-back {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
        }

        .btn-back:hover {
            background: #334155;
            color: #ffffff;
            border-color: #475569;
        }

        .btn-print {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        }

        .btn-print:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.45);
            transform: translateY(-1px);
        }

        .btn-print:active {
            transform: translateY(0);
        }

        .btn-icon {
            width: 16px;
            height: 16px;
            stroke-width: 2.2;
        }

        .btn-kbd {
            font-size: 10.5px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            padding: 2px 6px;
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
        }

        .btn-back .btn-kbd {
            background: rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.15);
            color: #94a3b8;
        }

        /* Container Struk Thermal 80mm */
        .struk {
            width: 72mm;
            margin: 24px auto;
            padding: 3mm 0 10mm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.25;
            color: #000;
            background: #fff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .tengah { text-align: center; }
        .kiri   { text-align: left; }
        .kanan  { text-align: right; white-space: nowrap; }
        .tebal  { font-weight: 700; }
        .garis  { border-top: 1px dashed #000; margin: 1.5mm 0; }
        .baris  { display: flex; justify-content: space-between; align-items: flex-start; gap: 1ch; }

        .store-title {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .store-desc {
            font-size: 11px;
            line-height: 1.2;
            word-break: break-word;
        }

        .item-block {
            margin: 1.5mm 0;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .item-nama {
            word-break: break-word;
            line-height: 1.2;
        }

        .item-sub {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .item-qty-harga {
            padding-left: 2ch;
            white-space: nowrap;
        }

        .cutter-space {
            height: 10mm;
            display: block;
        }

        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                width: 80mm !important;
            }

            .no-print {
                display: none !important;
            }

            .struk {
                margin: 0 !important;
                padding: 0 4mm 10mm !important;
                width: 80mm !important;
                box-sizing: border-box !important;
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .baris, .item-block {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    {{-- Toolbar Pratinjau Layar (User-Friendly & Responsif) --}}
    <div class="no-print-toolbar no-print">
        <div class="toolbar-container">
            <div class="toolbar-info">
                <span class="toolbar-badge">Thermal 80mm</span>
                <span class="toolbar-title">Struk #{{ $penjualan->no_transaksi }}</span>
            </div>

            <div class="toolbar-actions">
                <button type="button" class="btn-action btn-back" onclick="handleBack()" title="Kembali ke halaman sebelumnya (Esc)">
                    <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                    <span class="btn-kbd">Esc</span>
                </button>

                <button type="button" class="btn-action btn-print" onclick="window.print()" title="Cetak struk ke printer thermal (Enter)">
                    <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Struk</span>
                    <span class="btn-kbd">Enter</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Wadah Struk Thermal 80mm --}}
    <div class="struk" id="receipt-area">
        {{-- 1. Header Toko --}}
        <div class="tengah">
            <div class="store-title">{{ config('toko.nama', 'Pradan Motor') }}</div>
            @if(config('toko.tagline'))
                <div class="store-desc">{{ config('toko.tagline') }}</div>
            @endif
            @if(config('toko.alamat'))
                <div class="store-desc">{{ config('toko.alamat') }}</div>
            @endif
            @if(config('toko.telepon'))
                <div class="store-desc">Telp. {{ config('toko.telepon') }}</div>
            @endif
        </div>

        <div class="garis"></div>

        {{-- 2. Info Transaksi (2 Kolom: Kiri - Kanan) --}}
        <div class="baris">
            <span>{{ \Carbon\Carbon::parse($penjualan->tanggal)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span>
            <span class="kanan">#{{ $penjualan->no_transaksi }}</span>
        </div>
        <div class="baris">
            <span>Kasir : {{ \App\Support\StrukFormatter::potong($penjualan->user->nama ?? $penjualan->user->name ?? 'Kasir', 16) }}</span>
            <span class="kanan">Plg : {{ \App\Support\StrukFormatter::potong($penjualan->pelanggan->nama ?? 'Umum', 16) }}</span>
        </div>
        @if($penjualan->pelanggan && $penjualan->pelanggan->plat_nomor)
            <div class="baris">
                <span>Plat : {{ $penjualan->pelanggan->plat_nomor }}</span>
            </div>
        @endif

        <div class="garis"></div>

        {{-- 3. Rincian Item (Format 2 Baris per Item Gaya Minimarket) --}}
        @foreach($penjualan->details as $d)
            @php
                $namaItem = \App\Support\StrukFormatter::formatNamaItem($d->nama_item, $d->jenis_item);
            @endphp
            <div class="item-block">
                <div class="item-nama">{{ $namaItem }}</div>
                <div class="item-sub">
                    <span class="item-qty-harga">  {{ (int) $d->qty }} x {{ \App\Support\StrukFormatter::rupiah($d->harga_satuan) }}</span>
                    <span class="kanan">{{ \App\Support\StrukFormatter::rupiah($d->subtotal) }}</span>
                </div>
            </div>
        @endforeach

        <div class="garis"></div>

        {{-- 4. Ringkasan (Label Kiri, Angka Kanan) --}}
        <div class="baris">
            <span>Total Item</span>
            <span class="kanan">{{ (int) $penjualan->details->sum('qty') }}</span>
        </div>
        <div class="baris tebal">
            <span>TOTAL</span>
            <span class="kanan">{{ \App\Support\StrukFormatter::rupiah($penjualan->total) }}</span>
        </div>
        <div class="baris">
            <span>TUNAI</span>
            <span class="kanan">{{ \App\Support\StrukFormatter::rupiah($penjualan->nominal_bayar) }}</span>
        </div>
        <div class="baris">
            <span>KEMBALI</span>
            <span class="kanan">{{ \App\Support\StrukFormatter::rupiah($penjualan->kembalian ?? 0) }}</span>
        </div>

        <div class="garis"></div>

        {{-- 5. Footer --}}
        <div class="tengah">
            <div>{{ config('toko.footer', 'TERIMA KASIH ATAS KUNJUNGAN ANDA') }}</div>
            @if(config('toko.catatan'))
                <div class="store-desc" style="margin-top: 1.5mm;">{{ config('toko.catatan') }}</div>
            @endif
        </div>

        {{-- Ruang kosong aman untuk auto-cutter printer thermal (10mm) --}}
        <div class="cutter-space"></div>
    </div>

    <script>
        function handleBack() {
            if (window.opener || window.history.length <= 1) {
                window.close();
            } else {
                window.history.back();
            }
        }

        // Pintasan keyboard agar ramah kasir (Enter untuk cetak, Esc untuk kembali)
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                handleBack();
            } else if (event.key === 'Enter' && !event.repeat) {
                window.print();
            }
        });

        @if($autoprint)
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 350);
            });
        @endif
    </script>
</body>
</html>
