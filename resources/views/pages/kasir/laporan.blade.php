@extends('layouts.app')

@php
    $currentTitle = $tipe === 'rekap' ? 'Rekapitulasi Penjualan Harian Kasir' : 'Laporan Transaksi Penjualan Kasir';
@endphp

@section('title', 'Laporan Kasir')

@section('content')
    {{-- Kop Surat & Header Cetak Resmi (Hanya tampil saat dicetak ke PDF / Kertas) --}}
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

        {{-- Meta Info Dokumen --}}
        <div class="flex justify-between items-center text-[10px] text-gray-600 mb-2 border-b border-gray-200 pb-1.5">
            <div>
                Petugas Cetak: <strong class="text-black">{{ auth()->user()->nama ?? auth()->user()->name ?? 'Kasir' }}</strong> ({{ ucfirst(auth()->user()->role ?? 'Kasir') }})
            </div>
            <div>
                Waktu Cetak: <strong id="print-timestamp" class="text-black">{{ \Carbon\Carbon::now()->translatedFormat('d M Y, H:i') }} WIB</strong>
            </div>
        </div>
    </div>

    {{-- Page Header (Hanya di Web) --}}
    <x-page-header 
        title="Laporan Transaksi Kasir" 
        subtitle="Rekapitulasi penjualan, rincian transaksi, dan total pendapatan kasir bengkel" 
        icon="laporan" 
    />

    {{-- Ringkasan Statistik Periode (Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 print:hidden">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pendapatan</p>
                <p class="text-lg sm:text-xl font-black text-blue-600 mt-1">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                @include('components.icon', ['name' => 'dollar', 'size' => 20])
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Transaksi</p>
                <p class="text-lg sm:text-xl font-black text-gray-900 mt-1">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                @include('components.icon', ['name' => 'transaksi', 'size' => 20])
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rata-rata Transaksi</p>
                <p class="text-lg sm:text-xl font-black text-gray-900 mt-1">Rp {{ number_format($rataRata, 0, ',', '.') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                @include('components.icon', ['name' => 'chart', 'size' => 20])
            </div>
        </div>
    </div>

    {{-- Filter Panel: Tipe Tampilan & Tanggal Periode (Hanya di Web) --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-xs mb-4 print:hidden">
        <form method="GET" action="{{ route('kasir.laporan') }}" id="form-filter-laporan">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                <!-- Dropdown Tipe Laporan -->
                <div class="lg:col-span-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Tipe Laporan
                    </label>
                    <div class="relative">
                        <select name="tipe" 
                                onchange="document.getElementById('form-filter-laporan').submit()"
                                class="w-full px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 shadow-2xs focus:bg-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                            <option value="transaksi" {{ $tipe === 'transaksi' ? 'selected' : '' }}>Rincian Seluruh Transaksi</option>
                            <option value="rekap" {{ $tipe === 'rekap' ? 'selected' : '' }}>Rekapitulasi Penjualan Harian</option>
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
                    <a href="{{ route('kasir.laporan', ['tipe' => $tipe]) }}" 
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-medium transition-colors cursor-pointer"
                       title="Reset Periode">
                        @include('components.icon', ['name' => 'refresh', 'size' => 14])
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Laporan: Rincian Transaksi ATAU Rekapitulasi Harian --}}
    @if($tipe === 'transaksi')
        <x-table 
            id="table-laporan-transaksi-kasir" 
            :searchable="true" 
            searchPlaceholder="Cari no transaksi, kasir, atau pelanggan..." 
            :headers="[
                'NO. TRANSAKSI', 
                'TANGGAL & WAKTU', 
                'KASIR', 
                'PELANGGAN', 
                'TOTAL', 
                'NOMINAL BAYAR', 
                'KEMBALIAN', 
                ['text' => 'AKSI', 'class' => 'print:hidden action-col text-center']
            ]"
        >
            @forelse($transaksis as $trx)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $trx->no_transaksi }}</td>
                    <td class="py-3.5 px-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y, H:i') }}
                    </td>
                    <td class="py-3.5 px-4 text-gray-700 font-medium">
                        {{ $trx->user->nama ?? $trx->user->name ?? 'Kasir' }}
                    </td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">
                        {{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}
                    </td>
                    <td class="py-3.5 px-4 font-bold text-blue-600">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-4 text-gray-600">
                        Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-4 font-semibold text-emerald-600">
                        Rp {{ number_format($trx->kembalian ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-4 last:pr-6 action-col print:hidden text-center">
                        <x-button-action 
                            type="info" 
                            onclick="openModal('modal-detail-transaksi-{{ $trx->id_transaksi }}')" 
                            title="Lihat Detail Transaksi" 
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-10 text-center text-gray-400">Tidak ada transaksi penjualan pada rentang tanggal ini</td>
                </tr>
            @endforelse

            <x-slot:tfoot>
                <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr>
                        <td colspan="4" class="py-3 px-4 first:pl-6 text-right uppercase tracking-wider text-xs">Total Penjualan:</td>
                        <td class="py-3 px-4 text-blue-700 font-bold text-xs">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                        <td colspan="2" class="py-3 px-4 text-gray-600 font-medium text-xs">({{ $totalTransaksi }} Transaksi)</td>
                        <td class="py-3 px-4 last:pr-6 print:hidden action-col"></td>
                    </tr>
                </tfoot>
            </x-slot:tfoot>
        </x-table>

    @else
        <x-table 
            id="table-laporan-rekap-kasir" 
            :searchable="true" 
            searchPlaceholder="Cari tanggal rekap..." 
            :headers="['TANGGAL TRANSAKSI', 'JUMLAH TRANSAKSI', 'TOTAL NOMINAL']"
        >
            @forelse($rekapHarian as $rekap)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($rekap->tgl)->translatedFormat('d F Y') }}
                    </td>
                    <td class="py-3.5 px-4 text-gray-600">{{ $rekap->jml_transaksi }} Transaksi</td>
                    <td class="py-3.5 px-4 last:pr-6 font-bold text-blue-600">
                        Rp {{ number_format($rekap->total_nominal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-10 text-center text-gray-400">Belum ada transaksi pada rentang tanggal ini</td>
                </tr>
            @endforelse

            <x-slot:tfoot>
                <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-300">
                    <tr>
                        <td class="py-3.5 px-4 first:pl-6 uppercase tracking-wider text-xs">TOTAL PENJUALAN:</td>
                        <td class="py-3.5 px-4 text-gray-700 text-xs">{{ $totalTransaksi }} Transaksi</td>
                        <td class="py-3.5 px-4 last:pr-6 text-blue-700 font-bold text-xs">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </x-slot:tfoot>
        </x-table>
    @endif

    {{-- Tombol Cetak PDF dan Ekspor Excel --}}
    <div class="export-buttons flex items-center gap-3 mt-6 print:hidden">
        <x-button 
            variant="outline-danger" 
            size="sm" 
            icon="printer" 
            text="Cetak / PDF" 
            onclick="printReport()" 
        />
        <x-button 
            variant="outline-success" 
            size="sm" 
            icon="clipboard" 
            text="Ekspor CSV / Excel" 
            onclick="exportReportToCSV('Laporan_{{ $tipe === 'rekap' ? 'Rekap_Harian' : 'Transaksi' }}_Kasir_{{ $tanggalMulai }}_sd_{{ $tanggalAkhir }}.csv')" 
        />
    </div>

    {{-- Modal Detail Transaksi (Struk Itemization) --}}
    @if($tipe === 'transaksi')
        @foreach($transaksis as $trx)
            <x-modal 
                id="modal-detail-transaksi-{{ $trx->id_transaksi }}" 
                title="Struk Transaksi #{{ $trx->no_transaksi }}" 
                subtitle="Waktu: {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d F Y, H:i') }}"
                size="lg"
                :footer="false"
            >
                <div class="mb-4 text-sm text-gray-600 grid grid-cols-2 gap-2">
                    <div><span class="font-semibold text-gray-800">Pelanggan:</span> {{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}</div>
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
            </x-modal>
        @endforeach
    @endif

    {{-- Script Cetak Realtime & Ekspor CSV/Excel --}}
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

        window.addEventListener('beforeprint', updatePrintTime);

        function exportReportToCSV(filename) {
            const table = document.querySelector('.data-table-container table') || document.querySelector('table');
            if (!table) {
                alert('Tabel laporan tidak ditemukan');
                return;
            }

            let csv = [];
            const rows = table.querySelectorAll('tr');

            for (let i = 0; i < rows.length; i++) {
                let cols = rows[i].querySelectorAll('th, td');
                if (cols.length === 1 && cols[0].hasAttribute('colspan')) continue;

                let rowData = [];
                for (let j = 0; j < cols.length; j++) {
                    // Abaikan kolom 'AKSI' saat ekspor ke spreadsheet
                    if (cols[j].classList.contains('action-col') || cols[j].innerText.trim() === 'AKSI') {
                        continue;
                    }

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

            // UTF-8 BOM agar terbaca langsung dengan rapi oleh Microsoft Excel
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
