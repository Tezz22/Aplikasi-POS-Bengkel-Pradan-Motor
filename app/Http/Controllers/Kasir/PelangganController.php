<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::latest()->get();

        return view('pages.kasir.pelanggan', compact('pelanggans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:2', 'max:50'],
            'plat_nomor' => ['nullable', 'string', 'min:3', 'max:12', 'regex:/^[A-Za-z0-9\s]+$/'],
            'no_telepon' => ['nullable', 'string', 'min:8', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
        ], [
            'nama.required' => 'Nama lengkap pelanggan wajib diisi.',
            'nama.min' => 'Nama pelanggan minimal 2 karakter.',
            'nama.max' => 'Nama pelanggan maksimal 50 karakter.',
            'plat_nomor.min' => 'Plat nomor minimal 3 karakter.',
            'plat_nomor.max' => 'Plat nomor maksimal 12 karakter.',
            'plat_nomor.regex' => 'Format plat nomor hanya boleh huruf, angka, dan spasi.',
            'no_telepon.min' => 'Nomor telepon minimal 8 karakter.',
            'no_telepon.max' => 'Nomor telepon maksimal 15 karakter.',
            'no_telepon.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda +.',
        ]);

        $validated['nama'] = trim($validated['nama']);
        if (! empty($validated['plat_nomor'])) {
            $validated['plat_nomor'] = strtoupper(trim($validated['plat_nomor']));
        }
        if (! empty($validated['no_telepon'])) {
            $validated['no_telepon'] = trim($validated['no_telepon']);
        }

        Pelanggan::create($validated);

        return redirect()->route('kasir.pelanggan')->with('status', 'Pelanggan baru berhasil ditambahkan!');
    }
}
