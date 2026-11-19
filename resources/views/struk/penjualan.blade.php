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

        /* Mode Cetak Thermal Roll */
        @page {
            size: {{ $lebar === '80' ? '80mm' : '58mm' }} auto;
            margin: 0;
        }

        /* Container Struk */
        .receipt-wrapper {
            background: #fff;
            margin: 20px auto;
            padding: 8px 10px 14px 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        .struk-58 {
            width: 48mm;
            font-size: 11px;
        }

        .struk-80 {
            width: 72mm;
            font-size: 12.5px;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .store-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .store-info {
            font-size: 0.85em;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .double-divider {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            height: 3px;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 1px 0;
        }

        .item-row-name {
            word-break: break-word;
            padding-top: 3px;
            font-weight: 600;
        }

        .item-row-detail {
            display: flex;
            justify-content: space-between;
            font-size: 0.95em;
            padding-bottom: 3px;
        }

        .category-header {
            font-weight: bold;
            font-size: 0.9em;
            padding: 3px 0 1px 0;
            text-transform: uppercase;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .grand-total {
            font-size: 1.1em;
            font-weight: bold;
        }

        .cutter-space {
            height: 10mm; /* Ruang kosong aman untuk auto-cutter printer */
            display: block;
        }

        /* Screen Preview Toolbar */
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
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 12px;
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
            background: #2563eb;
        }

        .no-print-toolbar button.btn-secondary,
        .no-print-toolbar a.btn-secondary {
            background: #475569;
        }

        .no-print-toolbar button.btn-secondary:hover,
        .no-print-toolbar a.btn-secondary:hover {
            background: #334155;
        }

        .no-print-toolbar .width-toggle {
            display: flex;
            background: #334155;
            border-radius: 6px;
            padding: 2px;
        }

        .no-print-toolbar .width-toggle button {
            background: transparent;
            color: #94a3b8;
            padding: 4px 10px;
            border-radius: 4px;
        }

        .no-print-toolbar .width-toggle button.active {
            background: #3b82f6;
            color: #fff;
        }

        @media print {
            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: {{ $lebar === '80' ? '72mm' : '48mm' }} !important;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .receipt-wrapper {
                margin: 0 auto !important;
                padding: 1mm 2mm !important;
                box-shadow: none !important;
                width: {{ $lebar === '80' ? '72mm' : '48mm' }} !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    {{-- Toolbar Pratinjau Layar --}}
    <div class="no-print-toolbar">
        <button type="button" onclick="window.print()">
            🖨️ Cetak Struk
        </button>

        <div class="width-toggle">
            <button type="button" class="{{ $lebar === '58' ? 'active' : '' }}" onclick="switchWidth('58')">58mm</button>
            <button type="button" class="{{ $lebar === '80' ? 'active' : '' }}" onclick="switchWidth('80')">80mm</button>
        </div>

        <button type="button" class="btn-secondary" onclick="handleBack()">
            Kembali
        </button>
    </div>

    {{-- Kertas Struk Thermal --}}
    <div class="receipt-wrapper {{ $lebar === '80' ? 'struk-80' : 'struk-58' }}" id="receipt-area">
        {{-- 1. Header Identitas Toko --}}
        <div class="text-center">
            <div class="store-title">{{ config('toko.nama', 'Pradan Motor') }}</div>
            @if(config('toko.alamat'))
                <div class="store-info">{{ config('toko.alamat') }}</div>
            @endif
            @if(config('toko.telepon'))
                <div class="store-info">Telp: {{ config('toko.telepon') }}</div>
            @endif
        </div>

        <div class="double-divider"></div>

        {{-- 2. Metadata Transaksi --}}
        <table>
            <tr>
                <td style="width: 25%;">No.Trx</td>
                <td style="width: 3%;">:</td>
                <td class="font-bold text-left">#{{ $penjualan->no_transaksi }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($penjualan->tanggal)->timezone('Asia/Jakarta')->translatedFormat('d/m/Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td>{{ $penjualan->user->nama ?? $penjualan->user->name ?? 'Kasir' }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>:</td>
                <td>
                    {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                    @if($penjualan->pelanggan && $penjualan->pelanggan->plat_nomor)
                        ({{ $penjualan->pelanggan->plat_nomor }})
                    @endif
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- 3. Rincian Item (Snapshot dari detail_penjualan) --}}
        @php
            $barangItems = $penjualan->details->where('jenis_item', 'barang');
            $jasaItems = $penjualan->details->where('jenis_item', 'jasa');
            $isCampuran = $barangItems->isNotEmpty() && $jasaItems->isNotEmpty();
        @endphp

        {{-- Kelompok Barang / Suku Cadang --}}
        @if($barangItems->isNotEmpty())
            @if($isCampuran)
                <div class="category-header">[SUKU CADANG]</div>
            @endif
            @foreach($barangItems as $d)
                <div>
                    <div class="item-row-name">{{ $d->nama_item }}</div>
                    <div class="item-row-detail">
                        <span>{{ $d->qty }} x {{ number_format($d->harga_satuan, 0, ',', '.') }}</span>
                        <span class="font-bold">{{ number_format($d->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- Kelompok Jasa Servis --}}
        @if($jasaItems->isNotEmpty())
            @if($isCampuran)
                <div class="category-header" style="margin-top: 4px;">[JASA SERVIS]</div>
            @endif
            @foreach($jasaItems as $d)
                <div>
                    <div class="item-row-name">{{ $d->nama_item }}</div>
                    <div class="item-row-detail">
                        <span>{{ $d->qty }} x {{ number_format($d->harga_satuan, 0, ',', '.') }}</span>
                        <span class="font-bold">{{ number_format($d->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="divider"></div>

        {{-- 4. Ringkasan Total, Bayar, Kembalian --}}
        <table class="totals-table">
            <tr class="grand-total">
                <td class="text-left font-bold">TOTAL</td>
                <td class="text-right font-bold">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left">BAYAR (TUNAI)</td>
                <td class="text-right">Rp {{ number_format($penjualan->nominal_bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left font-bold">KEMBALI</td>
                <td class="text-right font-bold">Rp {{ number_format($penjualan->kembalian ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="double-divider"></div>

        {{-- 5. Footer & Catatan --}}
        <div class="text-center" style="margin-top: 6px;">
            <div style="font-weight: 600; font-size: 0.95em;">{{ config('toko.footer', 'Terima kasih atas kunjungan Anda') }}</div>
            @if(config('toko.catatan'))
                <div style="font-size: 0.8em; margin-top: 3px;">{{ config('toko.catatan') }}</div>
            @endif
        </div>

        {{-- Ruang kosong untuk cutter printer thermal --}}
        <div class="cutter-space"></div>
    </div>

    <script>
        function switchWidth(sz) {
            try {
                localStorage.setItem('receipt_width', sz);
            } catch(e) {}
            const url = new URL(window.location.href);
            url.searchParams.set('lebar', sz);
            window.location.href = url.toString();
        }

        function handleBack() {
            if (window.opener || window.history.length <= 1) {
                window.close();
            } else {
                window.history.back();
            }
        }

        // Simpan preferensi lebar terpilih
        try {
            const currentLebar = @json($lebar);
            localStorage.setItem('receipt_width', currentLebar);
        } catch(e) {}

        // Autoprint trigger jika query parameter autoprint=1
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

