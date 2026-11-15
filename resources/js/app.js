window.exportCashierReport = function (format) {
    const table = document.getElementById('table-laporan-kasir');

    if (!table) {
        window.alert('Tabel rekap kasir tidak ditemukan.');
        return;
    }

    if (format !== 'pdf' && format !== 'excel') {
        window.alert('Format rekap tidak dikenali.');
        return;
    }

    const rows = Array.from(table.rows, row =>
        Array.from(row.cells, cell => cell.innerText.trim())
    );
    const filename = `rekap-kasir-${new Date().toISOString().slice(0, 10)}`;

    if (format === 'pdf') {
        const printWindow = window.open('', '_blank');

        if (!printWindow) {
            window.alert('Popup diblokir. Izinkan popup untuk mencetak rekap kasir.');
            return;
        }

        const escapeHtml = value => value.replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        })[character]);
        const tableHtml = rows.map((row, rowIndex) => {
            const tag = rowIndex === 0 ? 'th' : 'td';
            const cells = row.map(cell => `<${tag}>${escapeHtml(cell)}</${tag}>`).join('');

            return `<tr>${cells}</tr>`;
        }).join('');

        printWindow.document.open();
        printWindow.document.write(`
            <!doctype html>
            <html lang="id">
            <head>
                <meta charset="utf-8">
                <title>${filename}</title>
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
                <table>${tableHtml}</table>
            </body>
            </html>
        `);
        printWindow.document.close();
        window.closeModal('modal-cetak-rekap-kasir');
        window.setTimeout(() => {
            printWindow.focus();
            printWindow.print();
        }, 250);

        return;
    }

    const csv = rows
        .map(row => row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(','))
        .join('\r\n');
    const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
    const downloadUrl = URL.createObjectURL(blob);
    const downloadLink = document.createElement('a');

    downloadLink.href = downloadUrl;
    downloadLink.download = `${filename}.csv`;
    document.body.appendChild(downloadLink);
    downloadLink.click();
    downloadLink.remove();
    window.closeModal('modal-cetak-rekap-kasir');
    window.setTimeout(() => URL.revokeObjectURL(downloadUrl), 1000);
};
