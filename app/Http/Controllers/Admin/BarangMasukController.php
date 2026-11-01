<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        // Mendukung input batch multi-item (items array) maupun single item
        if ($request->has('items') && is_array($request->input('items'))) {
            $validated = $request->validate([
                'no_referensi' => ['required', 'string', 'max:25'],
                'tanggal' => ['required', 'date'],
                'id_supplier' => ['required', 'exists:ms_suppliier,id_supplier'],
                'keterangan' => ['nullable', 'string', 'max:255'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.id_barang' => ['required', 'exists:ms_barang,id_barang'],
                'items.*.jumlah' => ['required', 'integer', 'min:1'],
                'items.*.harga_beli' => ['required', 'numeric', 'min:0'],
            ], [
                'no_referensi.required' => 'Nomor referensi barang masuk wajib diisi.',
                'no_referensi.max' => 'Nomor referensi maksimal 25 karakter.',
                'tanggal.required' => 'Tanggal masuk wajib diisi.',
                'id_supplier.required' => 'Supplier wajib dipilih.',
                'id_supplier.exists' => 'Supplier yang dipilih tidak valid.',
                'items.required' => 'Detail barang masuk wajib diisi.',
                'items.min' => 'Minimal terdapat 1 barang masuk.',
                'items.*.id_barang.required' => 'Barang wajib dipilih.',
                'items.*.id_barang.exists' => 'Barang yang dipilih tidak valid.',
                'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
                'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
                'items.*.harga_beli.required' => 'Harga beli satuan wajib diisi.',
                'items.*.harga_beli.min' => 'Harga beli tidak boleh bernilai negatif.',
            ]);

            DB::transaction(function () use ($validated) {
                $userId = auth()->user()->id_user ?? auth()->id();
                $baseRef = $validated['no_referensi'];
                $itemCount = count($validated['items']);

                foreach ($validated['items'] as $index => $item) {
                    $itemRef = $itemCount > 1
                        ? (strlen($baseRef) > 21 ? substr($baseRef, 0, 21) : $baseRef).'-'.($index + 1)
                        : $baseRef;

                    if (Pembelian::where('no_referensi', $itemRef)->exists()) {
                        throw ValidationException::withMessages([
                            'no_referensi' => "Nomor referensi '{$itemRef}' sudah pernah digunakan.",
                        ]);
                    }

                    // Kunci data barang untuk update atomik
                    $barang = Barang::where('id_barang', $item['id_barang'])->lockForUpdate()->firstOrFail();
                    $subtotal = (int) $item['jumlah'] * (int) $item['harga_beli'];

                    Pembelian::create([
                        'no_referensi' => $itemRef,
                        'tanggal' => $validated['tanggal'],
                        'id_supplier' => $validated['id_supplier'],
                        'id_barang' => $barang->id_barang,
                        'id_user' => $userId,
                        'jumlah' => $item['jumlah'],
                        'harga_beli' => $item['harga_beli'],
                        'subtotal' => $subtotal,
                        'keterangan' => $validated['keterangan'] ?? null,
                    ]);

                    // Tambahkan stok barang secara atomik
                    $barang->increment('stok', $item['jumlah']);
                }
            });

            return redirect()->route('admin.barang-masuk')->with('status', 'Penerimaan batch barang masuk berhasil dicatat dan seluruh stok telah diperbarui!');
        }

        // Single item submission
        $validated = $request->validate([
            'no_referensi' => ['required', 'string', 'max:25', 'unique:tr_pembelian,no_referensi'],
            'tanggal' => ['required', 'date'],
            'id_supplier' => ['required', 'exists:ms_suppliier,id_supplier'],
            'id_barang' => ['required', 'exists:ms_barang,id_barang'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'no_referensi.required' => 'Nomor referensi barang masuk wajib diisi.',
            'no_referensi.unique' => 'Nomor referensi ini sudah pernah digunakan.',
            'no_referensi.max' => 'Nomor referensi maksimal 25 karakter.',
            'tanggal.required' => 'Tanggal masuk wajib diisi.',
            'id_supplier.required' => 'Supplier wajib dipilih.',
            'id_supplier.exists' => 'Supplier yang dipilih tidak valid.',
            'id_barang.required' => 'Barang wajib dipilih.',
            'id_barang.exists' => 'Barang yang dipilih tidak valid.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.min' => 'Jumlah barang minimal 1.',
            'harga_beli.required' => 'Harga beli satuan wajib diisi.',
            'harga_beli.min' => 'Harga beli tidak boleh bernilai negatif.',
        ]);

        $subtotal = (int) $validated['jumlah'] * (int) $validated['harga_beli'];

        DB::transaction(function () use ($validated, $subtotal) {
            $barang = Barang::where('id_barang', $validated['id_barang'])->lockForUpdate()->firstOrFail();

            Pembelian::create([
                'no_referensi' => $validated['no_referensi'],
                'tanggal' => $validated['tanggal'],
                'id_supplier' => $validated['id_supplier'],
                'id_barang' => $barang->id_barang,
                'id_user' => auth()->user()->id_user ?? auth()->id(),
                'jumlah' => $validated['jumlah'],
                'harga_beli' => $validated['harga_beli'],
                'subtotal' => $subtotal,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Tambahkan stok barang otomatis
            $barang->increment('stok', $validated['jumlah']);
        });

        return redirect()->route('admin.barang-masuk')->with('status', 'Penerimaan barang masuk berhasil dicatat dan stok telah bertambah!');
    }

    public function destroy(Pembelian $pembelian)
    {
        return DB::transaction(function () use ($pembelian) {
            // Validasi & kurangi kembali stok barang
            if ($pembelian->id_barang && $pembelian->jumlah > 0) {
                $barang = Barang::where('id_barang', $pembelian->id_barang)->lockForUpdate()->first();

                if (! $barang) {
                    return redirect()->route('admin.barang-masuk')->with('error', 'Penghapusan dibatalkan: Data barang tidak ditemukan pada database.');
                }

                // Cegah stok menjadi negatif jika barang sudah terpakai
                if ($barang->stok < $pembelian->jumlah) {
                    return redirect()->route('admin.barang-masuk')->with('error', "Penghapusan ditolak: Stok barang '{$barang->nama_barang}' saat ini ({$barang->stok}) tidak mencukupi untuk dikurangi {$pembelian->jumlah} karena sebagian stok telah digunakan pada transaksi lain.");
                }

                $barang->decrement('stok', $pembelian->jumlah);
            }

            $pembelian->delete();

            return redirect()->route('admin.barang-masuk')->with('status', 'Catatan barang masuk berhasil dibatalkan dan stok telah dikurangi kembali.');
        });
    }
}
