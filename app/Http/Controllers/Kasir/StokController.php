<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;

class StokController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori')->latest()->get();

        return view('pages.kasir.stok', compact('barangs'));
    }
}
