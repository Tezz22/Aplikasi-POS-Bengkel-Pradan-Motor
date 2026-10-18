<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::withCount('penjualan')->orderBy('id_pelanggan', 'desc')->get();

        return view('pages.admin.pelanggan', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:50'],
            'plat_nomor' => ['nullable', 'string', 'max:12'],
            'no_telepon' => ['nullable', 'string', 'max:15'],
        ], [
            'nama.required'  => 'Nama lengkap pelanggan wajib diisi.',
            'nama.max'       => 'Nama pelanggan maksimal 50 karakter.',
            'plat_nomor.max' => 'Plat nomor maksimal 12 karakter.',
            'no_telepon.max' => 'Nomor telepon maksimal 15 karakter.',
        ]);

        Pelanggan::create($validated);

        return redirect()->route('admin.pelanggan')->with('status', 'Pelanggan baru berhasil ditambahkan!');
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:50'],
            'plat_nomor' => ['nullable', 'string', 'max:12'],
            'no_telepon' => ['nullable', 'string', 'max:15'],
        ], [
            'nama.required'  => 'Nama lengkap pelanggan wajib diisi.',
            'nama.max'       => 'Nama pelanggan maksimal 50 karakter.',
            'plat_nomor.max' => 'Plat nomor maksimal 12 karakter.',
            'no_telepon.max' => 'Nomor telepon maksimal 15 karakter.',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('admin.pelanggan')->with('status', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $totalPenjualan = Penjualan::where('id_pelanggan', $pelanggan->id_pelanggan)->count();
        if ($totalPenjualan > 0) {
            return redirect()->route('admin.pelanggan')->with('error', "Pelanggan '{$pelanggan->nama}' tidak dapat dihapus karena memiliki {$totalPenjualan} riwayat transaksi.");
        }

        $pelanggan->delete();

        return redirect()->route('admin.pelanggan')->with('status', 'Data pelanggan berhasil dihapus!');
    }
}
