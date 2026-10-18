<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Pelanggan;

class TransaksiController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori')->get();
        $jasas = Jasa::where('status', 'Tersedia')->get();
        $pelanggans = Pelanggan::all();

        return view('pages.kasir.transaksi', compact('barangs', 'jasas', 'pelanggans'));
    }
}
