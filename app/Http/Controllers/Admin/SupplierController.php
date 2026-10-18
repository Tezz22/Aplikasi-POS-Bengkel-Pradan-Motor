<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('pembelian')->orderBy('id_supplier', 'desc')->get();

        return view('pages.admin.supplier', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:50'],
            'no_telepon'   => ['nullable', 'string', 'max:15'],
            'alamat'        => ['nullable', 'string', 'max:255'],
        ], [
            'nama_supplier.required' => 'Nama supplier / distributor wajib diisi.',
            'nama_supplier.max'      => 'Nama supplier maksimal 50 karakter.',
            'no_telepon.max'         => 'Nomor telepon maksimal 15 karakter.',
            'alamat.max'             => 'Alamat maksimal 255 karakter.',
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.supplier')->with('status', 'Supplier baru berhasil ditambahkan!');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:50'],
            'no_telepon'   => ['nullable', 'string', 'max:15'],
            'alamat'        => ['nullable', 'string', 'max:255'],
        ], [
            'nama_supplier.required' => 'Nama supplier / distributor wajib diisi.',
            'nama_supplier.max'      => 'Nama supplier maksimal 50 karakter.',
            'no_telepon.max'         => 'Nomor telepon maksimal 15 karakter.',
            'alamat.max'             => 'Alamat maksimal 255 karakter.',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.supplier')->with('status', 'Data supplier berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        $totalPembelian = $supplier->pembelian()->count();
        if ($totalPembelian > 0) {
            return redirect()->route('admin.supplier')->with('error', "Supplier '{$supplier->nama_supplier}' tidak dapat dihapus karena masih memiliki {$totalPembelian} riwayat pembelian/barang masuk.");
        }

        $supplier->delete();

        return redirect()->route('admin.supplier')->with('status', 'Data supplier berhasil dihapus!');
    }
}
