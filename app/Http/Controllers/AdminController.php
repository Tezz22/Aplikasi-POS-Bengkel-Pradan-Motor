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

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        // Metrik Ringkasan
        $pendapatanHariIni = Penjualan::whereDate('tanggal', $today)->sum('total');
        if ($pendapatanHariIni == 0) {
            $pendapatanHariIni = Penjualan::sum('total');
        }

        $totalTransaksiHariIni = Penjualan::whereDate('tanggal', $today)->count();
        if ($totalTransaksiHariIni == 0) {
            $totalTransaksiHariIni = Penjualan::count();
        }

        $produkTerjual = PenjualanDetail::where('jenis_item', 'barang')->sum('qty');
        $rataRata = Penjualan::avg('total') ?? 0;

        $stokMenipis = Barang::where('stok', '<=', 10)->count();
        $totalBarang = Barang::count();
        $totalJasa = Jasa::count();

        // Status Transaksi Terbaru
        $transaksiTerbaru = Penjualan::with('pelanggan')
            ->orderBy('tanggal', 'desc')
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
        $barangs = Barang::with('kategori')->orderBy('id_barang', 'desc')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pages.admin.barang', compact('barangs', 'kategoris'));
    }

    public function kategori()
    {
        $kategoris = Kategori::withCount('barang')->orderBy('id_kategori', 'desc')->get();

        return view('pages.admin.kategori', compact('kategoris'));
    }

    public function supplier()
    {
        $suppliers = Supplier::orderBy('id_supplier', 'desc')->get();

        return view('pages.admin.supplier', compact('suppliers'));
    }

    public function layanan()
    {
        $layanans = Jasa::orderBy('id_jasa', 'desc')->get();

        return view('pages.admin.layanan', compact('layanans'));
    }

    public function pelanggan()
    {
        $pelanggans = Pelanggan::withCount('penjualan')->orderBy('id_pelanggan', 'desc')->get();

        return view('pages.admin.pelanggan', compact('pelanggans'));
    }

    public function pengguna()
    {
        $users = User::orderBy('id_user', 'desc')->get();

        return view('pages.admin.pengguna', compact('users'));
    }

    public function barangMasuk()
    {
        $pembelians = Pembelian::with(['supplier', 'user', 'barang'])->orderBy('tanggal', 'desc')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $barangs = Barang::orderBy('nama_barang')->get();

        return view('pages.admin.barang-masuk', compact('pembelians', 'suppliers', 'barangs'));
    }

    public function stokOpname()
    {
        $opnames = Opname::with(['user', 'details.barang'])->orderBy('tanggal', 'desc')->get();
        $totalPembelian = Pembelian::count();
        $itemDiperiksa = OpnameDetail::count();
        $itemSelisih = OpnameDetail::whereRaw('stok_fisik != stok_sistem')->count();
        $totalSupplier = Supplier::count();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

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
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pages.admin.riwayat', compact('transaksis'));
    }

    public function laporan()
    {
        // Rekap transaksi per tanggal dan kasir
        $laporans = Penjualan::with('user')
            ->selectRaw('DATE(tanggal) as tgl, id_user, COUNT(*) as jml_transaksi, SUM(total) as total_penjualan')
            ->groupBy('tgl', 'id_user')
            ->orderBy('tgl', 'desc')
            ->get();

        return view('pages.admin.laporan', compact('laporans'));
    }

    public function profil()
    {
        return view('pages.admin.profil');
    }
}
