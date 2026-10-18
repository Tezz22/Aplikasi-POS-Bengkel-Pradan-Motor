<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\PenjualanDetail;
use App\Models\PembelianDetail;
use App\Models\OpnameDetail;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori')->latest()->get();
        $kategoris = Kategori::all();

        return view('pages.admin.barang', compact('barangs', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', 'unique:ms_barang,kode_barang'],
            'barcode'     => ['nullable', 'string', 'max:100'],
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_id' => ['nullable', 'exists:ms_kategori,id'],
            'satuan'      => ['required', 'string', 'max:50'],
            'harga_beli'  => ['required', 'numeric', 'min:0'],
            'harga_jual'  => ['required', 'numeric', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique'   => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'satuan.required'      => 'Satuan barang wajib diisi.',
            'harga_beli.required'  => 'Harga beli wajib diisi.',
            'harga_beli.numeric'   => 'Harga beli harus berupa angka.',
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
            'kode_barang' => ['required', 'string', 'max:50', 'unique:ms_barang,kode_barang,' . $barang->id],
            'barcode'     => ['nullable', 'string', 'max:100'],
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_id' => ['nullable', 'exists:ms_kategori,id'],
            'satuan'      => ['required', 'string', 'max:50'],
            'harga_beli'  => ['required', 'numeric', 'min:0'],
            'harga_jual'  => ['required', 'numeric', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique'   => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'satuan.required'      => 'Satuan barang wajib diisi.',
            'harga_beli.required'  => 'Harga beli wajib diisi.',
            'harga_beli.numeric'   => 'Harga beli harus berupa angka.',
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
        $terpakaiPenjualan = PenjualanDetail::where('barang_id', $barang->id)->exists();
        $terpakaiPembelian = PembelianDetail::where('barang_id', $barang->id)->exists();
        $terpakaiOpname    = OpnameDetail::where('barang_id', $barang->id)->exists();

        if ($terpakaiPenjualan || $terpakaiPembelian || $terpakaiOpname) {
            return redirect()->route('admin.barang')->with('error', "Barang '{$barang->nama_barang}' tidak dapat dihapus karena sudah memiliki riwayat transaksi/stok.");
        }

        $barang->delete();

        return redirect()->route('admin.barang')->with('status', 'Barang berhasil dihapus!');
    }
}
