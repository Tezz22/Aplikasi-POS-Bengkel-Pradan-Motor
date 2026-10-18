<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;

class RiwayatController extends Controller
{
    public function index()
    {
        $transaksis = Penjualan::with(['pelanggan', 'user', 'details'])
            ->latest('tanggal')
            ->get();

        return view('pages.kasir.riwayat', compact('transaksis'));
    }
}
