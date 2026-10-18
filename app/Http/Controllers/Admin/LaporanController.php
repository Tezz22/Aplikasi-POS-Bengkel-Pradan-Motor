<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;

class LaporanController extends Controller
{
    public function index()
    {
        // Rekap transaksi per tanggal dan kasir
        $laporans = Penjualan::with('user')
            ->selectRaw('DATE(tanggal) as tgl, id_user, COUNT(*) as jml_transaksi, SUM(total) as total_penjualan')
            ->groupBy('tgl', 'id_user')
            ->orderBy('tgl', 'desc')
            ->get();

        return view('pages.admin.laporan', compact('laporans'));
    }
}
