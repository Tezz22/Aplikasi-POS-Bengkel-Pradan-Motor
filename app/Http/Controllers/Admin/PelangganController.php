<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::withCount('penjualan')->latest()->get();

        return view('pages.admin.pelanggan', compact('pelanggans'));
    }
}
