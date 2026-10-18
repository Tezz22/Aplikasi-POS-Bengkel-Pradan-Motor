<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $layanans = Jasa::orderBy('id_jasa', 'desc')->get();

        return view('pages.admin.layanan', compact('layanans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jasa' => ['required', 'string', 'max:120', 'unique:ms_jasa,nama_jasa'],
            'tarif'     => ['required', 'numeric', 'min:0'],
        ], [
            'nama_jasa.required' => 'Nama jasa / layanan wajib diisi.',
            'nama_jasa.max'      => 'Nama jasa maksimal 120 karakter.',
            'nama_jasa.unique'   => 'Nama jasa ini sudah terdaftar.',
            'tarif.required'     => 'Tarif jasa wajib diisi.',
            'tarif.numeric'      => 'Tarif harus berupa angka.',
            'tarif.min'          => 'Tarif tidak boleh bernilai negatif.',
        ]);

        Jasa::create($validated);

        return redirect()->route('admin.layanan')->with('status', 'Layanan jasa baru berhasil ditambahkan!');
    }

    public function update(Request $request, Jasa $jasa)
    {
        $validated = $request->validate([
            'nama_jasa' => ['required', 'string', 'max:120', 'unique:ms_jasa,nama_jasa,' . $jasa->id_jasa . ',id_jasa'],
            'tarif'     => ['required', 'numeric', 'min:0'],
        ], [
            'nama_jasa.required' => 'Nama jasa / layanan wajib diisi.',
            'nama_jasa.max'      => 'Nama jasa maksimal 120 karakter.',
            'nama_jasa.unique'   => 'Nama jasa ini sudah terdaftar.',
            'tarif.required'     => 'Tarif jasa wajib diisi.',
            'tarif.numeric'      => 'Tarif harus berupa angka.',
            'tarif.min'          => 'Tarif tidak boleh bernilai negatif.',
        ]);

        $jasa->update($validated);

        return redirect()->route('admin.layanan')->with('status', 'Data layanan jasa berhasil diperbarui!');
    }

    public function destroy(Jasa $jasa)
    {
        $terpakai = PenjualanDetail::where('id_jasa', $jasa->id_jasa)->exists();
        if ($terpakai) {
            return redirect()->route('admin.layanan')->with('error', "Jasa '{$jasa->nama_jasa}' tidak dapat dihapus karena sudah memiliki riwayat transaksi servis.");
        }

        $jasa->delete();

        return redirect()->route('admin.layanan')->with('status', 'Layanan jasa berhasil dihapus!');
    }
}
