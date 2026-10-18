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

        $transaksiKasirCount = Penjualan::where('id_user', $userId)->count();
        $pendapatanKasir = Penjualan::where('id_user', $userId)->sum('total');
        if ($pendapatanKasir == 0) {
            $pendapatanKasir = Penjualan::sum('total');
        }

        $jasaSelesai = PenjualanDetail::where('jenis_item', 'jasa')->count();
        $stokKritis = Barang::where('stok', '<=', 10)->count();

        $transaksiTerbaru = Penjualan::with('pelanggan')
            ->where(function ($q) use ($userId) {
                $q->where('id_user', $userId)->orWhereNull('id_user');
            })
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        if ($transaksiTerbaru->isEmpty()) {
            $transaksiTerbaru = Penjualan::with('pelanggan')->orderBy('tanggal', 'desc')->take(5)->get();
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
        $barangs = Barang::with('kategori')->orderBy('nama_barang')->get();
        $jasas = Jasa::orderBy('nama_jasa')->get();
        $pelanggans = Pelanggan::orderBy('nama')->get();

        return view('pages.kasir.transaksi', compact('barangs', 'jasas', 'pelanggans'));
    }

    public function pelanggan()
    {
        $pelanggans = Pelanggan::orderBy('id_pelanggan', 'desc')->get();

        return view('pages.kasir.pelanggan', compact('pelanggans'));
    }

    public function stok()
    {
        $barangs = Barang::with('kategori')->orderBy('id_barang', 'desc')->get();

        return view('pages.kasir.stok', compact('barangs'));
    }

    public function riwayat()
    {
        $transaksis = Penjualan::with(['pelanggan', 'user', 'details'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pages.kasir.riwayat', compact('transaksis'));
    }

    public function laporan()
    {
        $rekapHarian = Penjualan::selectRaw('DATE(tanggal) as tgl, COUNT(*) as jml_transaksi, SUM(total) as total_nominal')
            ->groupBy('tgl')
            ->orderBy('tgl', 'desc')
            ->get();

        $totalSetoran = Penjualan::sum('total');
        $totalTransaksi = Penjualan::count();

        return view('pages.kasir.laporan', compact('rekapHarian', 'totalSetoran', 'totalTransaksi'));
    }

    public function profil()
    {
        return view('pages.kasir.profil');
    }
}
