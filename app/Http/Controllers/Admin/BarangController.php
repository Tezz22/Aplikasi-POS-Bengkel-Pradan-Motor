<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\PenjualanDetail;
use App\Models\Pembelian;
use App\Models\OpnameDetail;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori')->orderBy('id_barang', 'desc')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pages.admin.barang', compact('barangs', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kategori' => ['required', 'exists:ms_kategori,id_kategori'],
            'nama_barang' => ['required', 'string', 'max:30'],
            'satuan'      => ['required', 'string', 'max:20'],
            'harga_jual'  => ['required', 'numeric', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
        ], [
            'id_kategori.required' => 'Kategori barang wajib dipilih.',
            'id_kategori.exists'   => 'Kategori tidak valid.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max'      => 'Nama barang maksimal 30 karakter.',
            'satuan.required'      => 'Satuan barang wajib diisi.',
            'satuan.max'           => 'Satuan maksimal 20 karakter.',
            'harga_jual.required'  => 'Harga jual wajib diisi.',
            'harga_jual.numeric'   => 'Harga jual harus berupa angka.',
            'stok.required'        => 'Stok awal wajib diisi.',
            'stok.integer'         => 'Stok harus berupa bilangan bulat.',
        ]);

        Barang::create($validated);

        return redirect()->route('admin.barang')->with('status', 'Barang baru berhasil ditambahkan!');
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'id_kategori' => ['required', 'exists:ms_kategori,id_kategori'],
            'nama_barang' => ['required', 'string', 'max:30'],
            'satuan'      => ['required', 'string', 'max:20'],
            'harga_jual'  => ['required', 'numeric', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
        ], [
            'id_kategori.required' => 'Kategori barang wajib dipilih.',
            'id_kategori.exists'   => 'Kategori tidak valid.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max'      => 'Nama barang maksimal 30 karakter.',
            'satuan.required'      => 'Satuan barang wajib diisi.',
            'satuan.max'           => 'Satuan maksimal 20 karakter.',
            'harga_jual.required'  => 'Harga jual wajib diisi.',
            'harga_jual.numeric'   => 'Harga jual harus berupa angka.',
            'stok.required'        => 'Stok barang wajib diisi.',
            'stok.integer'         => 'Stok harus berupa bilangan bulat.',
        ]);

        $barang->update($validated);

        return redirect()->route('admin.barang')->with('status', 'Data barang berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        // Periksa apakah barang sudah pernah ada di riwayat penjualan, pembelian, atau stok opname
        $terpakaiPenjualan = PenjualanDetail::where('id_barang', $barang->id_barang)->exists();
        $terpakaiPembelian = Pembelian::where('id_barang', $barang->id_barang)->exists();
        $terpakaiOpname    = OpnameDetail::where('id_barang', $barang->id_barang)->exists();

        if ($terpakaiPenjualan || $terpakaiPembelian || $terpakaiOpname) {
            return redirect()->route('admin.barang')->with('error', "Barang '{$barang->nama_barang}' tidak dapat dihapus karena sudah memiliki riwayat transaksi/stok.");
        }

        $barang->delete();

        return redirect()->route('admin.barang')->with('status', 'Barang berhasil dihapus!');
    }
}
