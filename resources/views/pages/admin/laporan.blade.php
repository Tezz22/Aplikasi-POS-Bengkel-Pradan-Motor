@extends('layouts.app')

@php
    $jenisTitles = [
        'penjualan' => 'Laporan Penjualan (POS)',
        'pembelian' => 'Laporan Pembelian (Barang Masuk)',
        'stok'      => 'Laporan Mutasi Stok (Stok Opname)',
        'jasa'      => 'Laporan Jasa Servis',
    ];
    $currentTitle = $jenisTitles[$jenis] ?? 'Laporan';
@endphp

@section('title', $currentTitle)

@section('content')
    {{-- Kop Surat & Header Cetak Resmi (Hanya tampil saat dicetak ke PDF/Kertas) --}}
    <div class="hidden print:block mb-5">
        <div class="border-b-2 border-black pb-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-black text-black tracking-wider uppercase">BENGKEL PRADAN MOTOR</h1>
                    <p class="text-[11px] text-gray-700 font-medium mt-0.5">Spesialis Servis Motor, Tune Up, Ganti Oli & Penyedia Suku Cadang Resmi</p>
                    <p class="text-[10px] text-gray-600 mt-0.5">Dkh.jinontro, RT.03/RW.02, Gendol, Tegalsari, Kec. Jetis, Kabupaten Ponorogo, Jawa Timur 63473</p>
                </div>
            </div>
        </div>
        <div class="border-b border-black mt-0.5"></div>

        {{-- Judul & Periode Dokumen --}}
        <div class="text-center my-4">
            <h2 class="text-base font-extrabold text-black uppercase tracking-wide">
                {{ $currentTitle }}
            </h2>
            <p class="text-xs text-gray-700 mt-0.5 font-medium">
                Periode: <strong class="text-black">{{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}</strong> s/d <strong class="text-black">{{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y') }}</strong>
            </p>
        </div>

        {{-- Meta Info Ringkas Dokumen --}}
        <div class="flex justify-between items-center text-[10px] text-gray-600 mb-2 border-b border-gray-200 pb-1.5">
            <div>
                Dicetak Oleh: <strong class="text-black">{{ auth()->user()->nama ?? auth()->user()->name ?? 'Administrator' }}</strong> ({{ ucfirst(auth()->user()->role ?? 'Admin') }})
            </div>
            <div>
                Waktu Cetak: <strong id="print-timestamp" class="text-black">{{ \Carbon\Carbon::now()->translatedFormat('d M Y, H:i') }} WIB</strong>
            </div>
        </div>
    </div>

    {{-- Page Header (Hanya di Web) --}}
    <x-page-header 
        title="Laporan Bengkel" 
        subtitle="Rekapitulasi data transaksi, pengadaan barang, mutasi stok, dan jasa servis bengkel" 
        icon="laporan" 
    />

    {{-- Filter Panel: Jenis Laporan & Tanggal Periode (Hanya di Web) --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-xs mb-1 print:hidden">
        <form method="GET" action="{{ route('admin.laporan') }}" id="form-filter-laporan">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                <!-- Dropdown Jenis Laporan -->
                <div class="lg:col-span-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Jenis Laporan
                    </label>
                    <div class="relative">
                        <select name="jenis" 
                                onchange="document.getElementById('form-filter-laporan').submit()"
                                class="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 shadow-2xs focus:bg-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            <option value="penjualan" {{ $jenis === 'penjualan' ? 'selected' : '' }}>Laporan Penjualan (POS)</option>
                            <option value="pembelian" {{ $jenis === 'pembelian' ? 'selected' : '' }}>Laporan Pembelian (Barang Masuk)</option>
                            <option value="stok" {{ $jenis === 'stok' ? 'selected' : '' }}>Laporan Mutasi Stok (Stok Opname)</option>
                            <option value="jasa" {{ $jenis === 'jasa' ? 'selected' : '' }}>Laporan Jasa Servis</option>
                        </select>
                    </div>
                </div>

                <!-- Input Tanggal Mulai -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Dari Tanggal
                    </label>
                    <input type="date" 
                           name="tanggal_mulai" 
                           value="{{ $tanggalMulai }}" 
                           class="w-full px-3.5 py-2 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 shadow-2xs focus:bg-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                </div>

                <!-- Input Tanggal Selesai -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Sampai Tanggal
                    </label>
                    <input type="date" 
                           name="tanggal_akhir" 
                           value="{{ $tanggalAkhir }}" 
                           class="w-full px-3.5 py-2 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 shadow-2xs focus:bg-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                </div>

                <!-- Tombol Filter & Reset -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        @include('components.icon', ['name' => 'search', 'size' => 14])
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('admin.laporan', ['jenis' => $jenis]) }}" 
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-medium transition-colors cursor-pointer"
                       title="Reset Periode Bulan Ini">
                        @include('components.icon', ['name' => 'refresh', 'size' => 14])
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Laporan Dinamis Berdasarkan Jenis Laporan --}}
    @if($jenis === 'penjualan')
        <x-table 
            id="table-laporan-penjualan" 
            :searchable="true" 
            searchPlaceholder="Cari no transaksi, kasir, pelanggan..."
            :headers="['NO. TRANSAKSI', 'TANGGAL & WAKTU', 'KASIR', 'PELANGGAN', 'TOTAL', 'BAYAR', 'KEMBALIAN']"
        >
            @forelse($data as $row)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $row->no_transaksi }}</td>
                    <td class="py-3.5 px-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y, H:i') }}
                    </td>
                    <td class="py-3.5 px-4 text-gray-700 font-medium">{{ $row->user->nama ?? $row->user->name ?? 'Kasir' }}</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $row->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                    <td class="py-3.5 px-4 font-bold text-blue-600">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                    <td class="py-3.5 px-4 text-gray-600">Rp {{ number_format($row->nominal_bayar, 0, ',', '.') }}</td>
                    <td class="py-3.5 px-4 last:pr-6 text-emerald-600 font-semibold">Rp {{ number_format($row->kembalian ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-gray-400">Tidak ada transaksi penjualan pada rentang tanggal ini</td>
                </tr>
            @endforelse

            <x-slot:tfoot>
                <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr>
                        <td colspan="4" class="py-3 px-4 first:pl-6 text-right uppercase tracking-wider text-xs">Total Penjualan:</td>
                        <td class="py-3 px-4 text-blue-700 font-bold text-xs">Rp {{ number_format($summary['total_nominal'] ?? 0, 0, ',', '.') }}</td>
                        <td colspan="2" class="py-3 px-4 last:pr-6 text-gray-600 font-medium text-xs">({{ $summary['total_transaksi'] ?? count($data) }} Transaksi)</td>
                    </tr>
                </tfoot>
            </x-slot:tfoot>
        </x-table>

    @elseif($jenis === 'pembelian')
        <x-table 
            id="table-laporan-pembelian" 
            :searchable="true" 
            searchPlaceholder="Cari no referensi, barang, supplier..."
            :headers="['NO. REFERENSI', 'TANGGAL', 'NAMA BARANG', 'SUPPLIER', 'JUMLAH', 'HARGA BELI', 'SUBTOTAL']"
        >
            @forelse($data as $row)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $row->no_referensi }}</td>
                    <td class="py-3.5 px-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $row->barang->nama_barang ?? '-' }}</td>
                    <td class="py-3.5 px-4 text-gray-700">{{ $row->supplier->nama_supplier ?? '-' }}</td>
                    <td class="py-3.5 px-4 font-medium text-gray-800">
                        {{ $row->jumlah }} {{ $row->barang->satuan ?? 'Pcs' }}
                    </td>
                    <td class="py-3.5 px-4 text-gray-600">Rp {{ number_format($row->harga_beli, 0, ',', '.') }}</td>
                    <td class="py-3.5 px-4 last:pr-6 font-bold text-rose-600">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-gray-400">Tidak ada pengadaan barang masuk pada rentang tanggal ini</td>
                </tr>
            @endforelse

            <x-slot:tfoot>
                <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr>
                        <td colspan="4" class="py-3 px-4 first:pl-6 text-right uppercase tracking-wider text-xs">Total Pengadaan:</td>
                        <td class="py-3 px-4 text-gray-800 text-xs">{{ number_format($summary['total_item'] ?? 0, 0, ',', '.') }} Item</td>
                        <td class="py-3 px-4 text-gray-400 text-xs">-</td>
                        <td class="py-3 px-4 last:pr-6 text-rose-700 font-bold text-xs">Rp {{ number_format($summary['total_nominal'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </x-slot:tfoot>
        </x-table>

    @elseif($jenis === 'stok')
        <x-table 
            id="table-laporan-stok" 
            :searchable="true" 
            searchPlaceholder="Cari barang, petugas, keterangan..."
            :headers="['TANGGAL', 'PETUGAS', 'NAMA BARANG', 'STOK SISTEM', 'STOK FISIK', 'SELISIH', 'KETERANGAN']"
        >
            @forelse($data as $row)
                @php
                    $selisih = $row->stok_fisik - $row->stok_sistem;
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 text-gray-600">
                        {{ $row->opname && $row->opname->tanggal ? \Carbon\Carbon::parse($row->opname->tanggal)->translatedFormat('d M Y, H:i') : '-' }}
                    </td>
                    <td class="py-3.5 px-4 font-medium text-gray-800">{{ $row->opname->user->nama ?? $row->opname->user->name ?? 'Admin' }}</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $row->barang->nama_barang ?? '-' }}</td>
                    <td class="py-3.5 px-4 text-gray-600">{{ $row->stok_sistem }}</td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $row->stok_fisik }}</td>
                    <td class="py-3.5 px-4">
                        @if($selisih > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                +{{ $selisih }}
                            </span>
                        @elseif($selisih < 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">
                                {{ $selisih }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                0
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 last:pr-6 text-gray-500 text-xs">{{ $row->alasan ?? $row->opname->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-gray-400">Tidak ada riwayat mutasi / stok opname pada rentang tanggal ini</td>
                </tr>
            @endforelse

            <x-slot:tfoot>
                <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr>
                        <td colspan="5" class="py-3 px-4 first:pl-6 text-right uppercase tracking-wider text-xs">Total Selisih Unit:</td>
                        <td colspan="2" class="py-3 px-4 last:pr-6 text-gray-900 font-bold text-xs">{{ number_format($summary['total_selisih'] ?? 0, 0, ',', '.') }} Unit</td>
                    </tr>
                </tfoot>
            </x-slot:tfoot>
        </x-table>

    @elseif($jenis === 'jasa')
        <x-table 
            id="table-laporan-jasa" 
            :searchable="true" 
            searchPlaceholder="Cari layanan, transaksi, pelanggan..."
            :headers="['TANGGAL', 'NO. TRANSAKSI', 'NAMA JASA / LAYANAN', 'KASIR', 'PELANGGAN', 'QTY', 'TARIF', 'SUBTOTAL']"
        >
            @forelse($data as $row)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 text-gray-600">
                        {{ $row->penjualan && $row->penjualan->tanggal ? \Carbon\Carbon::parse($row->penjualan->tanggal)->translatedFormat('d M Y, H:i') : '-' }}
                    </td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $row->penjualan->no_transaksi ?? '-' }}</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $row->nama_item }}</td>
                    <td class="py-3.5 px-4 text-gray-600">{{ $row->penjualan->user->nama ?? $row->penjualan->user->name ?? 'Kasir' }}</td>
                    <td class="py-3.5 px-4 font-medium text-gray-800">{{ $row->penjualan->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                    <td class="py-3.5 px-4 text-gray-600">{{ $row->qty }}x</td>
                    <td class="py-3.5 px-4 text-gray-600">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                    <td class="py-3.5 px-4 last:pr-6 font-bold text-blue-600">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-10 text-center text-gray-400">Tidak ada transaksi layanan jasa servis pada rentang tanggal ini</td>
                </tr>
            @endforelse

            <x-slot:tfoot>
                <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr>
                        <td colspan="5" class="py-3 px-4 first:pl-6 text-right uppercase tracking-wider text-xs">Total Pendapatan Jasa:</td>
                        <td class="py-3 px-4 text-gray-800 text-xs">{{ number_format($summary['total_jasa'] ?? 0, 0, ',', '.') }}x</td>
                        <td class="py-3 px-4 text-gray-400 text-xs">-</td>
                        <td class="py-3 px-4 last:pr-6 text-blue-700 font-bold text-xs">Rp {{ number_format($summary['total_nominal'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </x-slot:tfoot>
        </x-table>
    @endif

    <!-- Export Action Buttons -->
    <div class="export-buttons flex items-center gap-3 mt-6">
        <x-button 
            variant="outline-danger" 
            size="sm" 
            text="Cetak / PDF" 
            onclick="printReport()" 
        />
        <x-button 
            variant="outline-success" 
            size="sm" 
            text="Ekspor CSV / Excel" 
            onclick="exportReportToCSV('Laporan_{{ $jenis }}_{{ $tanggalMulai }}_sd_{{ $tanggalAkhir }}.csv')" 
        />
    </div>

    <!-- Script Export CSV & Cetak Realtime -->
    <script>
        function updatePrintTime() {
            const el = document.getElementById('print-timestamp');
            if (!el) return;

            const now = new Date();
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const day = String(now.getDate()).padStart(2, '0');
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            el.innerText = `${day} ${month} ${year}, ${hours}:${minutes} WIB`;
        }

        function printReport() {
            updatePrintTime();
            window.print();
        }

        // Pastikan waktu cetak ter-update saat user klik Cetak maupun Ctrl+P
        window.addEventListener('beforeprint', updatePrintTime);

        function exportReportToCSV(filename) {
            const table = document.querySelector('.data-table-container table') || document.querySelector('table');
            if (!table) {
                alert('Tabel tidak ditemukan');
                return;
            }

            let csv = [];
            const rows = table.querySelectorAll('tr');

            for (let i = 0; i < rows.length; i++) {
                let cols = rows[i].querySelectorAll('th, td');
                if (cols.length === 1 && cols[0].hasAttribute('colspan')) continue;

                let rowData = [];
                for (let j = 0; j < cols.length; j++) {
                    let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').trim();
                    text = text.replace(/"/g, '""');
                    rowData.push('"' + text + '"');
                }
                if (rowData.length > 0) {
                    csv.push(rowData.join(';'));
                }
            }

            if (csv.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            const blob = new Blob(['\uFEFF' + csv.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
