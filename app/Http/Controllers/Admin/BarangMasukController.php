<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index()
    {
        $pembelians = Pembelian::with(['supplier', 'user', 'barang'])->orderBy('tanggal', 'desc')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $barangs = Barang::orderBy('nama_barang')->get();

        return view('pages.admin.barang-masuk', compact('pembelians', 'suppliers', 'barangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_referensi' => ['required', 'string', 'max:25', 'unique:tr_pembelian,no_referensi'],
            'tanggal'      => ['required', 'date'],
            'id_supplier'  => ['required', 'exists:ms_suppliier,id_supplier'],
            'id_barang'    => ['required', 'exists:ms_barang,id_barang'],
            'jumlah'       => ['required', 'integer', 'min:1'],
            'harga_beli'   => ['required', 'numeric', 'min:0'],
            'keterangan'   => ['nullable', 'string', 'max:255'],
        ], [
            'no_referensi.required' => 'Nomor referensi barang masuk wajib diisi.',
            'no_referensi.unique'   => 'Nomor referensi ini sudah pernah digunakan.',
            'no_referensi.max'      => 'Nomor referensi maksimal 25 karakter.',
            'tanggal.required'      => 'Tanggal masuk wajib diisi.',
            'id_supplier.required'  => 'Supplier wajib dipilih.',
            'id_supplier.exists'    => 'Supplier yang dipilih tidak valid.',
            'id_barang.required'    => 'Barang wajib dipilih.',
            'id_barang.exists'      => 'Barang yang dipilih tidak valid.',
            'jumlah.required'       => 'Jumlah barang wajib diisi.',
            'jumlah.min'            => 'Jumlah barang minimal 1.',
            'harga_beli.required'   => 'Harga beli satuan wajib diisi.',
            'harga_beli.min'        => 'Harga beli tidak boleh negatif.',
        ]);

        $subtotal = $validated['jumlah'] * $validated['harga_beli'];

        DB::transaction(function () use ($validated, $subtotal) {
            Pembelian::create([
                'no_referensi' => $validated['no_referensi'],
                'tanggal'      => $validated['tanggal'],
                'id_supplier'  => $validated['id_supplier'],
                'id_barang'    => $validated['id_barang'],
                'id_user'      => auth()->user()->id_user ?? auth()->id(),
                'jumlah'       => $validated['jumlah'],
                'harga_beli'   => $validated['harga_beli'],
                'subtotal'     => $subtotal,
                'keterangan'   => $validated['keterangan'] ?? null,
            ]);

            // Tambahkan stok barang otomatis
            Barang::where('id_barang', $validated['id_barang'])->increment('stok', $validated['jumlah']);
        });

        return redirect()->route('admin.barang-masuk')->with('status', 'Penerimaan barang masuk berhasil dicatat dan stok telah bertambah!');
    }

    public function destroy(Pembelian $pembelian)
    {
        DB::transaction(function () use ($pembelian) {
            // Kurangi kembali stok barang
            if ($pembelian->id_barang && $pembelian->jumlah > 0) {
                Barang::where('id_barang', $pembelian->id_barang)->decrement('stok', $pembelian->jumlah);
            }

            $pembelian->delete();
        });

        return redirect()->route('admin.barang-masuk')->with('status', 'Catatan barang masuk berhasil dibatalkan dan stok dikembalikan.');
    }
}
