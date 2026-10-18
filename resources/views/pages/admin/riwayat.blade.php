@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <x-page-header 
        title="Riwayat Transaksi" 
        subtitle="Daftar semua transaksi penjualan barang dan jasa bengkel" 
        icon="riwayat" 
    />

    <x-table 
        id="table-riwayat" 
        :searchable="true" 
        searchPlaceholder="Cari no transaksi atau nama pelanggan..." 
        :headers="['NO. TRANSAKSI', 'TANGGAL & WAKTU', 'PELANGGAN', 'TOTAL', 'NOMINAL BAYAR', 'KEMBALIAN', 'KASIR', 'AKSI']"
    >
        @forelse($transaksis as $trx)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $trx->no_transaksi }}</td>
                <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y, H:i') }}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                <td class="py-3.5 px-4 font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-600">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 font-semibold text-emerald-600">Rp {{ number_format($trx->kembalian ?? 0, 0, ',', '.') }}</td>
                <td class="py-3.5 px-4 text-gray-500">{{ $trx->user->nama ?? $trx->user->name ?? 'Kasir' }}</td>
                <td class="py-3.5 px-4 last:pr-6">
                    <div class="flex items-center gap-1">
                        <x-button-action type="info" onclick="openModal('modal-detail-transaksi-{{ $trx->id_transaksi }}')" />
                        <x-button-action type="print" :onclick="'alert(\'Mencetak struk transaksi ' . $trx->no_transaksi . '...\')'" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="py-8 text-center text-gray-400">Belum ada riwayat transaksi</td>
            </tr>
        @endforelse
    </x-table>

    {{-- Modal Detail Transaksi --}}
    @foreach($transaksis as $trx)
        <x-modal 
            id="modal-detail-transaksi-{{ $trx->id_transaksi }}" 
            title="Struk Transaksi #{{ $trx->no_transaksi }}" 
            subtitle="Waktu: {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d F Y, H:i') }}"
            size="lg"
        >
            <div class="mb-4 text-sm text-gray-600 grid grid-cols-2 gap-2">
                <div><span class="font-semibold text-gray-800">Pelanggan:</span> {{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}</div>
                <div><span class="font-semibold text-gray-800">Kasir:</span> {{ $trx->user->nama ?? 'Kasir' }}</div>
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
@endsection
