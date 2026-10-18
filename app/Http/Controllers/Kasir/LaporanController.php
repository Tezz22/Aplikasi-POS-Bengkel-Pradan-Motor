<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;

class LaporanController extends Controller
{
    public function index()
    {
        $rekapMetode = Penjualan::selectRaw('metode_pembayaran, COUNT(*) as jml_transaksi, SUM(total) as total_nominal')
            ->groupBy('metode_pembayaran')
            ->get();

        $totalSetoran = Penjualan::sum('total');
        $totalTransaksi = Penjualan::count();

        return view('pages.kasir.laporan', compact('rekapMetode', 'totalSetoran', 'totalTransaksi'));
    }
}
