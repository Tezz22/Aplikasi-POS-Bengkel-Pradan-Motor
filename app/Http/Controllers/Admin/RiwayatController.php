<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function index()
    {
        $transaksis = Penjualan::with(['pelanggan', 'user', 'details.barang', 'details.jasa'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pages.admin.riwayat', compact('transaksis'));
    }

    public function destroy(Penjualan $penjualan)
    {
        DB::transaction(function () use ($penjualan) {
            // Kembalikan stok suku cadang yang dibeli pada transaksi ini
            foreach ($penjualan->details as $d) {
                if ($d->jenis_item === 'barang' && $d->id_barang && $d->qty > 0) {
                    Barang::where('id_barang', $d->id_barang)->increment('stok', $d->qty);
                }
            }

            $penjualan->delete();
        });

        return redirect()->route('admin.riwayat')->with('status', 'Transaksi berhasil dibatalkan dan stok suku cadang telah dikembalikan.');
    }
}
