<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;

class RiwayatController extends Controller
{
    public function index()
    {
        $transaksis = Penjualan::with(['pelanggan', 'user', 'details.barang', 'details.jasa'])
            ->latest('tanggal')
            ->get();

        return view('pages.admin.riwayat', compact('transaksis'));
    }
}
