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
            'nama'       => ['required', 'string', 'min:2', 'max:50'],
            'plat_nomor' => ['nullable', 'string', 'min:3', 'max:12', 'regex:/^[A-Za-z0-9\s]+$/'],
            'no_telepon' => ['nullable', 'string', 'min:8', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
        ], [
            'nama.required'    => 'Nama lengkap pelanggan wajib diisi.',
            'nama.min'         => 'Nama pelanggan minimal 2 karakter.',
            'nama.max'         => 'Nama pelanggan maksimal 50 karakter.',
            'plat_nomor.min'   => 'Plat nomor minimal 3 karakter.',
            'plat_nomor.max'   => 'Plat nomor maksimal 12 karakter.',
            'plat_nomor.regex' => 'Format plat nomor hanya boleh huruf, angka, dan spasi.',
            'no_telepon.min'   => 'Nomor telepon minimal 8 karakter.',
            'no_telepon.max'   => 'Nomor telepon maksimal 15 karakter.',
            'no_telepon.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda +.',
        ]);

        $validated['nama'] = trim($validated['nama']);
        if (!empty($validated['plat_nomor'])) {
            $validated['plat_nomor'] = strtoupper(trim($validated['plat_nomor']));
        }
        if (!empty($validated['no_telepon'])) {
            $validated['no_telepon'] = trim($validated['no_telepon']);
        }

        Pelanggan::create($validated);

        return redirect()->route('admin.pelanggan')->with('status', 'Pelanggan baru berhasil ditambahkan!');
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'min:2', 'max:50'],
            'plat_nomor' => ['nullable', 'string', 'min:3', 'max:12', 'regex:/^[A-Za-z0-9\s]+$/'],
            'no_telepon' => ['nullable', 'string', 'min:8', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
        ], [
            'nama.required'    => 'Nama lengkap pelanggan wajib diisi.',
            'nama.min'         => 'Nama pelanggan minimal 2 karakter.',
            'nama.max'         => 'Nama pelanggan maksimal 50 karakter.',
            'plat_nomor.min'   => 'Plat nomor minimal 3 karakter.',
            'plat_nomor.max'   => 'Plat nomor maksimal 12 karakter.',
            'plat_nomor.regex' => 'Format plat nomor hanya boleh huruf, angka, dan spasi.',
            'no_telepon.min'   => 'Nomor telepon minimal 8 karakter.',
            'no_telepon.max'   => 'Nomor telepon maksimal 15 karakter.',
            'no_telepon.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda +.',
        ]);

        $validated['nama'] = trim($validated['nama']);
        if (!empty($validated['plat_nomor'])) {
            $validated['plat_nomor'] = strtoupper(trim($validated['plat_nomor']));
        }
        if (!empty($validated['no_telepon'])) {
            $validated['no_telepon'] = trim($validated['no_telepon']);
        }

        $pelanggan->update($validated);

        return redirect()->route('admin.pelanggan')->with('status', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        // Set id_pelanggan pada riwayat penjualan menjadi null agar data transaksi tetap tersimpan aman
        Penjualan::where('id_pelanggan', $pelanggan->id_pelanggan)->update(['id_pelanggan' => null]);

        $pelanggan->delete();

        return redirect()->route('admin.pelanggan')->with('status', 'Data pelanggan berhasil dihapus!');
    }
}
