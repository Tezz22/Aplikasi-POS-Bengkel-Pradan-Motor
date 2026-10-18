<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('barang')->latest()->get();

        return view('pages.admin.kategori', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kategori' => ['required', 'string', 'max:50', 'unique:ms_kategori,kode_kategori'],
            'nama_kategori' => ['required', 'string', 'max:100'],
            'deskripsi'     => ['nullable', 'string'],
        ], [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique'   => 'Kode kategori sudah digunakan.',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
        ]);

        Kategori::create($validated);

        return redirect()->route('admin.kategori')->with('status', 'Kategori barang baru berhasil ditambahkan!');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'kode_kategori' => ['required', 'string', 'max:50', 'unique:ms_kategori,kode_kategori,' . $kategori->id],
            'nama_kategori' => ['required', 'string', 'max:100'],
            'deskripsi'     => ['nullable', 'string'],
        ], [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique'   => 'Kode kategori sudah digunakan.',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.kategori')->with('status', 'Data kategori berhasil diperbarui!');
    }

    public function destroy(Kategori $kategori)
    {
        $totalBarang = $kategori->barang()->count();
        if ($totalBarang > 0) {
            return redirect()->route('admin.kategori')->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh {$totalBarang} data barang.");
        }

        $kategori->delete();

        return redirect()->route('admin.kategori')->with('status', 'Kategori barang berhasil dihapus!');
    }
}
