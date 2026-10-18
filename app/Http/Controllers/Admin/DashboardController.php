<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Metrik Ringkasan
        $pendapatanHariIni = Penjualan::whereDate('tanggal', $today)->where('status', 'Lunas')->sum('total');
        if ($pendapatanHariIni == 0) {
            // Jika tanggal seeder berbeda, tampilkan total terkini
            $pendapatanHariIni = Penjualan::where('status', 'Lunas')->sum('total');
        }

        $totalTransaksiHariIni = Penjualan::whereDate('tanggal', $today)->count();
        if ($totalTransaksiHariIni == 0) {
            $totalTransaksiHariIni = Penjualan::count();
        }

        $produkTerjual = PenjualanDetail::where('tipe', 'barang')->sum('qty');
        $rataRata = Penjualan::where('status', 'Lunas')->avg('total') ?? 0;

        $stokMenipis = Barang::where('stok', '<=', 10)->count();
        $totalBarang = Barang::count();
        $totalJasa = Jasa::count();

        // Status Transaksi Terbaru
        $transaksiTerbaru = Penjualan::with('pelanggan')
            ->latest('tanggal')
            ->take(7)
            ->get();

        return view('pages.admin.dashboard', compact(
            'pendapatanHariIni',
            'totalTransaksiHariIni',
            'produkTerjual',
            'rataRata',
            'stokMenipis',
            'totalBarang',
            'totalJasa',
            'transaksiTerbaru'
        ));
    }
}
