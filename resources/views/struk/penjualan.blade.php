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

        /* Toolbar Pratinjau Layar (Tidak ikut tercetak) */
        .no-print-toolbar {
            background: #1e293b;
            color: #fff;
            padding: 10px 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .no-print-toolbar a,
        .no-print-toolbar button {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: 6px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s ease;
        }

        .no-print-toolbar button:hover,
        .no-print-toolbar a:hover {
            background: #1d4ed8;
        }

        .no-print-toolbar button.btn-secondary,
        .no-print-toolbar a.btn-secondary {
            background: #475569;
        }

        .no-print-toolbar button.btn-secondary:hover,
        .no-print-toolbar a.btn-secondary:hover {
            background: #334155;
        }

        /* Container Struk Thermal 80mm */
        .struk {
            width: 72mm;
            margin: 20px auto;
            padding: 3mm 0 10mm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.25;
            color: #000;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
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
    {{-- Toolbar Pratinjau Layar --}}
    <div class="no-print-toolbar no-print">
        <button type="button" onclick="window.print()">
            🖨️ Cetak
        </button>

        <button type="button" class="btn-secondary" onclick="handleBack()">
            Kembali
        </button>
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
