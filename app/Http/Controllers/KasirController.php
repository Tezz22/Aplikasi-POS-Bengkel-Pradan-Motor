<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KasirController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $transaksiKasirCount = Penjualan::where('id_user', $userId)->count();
        $pendapatanKasir = Penjualan::where('id_user', $userId)->sum('total');
        if ($pendapatanKasir == 0) {
            $pendapatanKasir = Penjualan::sum('total');
        }

        $jasaSelesai = PenjualanDetail::where('jenis_item', 'jasa')->count();
        $stokKritis = Barang::where('stok', '<=', 10)->count();

        $transaksiTerbaru = Penjualan::with('pelanggan')
            ->where(function ($q) use ($userId) {
                $q->where('id_user', $userId)->orWhereNull('id_user');
            })
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        if ($transaksiTerbaru->isEmpty()) {
            $transaksiTerbaru = Penjualan::with('pelanggan')->orderBy('tanggal', 'desc')->take(5)->get();
        }

        return view('pages.kasir.dashboard', compact(
            'transaksiKasirCount',
            'pendapatanKasir',
            'jasaSelesai',
            'stokKritis',
            'transaksiTerbaru'
        ));
    }

    public function transaksi()
    {
        $barangs = Barang::with('kategori')->orderBy('nama_barang')->get();
        $jasas = Jasa::orderBy('nama_jasa')->get();
        $pelanggans = Pelanggan::orderBy('nama')->get();

        return view('pages.kasir.transaksi', compact('barangs', 'jasas', 'pelanggans'));
    }

    public function storeTransaksi(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => ['nullable', 'exists:ms_pelanggan,id_pelanggan'],
            'nominal_bayar' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.jenis' => ['required', 'in:barang,jasa'],
            'items.*.id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Keranjang belanja tidak boleh kosong.',
            'items.min' => 'Pilih minimal 1 barang atau jasa servis.',
            'nominal_bayar.required' => 'Nominal bayar wajib diisi.',
            'nominal_bayar.numeric' => 'Nominal bayar harus berupa angka.',
            'nominal_bayar.min' => 'Nominal bayar tidak boleh bernilai negatif.',
        ]);

        return DB::transaction(function () use ($validated) {
            $total = 0;
            $itemsToProcess = [];

            // 1. Kunci dan validasi ketersediaan stok seluruh barang sebelum memproses transaksi
            foreach ($validated['items'] as $item) {
                if ($item['jenis'] === 'barang') {
                    $barang = Barang::where('id_barang', $item['id'])->lockForUpdate()->first();

                    if (! $barang) {
                        throw ValidationException::withMessages([
                            'items' => "Suku cadang dengan ID #{$item['id']} tidak ditemukan.",
                        ]);
                    }

                    if ($barang->stok < $item['qty']) {
                        throw ValidationException::withMessages([
                            'items' => "Stok untuk barang '{$barang->nama_barang}' tidak mencukupi. Tersedia: {$barang->stok}, diminta: {$item['qty']}.",
                        ]);
                    }

                    $subtotal = $barang->harga_jual * $item['qty'];
                    $total += $subtotal;

                    $itemsToProcess[] = [
                        'model' => $barang,
                        'jenis_item' => 'barang',
                        'id_barang' => $barang->id_barang,
                        'id_jasa' => null,
                        'nama_item' => $barang->nama_barang,
                        'harga_satuan' => $barang->harga_jual,
                        'qty' => $item['qty'],
                        'subtotal' => $subtotal,
                    ];
                } else {
                    $jasa = Jasa::find($item['id']);

                    if (! $jasa) {
                        throw ValidationException::withMessages([
                            'items' => "Layanan jasa dengan ID #{$item['id']} tidak ditemukan.",
                        ]);
                    }

                    $subtotal = $jasa->tarif * $item['qty'];
                    $total += $subtotal;

                    $itemsToProcess[] = [
                        'model' => $jasa,
                        'jenis_item' => 'jasa',
                        'id_barang' => null,
                        'id_jasa' => $jasa->id_jasa,
                        'nama_item' => $jasa->nama_jasa,
                        'harga_satuan' => $jasa->tarif,
                        'qty' => $item['qty'],
                        'subtotal' => $subtotal,
                    ];
                }
            }

            // 2. Validasi kecukupan nominal pembayaran
            if ($validated['nominal_bayar'] < $total) {
                throw ValidationException::withMessages([
                    'nominal_bayar' => 'Nominal bayar (Rp '.number_format($validated['nominal_bayar'], 0, ',', '.').') kurang dari total transaksi (Rp '.number_format($total, 0, ',', '.').').',
                ]);
            }

            $kembalian = $validated['nominal_bayar'] - $total;

            // 3. Generate No. Transaksi Unik
            $noTransaksi = 'TRX-'.date('Ymd').'-'.str_pad((Penjualan::count() + 1), 4, '0', STR_PAD_LEFT);
            while (Penjualan::where('no_transaksi', $noTransaksi)->exists()) {
                $noTransaksi = 'TRX-'.date('Ymd').'-'.strtoupper(Str::random(5));
            }

            // 4. Buat header transaksi penjualan
            $penjualan = Penjualan::create([
                'no_transaksi' => $noTransaksi,
                'id_user' => auth()->user()->id_user ?? auth()->id(),
                'id_pelanggan' => $validated['id_pelanggan'] ?? null,
                'tanggal' => now(),
                'total' => $total,
                'nominal_bayar' => $validated['nominal_bayar'],
                'kembalian' => $kembalian,
            ]);

            // 5. Simpan detail dan kurangi stok barang
            foreach ($itemsToProcess as $itemData) {
                PenjualanDetail::create([
                    'id_transaksi' => $penjualan->id_transaksi,
                    'jenis_item' => $itemData['jenis_item'],
                    'id_barang' => $itemData['id_barang'],
                    'id_jasa' => $itemData['id_jasa'],
                    'nama_item' => $itemData['nama_item'],
                    'harga_satuan' => $itemData['harga_satuan'],
                    'qty' => $itemData['qty'],
                    'subtotal' => $itemData['subtotal'],
                ]);

                if ($itemData['jenis_item'] === 'barang') {
                    $itemData['model']->decrement('stok', $itemData['qty']);
                }
            }

            return redirect()->route('kasir.transaksi')->with('status', "Transaksi #{$penjualan->no_transaksi} berhasil diproses! Total: Rp ".number_format($total, 0, ',', '.').', Kembalian: Rp '.number_format($kembalian, 0, ',', '.'));
        });
    }

    public function pelanggan()
    {
        $pelanggans = Pelanggan::withCount('penjualan')->orderBy('id_pelanggan', 'desc')->get();

        return view('pages.kasir.pelanggan', compact('pelanggans'));
    }

    public function stok()
    {
        $barangs = Barang::with('kategori')->orderBy('id_barang', 'desc')->get();

        return view('pages.kasir.stok', compact('barangs'));
    }

    public function riwayat(Request $request)
    {
        $query = Penjualan::with(['pelanggan', 'user', 'details.barang', 'details.jasa'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59',
            ]);
        }

        $transaksis = $query->get();

        return view('pages.kasir.riwayat', compact('transaksis'));
    }

    public function laporan(Request $request)
    {
        $tipe = $request->get('tipe', 'transaksi'); // 'transaksi' (detail) atau 'rekap' (harian)

        // Tentukan tanggal default:
        // Jika belum ada input tanggal dan ada data transaksi sebelumnya, gunakan awal bulan transaksi terakhir
        $defaultMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        if (Penjualan::whereDate('tanggal', '>=', $defaultMulai)->count() === 0 && Penjualan::exists()) {
            $latestTrxDate = Penjualan::max('tanggal');
            if ($latestTrxDate) {
                $defaultMulai = Carbon::parse($latestTrxDate)->startOfMonth()->format('Y-m-d');
            }
        }

        $tanggalMulai = $request->get('tanggal_mulai', $defaultMulai);
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::now()->format('Y-m-d'));

        $startDateTime = $tanggalMulai . ' 00:00:00';
        $endDateTime = $tanggalAkhir . ' 23:59:59';

        // Ambil transaksi penjualan sesuai rentang tanggal
        $transaksis = Penjualan::with(['pelanggan', 'user', 'details.barang', 'details.jasa'])
            ->whereBetween('tanggal', [$startDateTime, $endDateTime])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Rekapitulasi per tanggal
        $rekapHarian = Penjualan::selectRaw('DATE(tanggal) as tgl, COUNT(*) as jml_transaksi, SUM(total) as total_nominal')
            ->whereBetween('tanggal', [$startDateTime, $endDateTime])
            ->groupBy('tgl')
            ->orderBy('tgl', 'desc')
            ->get();

        $totalNominal = (int) $transaksis->sum('total');
        $totalTransaksi = $transaksis->count();
        $totalSetoran = $totalNominal;
        $rataRata = $totalTransaksi > 0 ? (int) round($transaksis->avg('total')) : 0;

        return view('pages.kasir.laporan', compact(
            'tipe',
            'tanggalMulai',
            'tanggalAkhir',
            'transaksis',
            'rekapHarian',
            'totalNominal',
            'totalSetoran',
            'totalTransaksi',
            'rataRata'
        ));
    }

    public function profil()
    {
        return view('pages.kasir.profil');
    }
}
