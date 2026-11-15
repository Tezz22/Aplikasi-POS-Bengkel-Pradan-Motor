<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Opname;
use App\Models\OpnameDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StokOpnameController extends Controller
{
    /**
     * Menampilkan daftar sesi penyesuaian stok opname beserta metrik ringkasannya.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->get('status', 'semua');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = Opname::with(['user', 'details.barang.kategori'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_opname', 'desc');

        if ($tanggalMulai) {
            $query->whereDate('tanggal', '>=', $tanggalMulai);
        }
        if ($tanggalAkhir) {
            $query->whereDate('tanggal', '<=', $tanggalAkhir);
        }

        if ($statusFilter === 'selisih') {
            $query->whereHas('details', function ($q) {
                $q->whereRaw('stok_fisik != stok_sistem');
            });
        } elseif ($statusFilter === 'sesuai') {
            $query->whereDoesntHave('details', function ($q) {
                $q->whereRaw('stok_fisik != stok_sistem');
            });
        }

        $opnames = $query->get();

        $barangs = Barang::with('kategori')->orderBy('nama_barang')->get();

        $totalSesi = Opname::count();
        $itemDiperiksa = OpnameDetail::count();
        $itemSelisih = OpnameDetail::whereRaw('stok_fisik != stok_sistem')->count();
        $totalBarang = Barang::count();

        $barangOptions = [];
        $barangDataMap = [];
        foreach ($barangs as $b) {
            $satuan = $b->satuan ?? 'Pcs';
            $kategori = $b->kategori->nama_kategori ?? 'Umum';
            $barangOptions[$b->id_barang] = "{$b->nama_barang} (Stok Sistem: {$b->stok} {$satuan})";
            $barangDataMap[$b->id_barang] = [
                'id_barang' => $b->id_barang,
                'nama_barang' => $b->nama_barang,
                'stok' => (int) $b->stok,
                'satuan' => $satuan,
                'kategori' => $kategori,
                'harga_jual' => (int) $b->harga_jual,
            ];
        }

        return view('pages.admin.stok-opname', compact(
            'opnames',
            'barangs',
            'totalSesi',
            'itemDiperiksa',
            'itemSelisih',
            'totalBarang',
            'barangOptions',
            'barangDataMap',
            'statusFilter',
            'tanggalMulai',
            'tanggalAkhir'
        ));
    }

    /**
     * Menyimpan sesi penyesuaian stok opname baru dan menyelaraskan stok barang di sistem.
     */
    public function store(Request $request): RedirectResponse
    {
        // Mendukung input pemeriksaan multi-item (items array)
        if ($request->has('items') && is_array($request->input('items'))) {
            $validated = $request->validate([
                'tanggal' => ['required', 'date'],
                'keterangan' => ['nullable', 'string', 'max:255'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.id_barang' => ['required', 'exists:ms_barang,id_barang'],
                'items.*.stok_fisik' => ['required', 'integer', 'min:0'],
                'items.*.alasan' => ['required', 'string', 'max:500'],
            ], [
                'tanggal.required' => 'Tanggal pemeriksaan wajib diisi.',
                'items.required' => 'Daftar barang pemeriksaan wajib diisi.',
                'items.min' => 'Minimal 1 barang harus diperiksa dalam sesi opname.',
                'items.*.id_barang.required' => 'Barang wajib dipilih.',
                'items.*.id_barang.exists' => 'Barang yang dipilih tidak valid.',
                'items.*.stok_fisik.required' => 'Stok fisik asli wajib diisi.',
                'items.*.stok_fisik.integer' => 'Stok fisik harus berupa bilangan bulat.',
                'items.*.stok_fisik.min' => 'Stok fisik tidak boleh bernilai negatif.',
                'items.*.alasan.required' => 'Alasan atau penjelasan selisih wajib diisi.',
            ]);

            DB::transaction(function () use ($validated): void {
                $opname = Opname::create([
                    'id_user' => auth()->user()->id_user ?? auth()->id(),
                    'tanggal' => $validated['tanggal'],
                    'keterangan' => $validated['keterangan'] ?? 'Penyesuaian stok fisik barang',
                ]);

                foreach ($validated['items'] as $item) {
                    $barang = Barang::where('id_barang', $item['id_barang'])->lockForUpdate()->firstOrFail();
                    $stokSistem = $barang->stok;

                    OpnameDetail::create([
                        'id_opname' => $opname->id_opname,
                        'id_barang' => $barang->id_barang,
                        'stok_sistem' => $stokSistem,
                        'stok_fisik' => $item['stok_fisik'],
                        'alasan' => $item['alasan'],
                    ]);

                    $barang->update(['stok' => $item['stok_fisik']]);
                }
            });

            return redirect()->route('admin.stok-opname')->with('status', 'Penyesuaian stok opname batch berhasil disimpan dan seluruh stok barang telah diselaraskan!');
        }

        // Input penyesuaian single item
        $validated = $request->validate([
            'tanggal' => ['required'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'id_barang' => ['required', 'exists:ms_barang,id_barang'],
            'stok_fisik' => ['required', 'integer', 'min:0'],
            'alasan' => ['required', 'string', 'max:500'],
        ], [
            'tanggal.required' => 'Tanggal pemeriksaan wajib diisi.',
            'id_barang.required' => 'Barang wajib dipilih untuk disesuaikan.',
            'id_barang.exists' => 'Barang yang dipilih tidak valid.',
            'stok_fisik.required' => 'Jumlah stok fisik asli wajib diisi.',
            'stok_fisik.integer' => 'Stok fisik harus berupa angka bilangan bulat.',
            'stok_fisik.min' => 'Stok fisik tidak boleh bernilai negatif.',
            'alasan.required' => 'Alasan atau penjelasan selisih wajib diisi.',
        ]);

        DB::transaction(function () use ($validated): void {
            $barang = Barang::where('id_barang', $validated['id_barang'])->lockForUpdate()->firstOrFail();
            $stokSistem = $barang->stok;

            $opname = Opname::create([
                'id_user' => auth()->user()->id_user ?? auth()->id(),
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'] ?? 'Penyesuaian stok fisik barang',
            ]);

            OpnameDetail::create([
                'id_opname' => $opname->id_opname,
                'id_barang' => $barang->id_barang,
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $validated['stok_fisik'],
                'alasan' => $validated['alasan'],
            ]);

            $barang->update(['stok' => $validated['stok_fisik']]);
        });

        return redirect()->route('admin.stok-opname')->with('status', 'Penyesuaian stok opname berhasil disimpan dan stok barang telah diselaraskan!');
    }

    /**
     * Memperbarui sesi penyesuaian stok opname dan menyesuaikan kembali stok barang terkait.
     */
    public function update(Request $request, Opname $opname): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => ['required'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'stok_fisik' => ['nullable', 'integer', 'min:0'],
            'alasan' => ['nullable', 'string', 'max:500'],
            'details' => ['nullable', 'array'],
            'details.*.id_detail' => ['required', 'exists:ms_penyesuaian_detail,id_detail'],
            'details.*.stok_fisik' => ['required', 'integer', 'min:0'],
            'details.*.alasan' => ['required', 'string', 'max:500'],
        ], [
            'tanggal.required' => 'Tanggal pemeriksaan wajib diisi.',
            'stok_fisik.integer' => 'Stok fisik harus berupa angka bilangan bulat.',
            'stok_fisik.min' => 'Stok fisik tidak boleh bernilai negatif.',
            'details.*.stok_fisik.required' => 'Stok fisik asli wajib diisi.',
            'details.*.stok_fisik.integer' => 'Stok fisik harus berupa angka bilangan bulat.',
            'details.*.stok_fisik.min' => 'Stok fisik tidak boleh bernilai negatif.',
            'details.*.alasan.required' => 'Alasan wajib diisi.',
        ]);

        DB::transaction(function () use ($validated, $opname, $request): void {
            $opname->update([
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'] ?? $opname->keterangan,
            ]);

            if (! empty($validated['details'])) {
                foreach ($validated['details'] as $item) {
                    $detail = OpnameDetail::where('id_detail', $item['id_detail'])
                        ->where('id_opname', $opname->id_opname)
                        ->firstOrFail();

                    $barang = Barang::where('id_barang', $detail->id_barang)->lockForUpdate()->firstOrFail();
                    $oldStokFisik = (int) $detail->stok_fisik;
                    $newStokFisik = (int) $item['stok_fisik'];
                    $delta = $newStokFisik - $oldStokFisik;

                    $newBarangStok = $barang->stok + $delta;
                    if ($newBarangStok < 0) {
                        throw ValidationException::withMessages([
                            'stok_fisik' => "Perubahan stok fisik pada barang '{$barang->nama_barang}' mengakibatkan stok akhir sistem menjadi negatif ({$newBarangStok}). Transaksi lain mungkin telah menggunakan stok ini.",
                        ]);
                    }

                    $barang->update(['stok' => $newBarangStok]);
                    $detail->update([
                        'stok_fisik' => $newStokFisik,
                        'alasan' => $item['alasan'],
                    ]);
                }
            } elseif ($request->filled('stok_fisik')) {
                $detail = $opname->details()->first();
                if ($detail) {
                    $barang = Barang::where('id_barang', $detail->id_barang)->lockForUpdate()->firstOrFail();
                    $oldStokFisik = (int) $detail->stok_fisik;
                    $newStokFisik = (int) $validated['stok_fisik'];
                    $delta = $newStokFisik - $oldStokFisik;

                    $newBarangStok = $barang->stok + $delta;
                    if ($newBarangStok < 0) {
                        throw ValidationException::withMessages([
                            'stok_fisik' => "Perubahan stok fisik pada barang '{$barang->nama_barang}' mengakibatkan stok akhir sistem menjadi negatif ({$newBarangStok}). Transaksi lain mungkin telah menggunakan stok ini.",
                        ]);
                    }

                    $barang->update(['stok' => $newBarangStok]);
                    $detail->update([
                        'stok_fisik' => $newStokFisik,
                        'alasan' => $validated['alasan'] ?? $detail->alasan,
                    ]);
                }
            }
        });

        return redirect()->route('admin.stok-opname')->with('status', 'Data sesi penyesuaian stok opname berhasil diperbarui dan stok telah diselaraskan!');
    }

    /**
     * Membatalkan dan menghapus sesi penyesuaian stok opname, serta mengembalikan stok barang ke kondisi semula.
     */
    public function destroy(Opname $opname): RedirectResponse
    {
        return DB::transaction(function () use ($opname): RedirectResponse {
            $opname->loadMissing('details.barang');

            foreach ($opname->details as $detail) {
                if ($detail->id_barang) {
                    $barang = Barang::where('id_barang', $detail->id_barang)->lockForUpdate()->first();
                    if ($barang) {
                        $adjustment = $detail->stok_fisik - $detail->stok_sistem;
                        $restoredStok = $barang->stok - $adjustment;

                        if ($restoredStok < 0) {
                            return redirect()->route('admin.stok-opname')->with(
                                'error',
                                "Penghapusan ditolak: Stok barang '{$barang->nama_barang}' saat ini ({$barang->stok}) tidak mencukupi untuk dikembalikan ke stok sebelum opname ({$restoredStok}) karena sebagian stok telah digunakan pada transaksi lain."
                            );
                        }

                        $barang->update(['stok' => $restoredStok]);
                    }
                }
            }

            $opname->delete();

            return redirect()->route('admin.stok-opname')->with(
                'status',
                'Catatan sesi penyesuaian stok opname berhasil dibatalkan dan stok barang telah dikembalikan ke kondisi sebelum penyesuaian.'
            );
        });
    }
}
