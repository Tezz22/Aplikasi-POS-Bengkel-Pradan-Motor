<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('pembelian')->latest()->get();

        return view('pages.admin.supplier', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_supplier' => ['required', 'string', 'max:50', 'unique:ms_supplier,kode_supplier'],
            'nama_supplier' => ['required', 'string', 'max:150'],
            'no_telp'       => ['nullable', 'string', 'max:50'],
            'kontak_person' => ['nullable', 'string', 'max:100'],
            'alamat'        => ['nullable', 'string'],
        ], [
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'kode_supplier.unique'   => 'Kode supplier sudah digunakan.',
            'nama_supplier.required' => 'Nama supplier / perusahaan wajib diisi.',
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.supplier')->with('status', 'Supplier baru berhasil ditambahkan!');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'kode_supplier' => ['required', 'string', 'max:50', 'unique:ms_supplier,kode_supplier,' . $supplier->id],
            'nama_supplier' => ['required', 'string', 'max:150'],
            'no_telp'       => ['nullable', 'string', 'max:50'],
            'kontak_person' => ['nullable', 'string', 'max:100'],
            'alamat'        => ['nullable', 'string'],
        ], [
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'kode_supplier.unique'   => 'Kode supplier sudah digunakan.',
            'nama_supplier.required' => 'Nama supplier / perusahaan wajib diisi.',
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
