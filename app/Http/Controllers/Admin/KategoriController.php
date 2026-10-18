<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('barang')->orderBy('id_kategori', 'desc')->get();

        return view('pages.admin.kategori', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:25', 'unique:ms_kategori,nama_kategori'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max'      => 'Nama kategori maksimal 25 karakter.',
            'nama_kategori.unique'   => 'Nama kategori sudah ada.',
        ]);

        Kategori::create($validated);

        return redirect()->route('admin.kategori')->with('status', 'Kategori barang baru berhasil ditambahkan!');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:25', 'unique:ms_kategori,nama_kategori,' . $kategori->id_kategori . ',id_kategori'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max'      => 'Nama kategori maksimal 25 karakter.',
            'nama_kategori.unique'   => 'Nama kategori sudah digunakan.',
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
