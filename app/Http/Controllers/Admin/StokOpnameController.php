<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\Pembelian;
use App\Models\Supplier;

class StokOpnameController extends Controller
{
    public function index()
    {
        $opnames = Opname::with(['supplier', 'user', 'details.barang'])->latest('tanggal')->get();
        $totalPembelian = Pembelian::count();
        $itemDiperiksa = OpnameDetail::count();
        $itemSelisih = OpnameDetail::where('selisih', '!=', 0)->count();
        $totalSupplier = Supplier::count();
        $kategoris = Kategori::all();

        return view('pages.admin.stok-opname', compact(
            'opnames',
            'totalPembelian',
            'itemDiperiksa',
            'itemSelisih',
            'totalSupplier',
            'kategoris'
        ));
    }
}
