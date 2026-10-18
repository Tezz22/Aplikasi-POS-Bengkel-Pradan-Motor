<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Supplier;

class BarangMasukController extends Controller
{
    public function index()
    {
        $pembelians = Pembelian::with(['supplier', 'user', 'details.barang'])->latest('tanggal')->get();
        $suppliers = Supplier::all();
        $barangs = Barang::all();

        return view('pages.admin.barang-masuk', compact('pembelians', 'suppliers', 'barangs'));
    }
}
