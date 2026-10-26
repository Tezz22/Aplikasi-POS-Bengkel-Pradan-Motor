<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $penjualan->no_transaksi }} - Pradan Motor</title>
    <style>
        body {
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .receipt {
            box-sizing: border-box;
            width: min(100% - 2rem, 32rem);
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            box-shadow: 0 10px 25px rgb(0 0 0 / 8%);
        }

        .center {
            text-align: center;
        }

        .muted {
            color: #6b7280;
            font-size: 0.85rem;
        }

        .divider {
            margin: 1rem 0;
            border: 0;
            border-top: 1px dashed #9ca3af;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        th,
        td {
            padding: 0.45rem 0;
            text-align: left;
            vertical-align: top;
        }

        th:last-child,
        td:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 0.5rem 0;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .actions a,
        .actions button {
            padding: 0.65rem 1rem;
            border: 0;
            border-radius: 0.6rem;
            background: #2563eb;
            color: white;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
        }

        .actions a {
            background: #e5e7eb;
            color: #374151;
        }

        @media print {
            body {
                background: white;
            }

            .receipt {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="receipt">
        <header class="center">
            <h1>PRADAN MOTOR</h1>
            <p class="muted">Bukti pembayaran transaksi</p>
        </header>

        <hr class="divider">

        <p class="muted">No. Transaksi: <strong>{{ $penjualan->no_transaksi }}</strong></p>
        <p class="muted">Tanggal: {{ $penjualan->tanggal->format('d/m/Y H:i') }}</p>
        <p class="muted">Kasir: {{ $penjualan->user->nama }}</p>
        <p class="muted">Pelanggan: {{ $penjualan->pelanggan->nama ?? 'Pelanggan Umum' }}</p>

        <hr class="divider">

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $detail)
                    <tr>
                        <td>
                            {{ $detail->nama_item }}
                            <div class="muted">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }} / item</div>
                        </td>
                        <td>{{ $detail->qty }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="divider">

        <div class="totals">
            <p><strong>Total</strong><strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong></p>
            <p><span>Tunai</span><span>Rp {{ number_format($penjualan->nominal_bayar, 0, ',', '.') }}</span></p>
            <p><span>Kembalian</span><span>Rp {{ number_format($penjualan->kembalian ?? 0, 0, ',', '.') }}</span></p>
        </div>

        <hr class="divider">
        <p class="center muted">Terima kasih telah berbelanja.</p>

        <div class="actions">
            <a href="{{ route('kasir.transaksi') }}">Kembali ke transaksi</a>
            <button type="button" onclick="window.print()">Cetak Struk</button>
        </div>
    </main>
    @if($autoPrint)
        <script>
            window.addEventListener('load', () => window.print());
        </script>
    @endif
</body>
</html>
