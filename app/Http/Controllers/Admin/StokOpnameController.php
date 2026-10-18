<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    public function index()
    {
        $opnames = Opname::with(['user', 'details.barang'])->orderBy('tanggal', 'desc')->get();
        $barangs = Barang::orderBy('nama_barang')->get();
        $totalPembelian = Pembelian::count();
        $itemDiperiksa = OpnameDetail::count();
        $itemSelisih = OpnameDetail::whereRaw('stok_fisik != stok_sistem')->count();
        $totalSupplier = Supplier::count();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pages.admin.stok-opname', compact(
            'opnames',
            'barangs',
            'totalPembelian',
            'itemDiperiksa',
            'itemSelisih',
            'totalSupplier',
            'kategoris'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'     => ['required'],
            'keterangan'  => ['nullable', 'string', 'max:255'],
            'id_barang'   => ['required', 'exists:ms_barang,id_barang'],
            'stok_fisik'  => ['required', 'integer', 'min:0'],
            'alasan'      => ['required', 'string', 'max:500'],
        ], [
            'tanggal.required'    => 'Tanggal pemeriksaan wajib diisi.',
            'id_barang.required'  => 'Barang wajib dipilih untuk disesuaikan.',
            'id_barang.exists'    => 'Barang yang dipilih tidak valid.',
            'stok_fisik.required' => 'Jumlah stok fisik asli wajib diisi.',
            'stok_fisik.integer'  => 'Stok fisik harus berupa angka bilangan bulat.',
            'stok_fisik.min'      => 'Stok fisik tidak boleh bernilai negatif.',
            'alasan.required'     => 'Alasan atau penjelasan selisih wajib diisi.',
        ]);

        DB::transaction(function () use ($validated) {
            $barang = Barang::findOrFail($validated['id_barang']);
            $stokSistem = $barang->stok;

            $opname = Opname::create([
                'id_user'    => auth()->user()->id_user ?? auth()->id(),
                'tanggal'    => $validated['tanggal'],
                'keterangan' => $validated['keterangan'] ?? 'Penyesuaian stok fisik barang',
            ]);

            OpnameDetail::create([
                'id_opname'   => $opname->id_opname,
                'id_barang'   => $barang->id_barang,
                'stok_sistem' => $stokSistem,
                'stok_fisik'  => $validated['stok_fisik'],
                'alasan'      => $validated['alasan'],
            ]);

            // Sinkronkan stok sistem dengan stok fisik asli
            $barang->update(['stok' => $validated['stok_fisik']]);
        });

        return redirect()->route('admin.stok-opname')->with('status', 'Penyesuaian stok opname berhasil disimpan dan stok barang telah diselaraskan!');
    }

    public function destroy(Opname $opname)
    {
        $opname->delete();

        return redirect()->route('admin.stok-opname')->with('status', 'Catatan sesi penyesuaian stok opname berhasil dihapus.');
    }
}
