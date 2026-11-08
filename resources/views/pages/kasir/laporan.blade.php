@extends('layouts.app')

@section('title', 'Laporan Kasir')

@section('content')
    <x-page-header 
        title="Laporan Penjualan Kasir" 
        subtitle="Rekapitulasi penjualan harian dan total setoran kasir" 
        icon="laporan" 
    />

    <x-table 
        id="table-laporan-kasir" 
        :headers="['TANGGAL TRANSAKSI', 'JUMLAH TRANSAKSI', 'TOTAL NOMINAL']"
    >
        @forelse($rekapHarian as $rekap)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($rekap->tgl)->translatedFormat('d F Y') }}
                </td>
                <td class="py-3.5 px-4 text-gray-600">{{ $rekap->jml_transaksi }} Transaksi</td>
                <td class="py-3.5 px-4 last:pr-6 font-bold text-gray-900">Rp {{ number_format($rekap->total_nominal, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="py-8 text-center text-gray-400">Belum ada transaksi</td>
            </tr>
        @endforelse
        <tr class="bg-gray-50/80 font-bold text-gray-900 border-t border-gray-200">
            <td class="py-3.5 px-4 first:pl-6">TOTAL PENJUALAN:</td>
            <td class="py-3.5 px-4 text-gray-700">{{ $totalTransaksi }} Transaksi</td>
            <td class="py-3.5 px-4 last:pr-6 text-blue-600 text-sm">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
        </tr>
    </x-table>

    <div class="flex items-center gap-3 mt-5">
        <x-button
            variant="primary"
            size="sm"
            icon="printer"
            text="Cetak Rekap Kasir"
            onclick="openModal('modal-cetak-rekap-kasir')"
        />
    </div>

    <x-modal
        id="modal-cetak-rekap-kasir"
        title="Cetak Rekap Kasir"
        subtitle="Pilih format rekap yang ingin dicetak atau diunduh"
    >
        <p>Rekap berisi seluruh transaksi kasir dan total penjualan.</p>

        <x-slot:footer>
            <div class="flex w-full items-center justify-between">
                <x-button
                    variant="secondary"
                    size="sm"
                    text="Batal"
                    onclick="closeModal('modal-cetak-rekap-kasir')"
                />
                <div class="flex items-center gap-2.5">
                    <x-button
                        variant="outline-success"
                        size="sm"
                        text="Excel (CSV)"
                        onclick="exportCashierReport('excel')"
                    />
                    <x-button
                        variant="outline-danger"
                        size="sm"
                        text="Cetak PDF"
                        onclick="exportCashierReport('pdf')"
                    />
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script>
        function exportCashierReport(format) {
            const table = document.getElementById('table-laporan-kasir');

            if (!table) {
                window.alert('Tabel rekap kasir tidak ditemukan.');
                return;
            }

            closeModal('modal-cetak-rekap-kasir');

            if (format === 'pdf') {
                const printWindow = window.open('', '_blank');

                if (!printWindow) {
                    window.alert('Popup diblokir. Izinkan popup untuk mencetak rekap kasir.');
                    return;
                }

                printWindow.document.open();
                printWindow.document.write(`
                    <!doctype html>
                    <html lang="id">
                    <head>
                        <meta charset="utf-8">
                        <title>Rekap Penjualan Kasir</title>
                        <style>
                            body { font-family: Arial, sans-serif; color: #111827; }
                            h1 { margin-bottom: 4px; font-size: 20px; }
                            p { margin-top: 0; color: #4b5563; font-size: 12px; }
                            table { width: 100%; border-collapse: collapse; font-size: 12px; }
                            th, td { padding: 8px; border: 1px solid #d1d5db; text-align: left; }
                            th { background: #f3f4f6; }
                            @page { margin: 18mm; }
                        </style>
                    </head>
                    <body>
                        <h1>Rekap Penjualan Kasir</h1>
                        <p>Dicetak ${new Date().toLocaleDateString('id-ID')}</p>
                        ${table.outerHTML}
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();

                return;
            }

            if (format !== 'excel') {
                window.alert('Format rekap tidak dikenali.');
                return;
            }

            const csv = Array.from(table.rows)
                .map(row => Array.from(row.cells)
                    .map(cell => `"${cell.innerText.trim().replace(/"/g, '""')}"`)
                    .join(','))
                .join('\r\n');
            const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
            const downloadUrl = URL.createObjectURL(blob);
            const downloadLink = document.createElement('a');

            downloadLink.href = downloadUrl;
            downloadLink.download = `rekap-kasir-${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            downloadLink.remove();
            window.setTimeout(() => URL.revokeObjectURL(downloadUrl), 1000);
        }
    </script>
@endpush
