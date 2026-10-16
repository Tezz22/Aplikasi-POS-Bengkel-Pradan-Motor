<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    public function dashboard()
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

    public function transaksi()
    {
        $barangs = Barang::with('kategori')->get();
        $jasas = Jasa::where('status', 'Tersedia')->get();
        $pelanggans = Pelanggan::all();

        return view('pages.kasir.transaksi', compact('barangs', 'jasas', 'pelanggans'));
    }

    public function pelanggan()
    {
        $pelanggans = Pelanggan::latest()->get();

        return view('pages.kasir.pelanggan', compact('pelanggans'));
    }

    public function stok()
    {
        $barangs = Barang::with('kategori')->latest()->get();

        return view('pages.kasir.stok', compact('barangs'));
    }

    public function riwayat()
    {
        $transaksis = Penjualan::with(['pelanggan', 'user', 'details'])
            ->latest('tanggal')
            ->get();

        return view('pages.kasir.riwayat', compact('transaksis'));
    }

    public function laporan()
    {
        $rekapMetode = Penjualan::selectRaw('metode_pembayaran, COUNT(*) as jml_transaksi, SUM(total) as total_nominal')
            ->groupBy('metode_pembayaran')
            ->get();

        $totalSetoran = Penjualan::sum('total');
        $totalTransaksi = Penjualan::count();

        return view('pages.kasir.laporan', compact('rekapMetode', 'totalSetoran', 'totalTransaksi'));
    }

    public function profil()
    {
        return view('pages.kasir.profil');
    }
}
