@extends('layouts.app')

@section('title', 'Penyesuaian Stok (Opname)')

@section('content')
    {{-- Page Header --}}
    <x-page-header 
        title="Penyesuaian Stok (Opname)" 
        subtitle="Kelola audit dan penyesuaian stok sistem dengan stok fisik bengkel" 
        icon="stok-opname"
    >
        <x-slot:actions>
            <x-button variant="primary" icon="plus" text="Mulai Sesi Opname" onclick="openModal('modal-stok-opname')" />
        </x-slot:actions>
    </x-page-header>

    {{-- Metric Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <x-summary-card 
            label="Total Sesi Opname" 
            :value="number_format($totalSesi, 0, ',', '.')" 
            sub="sesi audit tercatat" 
            icon="clipboard" 
            type="info" 
        />
        <x-summary-card 
            label="Item Diperiksa" 
            :value="number_format($itemDiperiksa, 0, ',', '.')" 
            sub="total pemeriksaan barang" 
            icon="bag" 
            type="success" 
        />
        <x-summary-card 
            label="Item Selisih" 
            :value="number_format($itemSelisih, 0, ',', '.')" 
            sub="perbedaan fisik & sistem" 
            icon="alert" 
            :type="$itemSelisih > 0 ? 'danger' : 'success'" 
        />
        <x-summary-card 
            label="Total Suku Cadang" 
            :value="number_format($totalBarang, 0, ',', '.')" 
            sub="barang aktif terdaftar" 
            icon="tool" 
            type="warning" 
        />
    </div>

    {{-- Filter Toolbar: Status Filter Pills & Tanggal --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Status Filter Tabs / Pills --}}
        <div class="flex items-center gap-1.5 p-1 bg-gray-50 border border-gray-200/60 rounded-xl overflow-x-auto text-xs">
            <a href="{{ route('admin.stok-opname', array_merge(request()->except('status', 'page'), ['status' => 'semua'])) }}" 
               class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-all {{ $statusFilter === 'semua' ? 'bg-white text-blue-600 shadow-2xs' : 'text-gray-500 hover:text-gray-800' }}">
                Semua Sesi
            </a>
            <a href="{{ route('admin.stok-opname', array_merge(request()->except('status', 'page'), ['status' => 'selisih'])) }}" 
               class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === 'selisih' ? 'bg-white text-rose-600 shadow-2xs' : 'text-gray-500 hover:text-gray-800' }}">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Ada Selisih</span>
            </a>
            <a href="{{ route('admin.stok-opname', array_merge(request()->except('status', 'page'), ['status' => 'sesuai'])) }}" 
               class="px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === 'sesuai' ? 'bg-white text-emerald-600 shadow-2xs' : 'text-gray-500 hover:text-gray-800' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Sesuai</span>
            </a>
        </div>

        {{-- Filter Rentang Tanggal --}}
        <form method="GET" action="{{ route('admin.stok-opname') }}" class="flex flex-wrap items-center gap-2 text-xs">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="flex items-center gap-1.5">
                <span class="text-gray-500 font-medium">Dari:</span>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" 
                       class="px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:bg-white focus:border-blue-500 outline-none">
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-gray-500 font-medium">Sampai:</span>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" 
                       class="px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:bg-white focus:border-blue-500 outline-none">
            </div>
            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-2xs transition-colors">
                Filter
            </button>
            @if($tanggalMulai || $tanggalAkhir || ($statusFilter && $statusFilter !== 'semua'))
                <a href="{{ route('admin.stok-opname') }}" class="px-2.5 py-1.5 text-gray-500 hover:text-gray-800 font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table Card --}}
    <x-table 
        id="table-stok-opname" 
        :searchable="true" 
        searchPlaceholder="Cari tanggal, item, keterangan, atau petugas..." 
        :headers="['NO', 'TANGGAL & WAKTU', 'ITEM DIPERIKSA', 'STATUS SELISIH', 'KETERANGAN', 'PETUGAS', 'AKSI']"
    >
        @forelse($opnames as $opname)
            @php
                $totalDetails = $opname->details->count();
                $selisihCount = $opname->details->filter(fn($d) => (int)$d->stok_fisik !== (int)$d->stok_sistem)->count();
                $firstDetail = $opname->details->first();
                $otherCount = $totalDetails - 2;
                $previewItems = $opname->details->take(2);
            @endphp
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-semibold text-gray-500">{{ $loop->iteration }}</td>
                <td class="py-3.5 px-4 text-gray-700 whitespace-nowrap">
                    <span class="font-medium text-gray-900 block">{{ \Carbon\Carbon::parse($opname->tanggal)->translatedFormat('d M Y') }}</span>
                    <span class="text-[11px] text-gray-400 font-normal">{{ \Carbon\Carbon::parse($opname->tanggal)->format('H:i') }} WIB</span>
                </td>
                <td class="py-3.5 px-4">
                    <div class="font-bold text-gray-800">{{ $totalDetails }} item</div>
                    <div class="text-[11px] text-gray-500 font-normal leading-snug mt-0.5 max-w-[240px]">
                        @foreach($previewItems as $idx => $d)
                            <span class="truncate inline-block max-w-[180px] align-bottom">{{ $d->barang->nama_barang ?? 'Barang #' . $d->id_barang }}</span>@if(!$loop->last), @endif
                        @endforeach
                        @if($otherCount > 0)
                            <span class="text-blue-600 font-medium">+{{ $otherCount }} lainnya</span>
                        @endif
                    </div>
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap">
                    @if($selisihCount > 0)
                        <div class="flex flex-col gap-0.5 items-start">
                            <x-badge type="danger" icon="alert" text="Ada Selisih" />
                            <span class="text-[10px] text-rose-600 font-medium pl-1">
                                {{ $selisihCount }} dari {{ $totalDetails }} item
                            </span>
                        </div>
                    @else
                        <div class="flex flex-col gap-0.5 items-start">
                            <x-badge type="success" icon="check" text="Sesuai" />
                            <span class="text-[10px] text-emerald-600 font-medium pl-1">
                                Semua sesuai (0 selisih)
                            </span>
                        </div>
                    @endif
                </td>
                <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate" title="{{ $opname->keterangan ?? '-' }}">
                    {{ $opname->keterangan ?? '-' }}
                </td>
                <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                    <span class="font-medium text-gray-800">{{ $opname->user->nama ?? $opname->user->name ?? 'Admin' }}</span>
                </td>
                <td class="py-3.5 px-4 last:pr-6 whitespace-nowrap">
                    <div class="flex items-center gap-1">
                        <x-button-action type="info" onclick="openModal('modal-detail-opname-{{ $opname->id_opname }}')" title="Lihat Detail Penyesuaian" />
                        <x-button-action type="edit" onclick="openModal('modal-edit-opname-{{ $opname->id_opname }}')" title="Edit / Revisi Sesi Opname" />
                        <x-button-action type="delete" onclick="openModal('modal-hapus-opname-{{ $opname->id_opname }}')" title="Batalkan Sesi Opname" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 text-gray-400 flex items-center justify-center">
                            @include('components.icon', ['name' => 'stok-opname', 'size' => 24])
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800">Belum ada sesi stok opname</h4>
                        <p class="text-xs text-gray-500 max-w-sm mb-2">
                            Lakukan pemeriksaan audit berkala untuk menyelaraskan stok sistem komputer dengan stok fisik suku cadang.
                        </p>
                        <x-button variant="primary" size="sm" icon="plus" text="Mulai Sesi Opname" onclick="openModal('modal-stok-opname')" />
                    </div>
                </td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modals Detail, Edit, dan Hapus Opname --}}
    @foreach($opnames as $opname)
        @php
            // Urutkan item yang memiliki selisih di posisi teratas
            $sortedDetails = $opname->details->sortByDesc(fn($d) => abs((int)$d->stok_fisik - (int)$d->stok_sistem));
            $modalSelisihCount = $opname->details->filter(fn($d) => (int)$d->stok_fisik !== (int)$d->stok_sistem)->count();
            $modalSesuaiCount = $opname->details->count() - $modalSelisihCount;
        @endphp

        {{-- Modal Detail --}}
        <x-modal 
            id="modal-detail-opname-{{ $opname->id_opname }}" 
            title="Detail Penyesuaian #OPN-{{ str_pad($opname->id_opname, 3, '0', STR_PAD_LEFT) }}" 
            subtitle="Pemeriksaan tanggal {{ \Carbon\Carbon::parse($opname->tanggal)->translatedFormat('d F Y, H:i') }}"
            size="xl"
        >
            {{-- Header Ringkasan Sesi --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4 p-3.5 bg-slate-50 border border-slate-100 rounded-xl text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Keterangan Sesi</span>
                    <span class="font-semibold text-gray-800 leading-snug">{{ $opname->keterangan ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Petugas Pencatat</span>
                    <span class="font-semibold text-gray-800">{{ $opname->user->nama ?? $opname->user->name ?? 'Admin' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Status Sesi</span>
                    @if($modalSelisihCount > 0)
                        <span class="inline-flex items-center gap-1 font-semibold text-rose-700 text-xs">
                            @include('components.icon', ['name' => 'alert', 'size' => 12])
                            <span>Ada Selisih ({{ $modalSelisihCount }} item)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 text-xs">
                            @include('components.icon', ['name' => 'check', 'size' => 12])
                            <span>Semua Sesuai</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Mini Ringkasan Metrik --}}
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                    Total: {{ $opname->details->count() }} item
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                    Ada Selisih: {{ $modalSelisihCount }}
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                    Sesuai: {{ $modalSesuaiCount }}
                </span>
            </div>

            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold">
                        <tr>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3 text-center">Stok Sistem</th>
                            <th class="p-3 text-center">Stok Fisik</th>
                            <th class="p-3 text-center">Status Selisih</th>
                            <th class="p-3">Alasan Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sortedDetails as $d)
                            @php 
                                $diff = (int)$d->stok_fisik - (int)$d->stok_sistem; 
                                $isSelisih = $diff !== 0;
                            @endphp
                            <tr class="{{ $isSelisih ? 'bg-rose-50/30 hover:bg-rose-50/50' : 'hover:bg-slate-50/50' }}">
                                <td class="p-3">
                                    <div class="font-semibold text-gray-900">{{ $d->barang->nama_barang ?? 'Barang (Tidak ditemukan)' }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $d->barang->satuan ?? 'Pcs' }} &bull; {{ $d->barang->kategori->nama_kategori ?? '-' }}</div>
                                </td>
                                <td class="p-3 text-center text-gray-700 font-medium">{{ $d->stok_sistem }}</td>
                                <td class="p-3 text-center font-bold text-gray-900">{{ $d->stok_fisik }}</td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    @if($diff > 0)
                                        <x-badge type="success" :text="'Lebih +' . $diff" />
                                    @elseif($diff < 0)
                                        <x-badge type="danger" :text="'Kurang ' . $diff" />
                                    @else
                                        <x-badge type="neutral" text="Sesuai" />
                                    @endif
                                </td>
                                <td class="p-3 text-gray-600 leading-relaxed">{{ $d->alasan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-modal>

        {{-- Modal Edit Sesi Opname (Revisi) --}}
        <x-modal 
            id="modal-edit-opname-{{ $opname->id_opname }}" 
            title="Revisi Sesi Penyesuaian #OPN-{{ str_pad($opname->id_opname, 3, '0', STR_PAD_LEFT) }}" 
            subtitle="Perbarui tanggal, keterangan, atau hitungan stok fisik audit" 
            size="xl"
            :formAction="route('admin.stok-opname.update', $opname->id_opname)"
            formMethod="PUT"
            submitText="Simpan Perubahan"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <x-input 
                    label="Tanggal & Waktu Pemeriksaan" 
                    name="tanggal" 
                    type="datetime-local" 
                    :value="\Carbon\Carbon::parse($opname->tanggal)->format('Y-m-d\TH:i')" 
                    :required="true" 
                />
                <x-input 
                    label="Keterangan Sesi" 
                    name="keterangan" 
                    :value="$opname->keterangan" 
                    placeholder="Contoh: Audit stok akhir bulan" 
                    maxlength="255" 
                />
            </div>

            <div class="border-t border-gray-100 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Item yang Disesuaikan</h5>
                    <span class="text-[11px] text-gray-400">Nilai lama vs Nilai baru</span>
                </div>

                @foreach($opname->details as $index => $detail)
                    @php
                        $currentBarangStok = $detail->barang?->stok ?? 0;
                    @endphp
                    <div class="p-3.5 mb-3 bg-gray-50/70 border border-gray-200/80 rounded-xl space-y-3 edit-item-card"
                         data-sistem="{{ $detail->stok_sistem }}"
                         data-old-fisik="{{ $detail->stok_fisik }}"
                         data-current-stok="{{ $currentBarangStok }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-1.5">
                            <div>
                                <span class="font-bold text-gray-900">{{ $detail->barang->nama_barang ?? 'Barang #' . $detail->id_barang }}</span>
                                <span class="text-gray-500 text-[11px] block">Kategori: {{ $detail->barang->kategori->nama_kategori ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700">
                                    Stok Sistem Awal: {{ $detail->stok_sistem }} {{ $detail->barang->satuan ?? 'Pcs' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    Stok Saat Ini: {{ $currentBarangStok }}
                                </span>
                            </div>
                        </div>

                        <input type="hidden" name="details[{{ $index }}][id_detail]" value="{{ $detail->id_detail }}" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Stok Fisik Baru <span class="text-rose-500">*</span></label>
                                <input type="number" 
                                       min="0" 
                                       inputmode="numeric" 
                                       name="details[{{ $index }}][stok_fisik]" 
                                       value="{{ $detail->stok_fisik }}" 
                                       required
                                       oninput="updateEditLivePreview(this)"
                                       class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 edit-stok-input" />
                                
                                {{-- Live Preview Selisih di Edit Modal --}}
                                <div class="mt-1.5 flex items-center justify-between text-[11px] edit-preview-panel">
                                    <span class="text-gray-500">Status: <strong class="preview-badge font-semibold">Sesuai</strong></span>
                                    <span class="text-gray-400 preview-hint"></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Alasan Selisih <span class="text-rose-500">*</span></label>
                                <input type="text" 
                                       name="details[{{ $index }}][alasan]" 
                                       value="{{ $detail->alasan }}" 
                                       maxlength="500" 
                                       required
                                       placeholder="Penyebab perbedaan stok"
                                       class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                            </div>
                        </div>

                        @if($opname->details->count() === 1)
                            <input type="hidden" name="stok_fisik" value="{{ $detail->stok_fisik }}" />
                            <input type="hidden" name="alasan" value="{{ $detail->alasan }}" />
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl flex items-center gap-2 text-xs text-blue-700 mt-2">
                <x-icon name="info" :size="16" />
                <span>Perubahan stok fisik akan langsung menyinkronkan saldo suku cadang di inventaris secara otomatis.</span>
            </div>
        </x-modal>

        {{-- Modal Konfirmasi Batalkan & Hapus Sesi Opname --}}
        <x-modal
            id="modal-hapus-opname-{{ $opname->id_opname }}"
            title="Konfirmasi Batalkan Sesi Opname"
            subtitle="Tindakan ini akan mengembalikan stok sistem"
            :formAction="route('admin.stok-opname.destroy', $opname->id_opname)"
            formMethod="DELETE"
            submitText="Ya, Batalkan Sesi"
            submitVariant="danger"
            size="md"
        >
            <div class="text-center py-2">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto mb-3.5 shadow-2xs">
                    <x-icon name="alert" :size="26" />
                </div>
                <h4 class="text-sm font-bold text-gray-900 mb-1">Batalkan Sesi Opname Ini?</h4>
                <p class="text-xs text-gray-600 leading-relaxed mb-3">
                    Catatan audit sesi <span class="font-bold text-gray-900">#OPN-{{ str_pad($opname->id_opname, 3, '0', STR_PAD_LEFT) }}</span>
                    akan dihapus dan stok barang yang telah disesuaikan akan dikembalikan ke saldo sebelum penyesuaian.
                </p>

                <div class="text-left bg-gray-50 border border-gray-100 rounded-xl p-3 text-[11px] text-gray-700 space-y-2 mb-2">
                    <div class="font-bold text-gray-800 text-xs border-b border-gray-200/60 pb-1 flex justify-between">
                        <span>Prediksi Pergerakan Stok:</span>
                        <span>Stok Sekarang &rarr; Stok Batal</span>
                    </div>
                    @foreach($opname->details as $d)
                        @php 
                            $diff = (int)$d->stok_fisik - (int)$d->stok_sistem;
                            $currentStok = $d->barang?->stok ?? 0;
                            $restoredStok = $currentStok - $diff;
                            $isNegative = $restoredStok < 0;
                        @endphp
                        <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg {{ $isNegative ? 'bg-rose-100/60 text-rose-800 border border-rose-200' : '' }}">
                            <span class="truncate max-w-[170px] font-medium">{{ $d->barang->nama_barang ?? '-' }}</span>
                            <div class="text-right whitespace-nowrap">
                                <span class="font-bold">{{ $currentStok }} &rarr; {{ $restoredStok }}</span>
                                @if($isNegative)
                                    <span class="block text-[10px] text-rose-600 font-bold">Tidak dapat dibatalkan, stok sudah terpakai transaksi lain</span>
                                @else
                                    <span class="block text-[10px] {{ $diff > 0 ? 'text-rose-600' : ($diff < 0 ? 'text-emerald-600' : 'text-gray-400') }}">
                                        {{ $diff > 0 ? 'Dikurangi ' . $diff : ($diff < 0 ? 'Ditambah ' . abs($diff) : 'Tetap (0)') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-modal>
    @endforeach

    {{-- Modal Mulai Stok Opname (Input) --}}
    <x-modal 
        id="modal-stok-opname" 
        title="Input Penyesuaian Stok Barang" 
        subtitle="Catat hasil hitung fisik. Stok sistem akan disesuaikan otomatis." 
        size="xl"
        :formAction="route('admin.stok-opname.store')"
        formMethod="POST"
        submitText="Simpan Stok Opname"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Tanggal & Waktu Pemeriksaan" name="tanggal" type="datetime-local" :value="old('tanggal', date('Y-m-d\TH:i'))" :required="true" />
            <x-input label="Keterangan Sesi" name="keterangan" :value="old('keterangan')" placeholder="Contoh: Audit akhir bulan / audit rak oli" maxlength="255" />
        </div>

        {{-- Container Batch Items --}}
        <div class="border-t border-gray-100 pt-3">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h5 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Daftar Barang yang Disesuaikan</h5>
                    <p class="text-[11px] text-gray-500">Pilih satu atau tambahkan beberapa barang sekaligus</p>
                </div>
                <button type="button" 
                        onclick="addBatchItemRow()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl text-xs font-semibold transition-colors">
                    @include('components.icon', ['name' => 'plus', 'size' => 14])
                    <span>+ Tambah Barang</span>
                </button>
            </div>

            <div id="batch-items-container" class="space-y-3">
                {{-- Baris pertama (default single item) --}}
                <div class="opname-item-row p-3.5 bg-gray-50/70 border border-gray-200/80 rounded-2xl space-y-3 relative">
                    <button type="button" 
                            onclick="removeBatchItemRow(this)" 
                            class="btn-remove-row absolute top-3 right-3 text-gray-400 hover:text-rose-600 p-1 rounded-lg hidden"
                            title="Hapus baris barang ini">
                        @include('components.icon', ['name' => 'close', 'size' => 16])
                    </button>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        {{-- Dropdown Pilih Barang --}}
                        <div class="sm:col-span-7">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Pilih Barang <span class="text-rose-500">*</span>
                            </label>
                            <select name="items[0][id_barang]" 
                                    required 
                                    onchange="onBarangSelectChange(this)"
                                    class="item-barang-select w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                                <option value="">-- Pilih Suku Cadang --</option>
                                @foreach($barangs as $b)
                                    <option value="{{ $b->id_barang }}" 
                                            data-stok="{{ $b->stok }}" 
                                            data-satuan="{{ $b->satuan ?? 'Pcs' }}" 
                                            data-kategori="{{ $b->kategori->nama_kategori ?? '-' }}"
                                            data-nama="{{ $b->nama_barang }}">
                                        {{ $b->nama_barang }} (Stok Sistem: {{ $b->stok }} {{ $b->satuan ?? 'Pcs' }})
                                    </option>
                                @endforeach
                            </select>

                            {{-- Panel Info Ringkas Setelah Barang Dipilih --}}
                            <div class="panel-barang-info mt-2 p-2 bg-blue-50/60 border border-blue-100 rounded-xl hidden text-[11px] text-blue-900 flex items-center justify-between">
                                <div>
                                    <span class="font-bold barang-info-nama">-</span>
                                    <span class="text-blue-700 block text-[10px]">Kategori: <span class="barang-info-kategori">-</span></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-gray-500 text-[10px] block">Stok Sistem Saat Ini:</span>
                                    <span class="font-extrabold text-blue-800 barang-info-stok">0 Pcs</span>
                                </div>
                            </div>
                        </div>

                        {{-- Stok Fisik Asli --}}
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Stok Fisik Asli <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" 
                                   min="0" 
                                   inputmode="numeric" 
                                   name="items[0][stok_fisik]" 
                                   required 
                                   placeholder="0" 
                                   oninput="onStokFisikInput(this)"
                                   class="item-stok-input w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
                            
                            {{-- Live Preview Selisih Real-Time --}}
                            <div class="panel-selisih-preview mt-1.5 flex flex-col gap-0.5 text-[11px] hidden">
                                <div class="flex items-center gap-1.5">
                                    <span class="badge-selisih inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"></span>
                                </div>
                                <span class="hint-perubahan text-[10px] text-gray-500 leading-tight"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Alasan Selisih dengan Quick Chips & Karakter Counter --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-gray-700">
                                Alasan / Penyebab Selisih <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-gray-400 char-counter">0/500</span>
                        </div>
                        <textarea name="items[0][alasan]" 
                                  rows="2" 
                                  maxlength="500" 
                                  required 
                                  placeholder="Contoh: 1 pcs botol oli bocor saat pemindahan rak"
                                  oninput="updateCharCounter(this)"
                                  class="item-alasan-input w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 resize-none"></textarea>
                        
                        {{-- Quick Chips Alasan Selisih --}}
                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5 text-[10px]">
                            <span class="text-gray-400 font-medium">Isi Cepat:</span>
                            @php
                                $quickChips = [
                                    'Barang pecah/bocor',
                                    'Hilang',
                                    'Salah hitung penerimaan',
                                    'Barang aus/rusak',
                                    'Barang ditemukan',
                                    'Sesuai hasil hitung',
                                ];
                            @endphp
                            @foreach($quickChips as $chip)
                                <button type="button" 
                                        onclick="applyQuickChip(this, '{{ $chip }}')"
                                        class="px-2 py-0.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:text-blue-600 hover:border-blue-300 transition-colors shadow-2xs">
                                    {{ $chip }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl flex items-center gap-2 text-xs text-blue-700">
            <x-icon name="info" :size="16" />
            <span>Stok sistem di inventaris akan otomatis disesuaikan agar sama persis dengan angka stok fisik yang diinputkan.</span>
        </div>
    </x-modal>
@endsection

@push('scripts')
<script>
    // Data master barang untuk live preview
    const barangDataMap = @json($barangDataMap);

    // Update Live Preview pada Input Stok Opname Baru
    function onBarangSelectChange(selectEl) {
        const row = selectEl.closest('.opname-item-row');
        const selectedId = selectEl.value;
        const infoPanel = row.querySelector('.panel-barang-info');
        const stokInput = row.querySelector('.item-stok-input');

        // Pencegahan barang ganda dalam batch
        if (selectedId) {
            const allSelects = document.querySelectorAll('.item-barang-select');
            let duplicateCount = 0;
            allSelects.forEach(s => {
                if (s !== selectEl && s.value === selectedId) duplicateCount++;
            });
            if (duplicateCount > 0) {
                showAlertModal('Barang ini sudah dipilih pada baris lain. Silakan pilih suku cadang yang berbeda.', 'Barang Duplikat', 'warning');
                selectEl.value = '';
                infoPanel.classList.add('hidden');
                return;
            }
        }

        if (selectedId && barangDataMap[selectedId]) {
            const data = barangDataMap[selectedId];
            infoPanel.classList.remove('hidden');
            infoPanel.querySelector('.barang-info-nama').textContent = data.nama_barang;
            infoPanel.querySelector('.barang-info-kategori').textContent = data.kategori;
            infoPanel.querySelector('.barang-info-stok').textContent = data.stok + ' ' + data.satuan;
            
            // Re-kalkulasi selisih jika stok fisik sudah terisi
            onStokFisikInput(stokInput);
        } else {
            infoPanel.classList.add('hidden');
            row.querySelector('.panel-selisih-preview').classList.add('hidden');
        }
    }

    function onStokFisikInput(inputEl) {
        const row = inputEl.closest('.opname-item-row');
        const selectEl = row.querySelector('.item-barang-select');
        const previewPanel = row.querySelector('.panel-selisih-preview');
        const badgeEl = previewPanel.querySelector('.badge-selisih');
        const hintEl = previewPanel.querySelector('.hint-perubahan');

        const selectedId = selectEl ? selectEl.value : null;
        if (!selectedId || !barangDataMap[selectedId] || inputEl.value === '') {
            previewPanel.classList.add('hidden');
            return;
        }

        const sistem = barangDataMap[selectedId].stok;
        const satuan = barangDataMap[selectedId].satuan;
        const fisik = parseInt(inputEl.value, 10);

        if (isNaN(fisik)) {
            previewPanel.classList.add('hidden');
            return;
        }

        previewPanel.classList.remove('hidden');
        const diff = fisik - sistem;

        if (diff > 0) {
            badgeEl.className = 'badge-selisih inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20';
            badgeEl.textContent = 'Lebih +' + diff + ' ' + satuan;
            hintEl.textContent = `Stok sistem akan disesuaikan bertambah dari ${sistem} menjadi ${fisik} ${satuan}.`;
        } else if (diff < 0) {
            badgeEl.className = 'badge-selisih inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 ring-1 ring-rose-600/20';
            badgeEl.textContent = 'Kurang ' + diff + ' ' + satuan;
            hintEl.textContent = `Stok sistem akan disesuaikan berkurang dari ${sistem} menjadi ${fisik} ${satuan}.`;
        } else {
            badgeEl.className = 'badge-selisih inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 ring-1 ring-gray-600/10';
            badgeEl.textContent = 'Sesuai (0 Selisih)';
            hintEl.textContent = `Stok fisik sama dengan stok sistem (${sistem} ${satuan}).`;
        }
    }

    // Quick Chips Alasan
    function applyQuickChip(buttonEl, text) {
        const row = buttonEl.closest('.opname-item-row');
        const textarea = row.querySelector('.item-alasan-input');
        if (textarea) {
            textarea.value = text;
            updateCharCounter(textarea);
            textarea.focus();
        }
    }

    function updateCharCounter(textarea) {
        const row = textarea.closest('.opname-item-row');
        const counter = row.querySelector('.char-counter');
        if (counter) {
            counter.textContent = textarea.value.length + '/500';
        }
    }

    // Tambah & Hapus Baris Batch Input Opname
    let batchRowIndex = 1;
    function addBatchItemRow() {
        const container = document.getElementById('batch-items-container');
        const firstRow = container.querySelector('.opname-item-row');
        if (!firstRow) return;

        const newRow = firstRow.cloneNode(true);
        const idx = batchRowIndex++;

        // Reset field input
        const select = newRow.querySelector('.item-barang-select');
        select.name = `items[${idx}][id_barang]`;
        select.value = '';

        const stokInput = newRow.querySelector('.item-stok-input');
        stokInput.name = `items[${idx}][stok_fisik]`;
        stokInput.value = '';

        const alasanInput = newRow.querySelector('.item-alasan-input');
        alasanInput.name = `items[${idx}][alasan]`;
        alasanInput.value = '';

        newRow.querySelector('.panel-barang-info').classList.add('hidden');
        newRow.querySelector('.panel-selisih-preview').classList.add('hidden');
        newRow.querySelector('.char-counter').textContent = '0/500';

        // Tampilkan tombol hapus baris
        const removeBtn = newRow.querySelector('.btn-remove-row');
        if (removeBtn) removeBtn.classList.remove('hidden');

        container.appendChild(newRow);
        checkRemoveButtons();
    }

    function removeBatchItemRow(btn) {
        const row = btn.closest('.opname-item-row');
        const container = document.getElementById('batch-items-container');
        if (container.querySelectorAll('.opname-item-row').length > 1) {
            row.remove();
            checkRemoveButtons();
        }
    }

    function checkRemoveButtons() {
        const rows = document.querySelectorAll('.opname-item-row');
        rows.forEach(r => {
            const btn = r.querySelector('.btn-remove-row');
            if (btn) {
                if (rows.length > 1) {
                    btn.classList.remove('hidden');
                } else {
                    btn.classList.add('hidden');
                }
            }
        });
    }

    // Live preview pada modal Edit Revisi
    function updateEditLivePreview(inputEl) {
        const card = inputEl.closest('.edit-item-card');
        const sistem = parseInt(card.dataset.sistem, 10);
        const oldFisik = parseInt(card.dataset.oldFisik, 10);
        const currentStok = parseInt(card.dataset.currentStok, 10);
        const newFisik = parseInt(inputEl.value, 10);

        const badge = card.querySelector('.preview-badge');
        const hint = card.querySelector('.preview-hint');

        if (isNaN(newFisik)) {
            badge.textContent = '-';
            hint.textContent = '';
            return;
        }

        const delta = newFisik - oldFisik;
        const targetStok = currentStok + delta;

        const diff = newFisik - sistem;
        if (diff > 0) {
            badge.className = 'preview-badge font-semibold text-emerald-600';
            badge.textContent = 'Lebih +' + diff;
        } else if (diff < 0) {
            badge.className = 'preview-badge font-semibold text-rose-600';
            badge.textContent = 'Kurang ' . diff;
        } else {
            badge.className = 'preview-badge font-semibold text-gray-500';
            badge.textContent = 'Sesuai';
        }

        if (targetStok < 0) {
            hint.innerHTML = `<span class="text-rose-600 font-bold">Peringatan: Stok akhir sistem menjadi ${targetStok} (negatif)!</span>`;
        } else {
            hint.textContent = `Stok sistem akan disesuaikan menjadi ${targetStok}`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi preview di modal edit yang sudah ada
        document.querySelectorAll('.edit-stok-input').forEach(input => {
            updateEditLivePreview(input);
        });
    });
</script>
@endpush
