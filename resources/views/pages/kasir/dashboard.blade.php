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
            label="TOTAL TRANSAKSI SAYA" 
            :value="$transaksiKasirCount" 
            icon="refresh" 
            color="blue" 
            sub="Hari ini" 
        />
        <x-stat-card 
            label="PENDAPATAN KASIR" 
            :value="'Rp ' . number_format($pendapatanKasir, 0, ',', '.')" 
            icon="dollar" 
            color="emerald" 
            sub="Tunai & QRIS" 
        />
        <x-stat-card 
            label="JASA SELESAI" 
            :value="$jasaSelesai" 
            icon="layanan" 
            color="purple" 
            sub="Unit motor dikerjakan" 
        />
        <x-stat-card 
            label="STOK KRITIS" 
            :value="$stokKritis" 
            icon="alert" 
            color="amber" 
            sub="Segera infokan admin" 
        />
    </div>

    <!-- Transaksi Terakhir Kasir -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Transaksi Terakhir Kasir</h3>
                <p class="text-xs text-gray-400 mt-0.5">Penjualan barang & jasa yang Anda proses hari ini</p>
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
                        <th class="py-2.5 px-3">NO. NOTA</th>
                        <th class="py-2.5 px-3">WAKTU</th>
                        <th class="py-2.5 px-3">PELANGGAN</th>
                        <th class="py-2.5 px-3">METODE</th>
                        <th class="py-2.5 px-3 text-right">TOTAL</th>
                        <th class="py-2.5 px-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($transaksiTerbaru as $trx)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-3 font-bold text-gray-900">{{ $trx->no_faktur }}</td>
                            <td class="py-3 px-3 text-gray-500">{{ \Carbon\Carbon::parse($trx->tanggal)->format('H:i') }}</td>
                            <td class="py-3 px-3 font-semibold text-gray-900">{{ $trx->pelanggan->nama ?? ($trx->nama_pelanggan_non_member ?? 'Umum') }}</td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $trx->metode_pembayaran === 'Tunai' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center">
                                <x-button-action type="print" onclick="alert('Cetak struk {{ $trx->no_faktur }}')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada transaksi hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
