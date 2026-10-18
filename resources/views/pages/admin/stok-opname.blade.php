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
            :value="(string)$opnames->count()" 
            sub="sesi audit tercatat" 
            icon="clipboard" 
            type="info" 
        />
        <x-summary-card 
            label="Item Diperiksa" 
            :value="(string)$itemDiperiksa" 
            sub="total pemeriksaan barang" 
            icon="bag" 
            type="success" 
        />
        <x-summary-card 
            label="Item Selisih" 
            :value="(string)$itemSelisih" 
            sub="perbedaan fisik & sistem" 
            icon="alert" 
            type="danger" 
        />
        <x-summary-card 
            label="Total Suku Cadang" 
            :value="(string)\App\Models\Barang::count()" 
            sub="barang aktif terdaftar" 
            icon="tool" 
            type="warning" 
        />
    </div>

    {{-- Table Card --}}
    <x-table 
        id="table-stok-opname" 
        :searchable="true" 
        searchPlaceholder="Cari tanggal, keterangan, atau pencatat...." 
        :headers="['ID SESI', 'TANGGAL & WAKTU', 'ITEM DIPERIKSA', 'STATUS SELISIH', 'KETERANGAN', 'DICATAT OLEH', 'AKSI']"
    >
        @forelse($opnames as $opname)
            @php
                $selisihCount = $opname->details->filter(fn($d) => $d->stok_fisik != $d->stok_sistem)->count();
            @endphp
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-500">#OPN-{{ str_pad($opname->id_opname, 3, '0', STR_PAD_LEFT) }}</td>
                <td class="py-3.5 px-4 text-gray-700">{{ \Carbon\Carbon::parse($opname->tanggal)->translatedFormat('d M Y, H:i') }}</td>
                <td class="py-3.5 px-4 text-gray-700 font-medium">{{ $opname->details->count() }} item</td>
                <td class="py-3.5 px-4">
                    <x-badge 
                        :type="$selisihCount > 0 ? 'danger' : 'success'" 
                        :text="$selisihCount > 0 ? $selisihCount . ' Ada Selisih' : 'Semua Sesuai'" 
                    />
                </td>
                <td class="py-3.5 px-4 text-gray-600">{{ $opname->keterangan ?? '-' }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $opname->user->nama ?? $opname->user->name ?? 'Admin' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="info" onclick="openModal('modal-detail-opname-{{ $opname->id_opname }}')" />
                        
                        <form action="{{ route('admin.stok-opname.destroy', $opname->id_opname) }}" method="POST" onsubmit="return confirm('Hapus catatan audit sesi penyesuaian stok ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button-action type="delete" buttonType="submit" title="Hapus Sesi Opname" />
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data penyesuaian stok</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modals Detail Opname --}}
    @foreach($opnames as $opname)
        <x-modal 
            id="modal-detail-opname-{{ $opname->id_opname }}" 
            title="Detail Penyesuaian #OPN-{{ str_pad($opname->id_opname, 3, '0', STR_PAD_LEFT) }}" 
            subtitle="Pemeriksaan tanggal {{ \Carbon\Carbon::parse($opname->tanggal)->translatedFormat('d F Y, H:i') }}"
            size="lg"
        >
            <div class="mb-4 text-sm text-gray-600">
                <p><span class="font-semibold text-gray-800">Keterangan:</span> {{ $opname->keterangan ?? '-' }}</p>
                <p><span class="font-semibold text-gray-800">Dicatat Oleh:</span> {{ $opname->user->nama ?? 'Admin' }}</p>
            </div>
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold">
                        <tr>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3 text-center">Stok Sistem</th>
                            <th class="p-3 text-center">Stok Fisik</th>
                            <th class="p-3 text-center">Selisih</th>
                            <th class="p-3">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($opname->details as $d)
                            @php $diff = $d->stok_fisik - $d->stok_sistem; @endphp
                            <tr>
                                <td class="p-3 font-semibold text-gray-800">{{ $d->barang->nama_barang ?? 'Barang #' . $d->id_barang }}</td>
                                <td class="p-3 text-center">{{ $d->stok_sistem }}</td>
                                <td class="p-3 text-center font-bold">{{ $d->stok_fisik }}</td>
                                <td class="p-3 text-center font-bold {{ $diff < 0 ? 'text-rose-600' : ($diff > 0 ? 'text-emerald-600' : 'text-gray-400') }}">
                                    {{ $diff > 0 ? '+' . $diff : $diff }}
                                </td>
                                <td class="p-3 text-gray-600">{{ $d->alasan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-modal>
    @endforeach

    {{-- Modal Mulai Stok Opname --}}
    <x-modal 
        id="modal-stok-opname" 
        title="Input Penyesuaian Stok Barang" 
        subtitle="Sesuaikan stok sistem dengan stok fisik asli di etalase / gudang" 
        size="lg"
        :formAction="route('admin.stok-opname.store')"
        formMethod="POST"
        submitText="Simpan & Sesuaikan Stok"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Tanggal & Waktu Pemeriksaan" name="tanggal" type="datetime-local" :value="date('Y-m-d\TH:i')" :required="true" />
            <x-select 
                label="Pilih Barang yang Disesuaikan" 
                name="id_barang" 
                :options="$barangs->pluck('nama_barang', 'id_barang')->toArray()" 
                :required="true" 
            />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Stok Fisik Asli (Hasil Hitung)" name="stok_fisik" type="number" placeholder="0" :required="true" />
            <x-input label="Keterangan Sesi" name="keterangan" placeholder="Contoh: Audit pertengahan bulan" maxlength="255" />
        </div>
        <x-input label="Alasan Perubahan / Penyebab Selisih" name="alasan" placeholder="Contoh: 1 pcs botol retak bocor saat pemindahan rak" :required="true" />
    </x-modal>
@endsection
