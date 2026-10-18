<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $transaksiKasirCount = Penjualan::where('user_id', $userId)->count();
        $pendapatanKasir = Penjualan::where('user_id', $userId)->where('status', 'Lunas')->sum('total');
        if ($pendapatanKasir == 0) {
            $pendapatanKasir = Penjualan::where('status', 'Lunas')->sum('total');
        }

        $jasaSelesai = PenjualanDetail::where('tipe', 'jasa')->count();
        $stokKritis = Barang::where('stok', '<=', 10)->count();

        $transaksiTerbaru = Penjualan::with('pelanggan')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            })
            ->latest('tanggal')
            ->take(5)
            ->get();

        if ($transaksiTerbaru->isEmpty()) {
            $transaksiTerbaru = Penjualan::with('pelanggan')->latest('tanggal')->take(5)->get();
        }

        return view('pages.kasir.dashboard', compact(
            'transaksiKasirCount',
            'pendapatanKasir',
            'jasaSelesai',
            'stokKritis',
            'transaksiTerbaru'
        ));
    }
}
