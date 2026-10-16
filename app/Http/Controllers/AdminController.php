<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Supplier;
use App\Models\Jasa;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Opname;
use App\Models\OpnameDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        // Metrik Ringkasan
        $pendapatanHariIni = Penjualan::whereDate('tanggal', $today)->where('status', 'Lunas')->sum('total');
        if ($pendapatanHariIni == 0) {
            // Jika tanggal seeder berbeda, tampilkan total terkini
            $pendapatanHariIni = Penjualan::where('status', 'Lunas')->sum('total');
        }

        $totalTransaksiHariIni = Penjualan::whereDate('tanggal', $today)->count();
        if ($totalTransaksiHariIni == 0) {
            $totalTransaksiHariIni = Penjualan::count();
        }

        $produkTerjual = PenjualanDetail::where('tipe', 'barang')->sum('qty');
        $rataRata = Penjualan::where('status', 'Lunas')->avg('total') ?? 0;

        $stokMenipis = Barang::where('stok', '<=', 10)->count();
        $totalBarang = Barang::count();
        $totalJasa = Jasa::count();

        // Status Transaksi Terbaru
        $transaksiTerbaru = Penjualan::with('pelanggan')
            ->latest('tanggal')
            ->take(7)
            ->get();

        return view('pages.admin.dashboard', compact(
            'pendapatanHariIni',
            'totalTransaksiHariIni',
            'produkTerjual',
            'rataRata',
            'stokMenipis',
            'totalBarang',
            'totalJasa',
            'transaksiTerbaru'
        ));
    }

    public function barang()
    {
        $barangs = Barang::with('kategori')->latest()->get();
        $kategoris = Kategori::all();

        return view('pages.admin.barang', compact('barangs', 'kategoris'));
    }

    public function kategori()
    {
        $kategoris = Kategori::withCount('barang')->latest()->get();

        return view('pages.admin.kategori', compact('kategoris'));
    }

    public function supplier()
    {
        $suppliers = Supplier::latest()->get();

        return view('pages.admin.supplier', compact('suppliers'));
    }

    public function layanan()
    {
        $layanans = Jasa::latest()->get();

        return view('pages.admin.layanan', compact('layanans'));
    }

    public function pelanggan()
    {
        $pelanggans = Pelanggan::withCount('penjualan')->latest()->get();

        return view('pages.admin.pelanggan', compact('pelanggans'));
    }

    public function pengguna()
    {
        $users = User::latest()->get();

        return view('pages.admin.pengguna', compact('users'));
    }

    public function barangMasuk()
    {
        $pembelians = Pembelian::with(['supplier', 'user', 'details.barang'])->latest('tanggal')->get();
        $suppliers = Supplier::all();
        $barangs = Barang::all();

        return view('pages.admin.barang-masuk', compact('pembelians', 'suppliers', 'barangs'));
    }

    public function stokOpname()
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

    public function riwayat()
    {
        $transaksis = Penjualan::with(['pelanggan', 'user', 'details.barang', 'details.jasa'])
            ->latest('tanggal')
            ->get();

        return view('pages.admin.riwayat', compact('transaksis'));
    }

    public function laporan()
    {
        // Rekap transaksi per tanggal dan kasir
        $laporans = Penjualan::with('user')
            ->selectRaw('DATE(tanggal) as tgl, user_id, COUNT(*) as jml_transaksi, SUM(total) as total_penjualan')
            ->groupBy('tgl', 'user_id')
            ->orderBy('tgl', 'desc')
            ->get();

        return view('pages.admin.laporan', compact('laporans'));
    }

    public function profil()
    {
        return view('pages.admin.profil');
    }
}
