@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
    <x-page-header 
        title="Dashboard Kasir" 
        subtitle="Selamat bekerja, kelola transaksi pelanggan bengkel hari ini" 
        icon="dashboard"
    >
        <x-slot:actions>
            <x-button 
                variant="primary" 
                icon="transaksi" 
                text="Buka POS Kasir" 
                :href="route('kasir.transaksi')" 
            />
        </x-slot:actions>
    </x-page-header>

    <!-- Kasir Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <x-stat-card 
            label="TRANSAKSI KASIR" 
            :value="$transaksiKasirCount" 
            icon="refresh" 
            color="blue" 
            sub="Total penjualan kasir" 
        />
        <x-stat-card 
            label="PENDAPATAN KASIR" 
            :value="'Rp ' . number_format($pendapatanKasir, 0, ',', '.')" 
            icon="dollar" 
            color="emerald" 
            sub="Total penerimaan" 
        />
        <x-stat-card 
            label="JASA DIKERJAKAN" 
            :value="$jasaSelesai" 
            icon="layanan" 
            color="purple" 
            sub="Layanan servis selesai" 
        />
        <x-stat-card 
            label="STOK MENIPIS" 
            :value="$stokKritis" 
            icon="alert" 
            color="amber" 
            sub="Stok <= 10 pcs" 
        />
    </div>

    <!-- Transaksi Terakhir Kasir -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Transaksi Terakhir Kasir</h3>
                <p class="text-xs text-gray-400 mt-0.5">Penjualan barang & jasa yang diproses pada sistem</p>
            </div>
            <x-button 
                variant="secondary" 
                size="sm" 
                text="Lihat Semua Riwayat" 
                :href="route('kasir.riwayat')" 
            />
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold border-b border-gray-100">
                        <th class="py-2.5 px-3">NO. TRANSAKSI</th>
                        <th class="py-2.5 px-3">WAKTU</th>
                        <th class="py-2.5 px-3">PELANGGAN</th>
                        <th class="py-2.5 px-3">NOMINAL BAYAR</th>
                        <th class="py-2.5 px-3 text-right">TOTAL</th>
                        <th class="py-2.5 px-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($transaksiTerbaru as $trx)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-3 font-bold text-gray-900">{{ $trx->no_transaksi }}</td>
                            <td class="py-3 px-3 text-gray-500">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M, H:i') }}</td>
                            <td class="py-3 px-3 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                            <td class="py-3 px-3 text-gray-600">
                                Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center">
                                <x-button-action type="print" :href="route('kasir.transaksi.receipt', $trx)" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
