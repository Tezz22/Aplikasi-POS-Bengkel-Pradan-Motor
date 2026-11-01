<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\OpnameDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'penjualan');
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', Carbon::now()->format('Y-m-d'));

        $startDateTime = $tanggalMulai . ' 00:00:00';
        $endDateTime = $tanggalAkhir . ' 23:59:59';

        $data = collect();
        $summary = [];

        switch ($jenis) {
            case 'pembelian':
                $data = Pembelian::with(['barang', 'supplier', 'user'])
                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
                    ->orderBy('tanggal', 'desc')
                    ->get();

                $summary = [
                    'total_nominal'   => (int) $data->sum('subtotal'),
                    'total_item'      => (int) $data->sum('jumlah'),
                    'total_transaksi' => $data->count(),
                ];
                break;

            case 'stok':
                $data = OpnameDetail::with(['opname.user', 'barang'])
                    ->whereHas('opname', function ($q) use ($startDateTime, $endDateTime) {
                        $q->whereBetween('tanggal', [$startDateTime, $endDateTime]);
                    })
                    ->get();

                $summary = [
                    'total_penyesuaian' => $data->count(),
                    'total_selisih'     => (int) $data->sum(fn ($item) => abs($item->stok_fisik - $item->stok_sistem)),
                ];
                break;

            case 'jasa':
                $data = PenjualanDetail::with(['penjualan.user', 'penjualan.pelanggan', 'jasa'])
                    ->where('jenis_item', 'jasa')
                    ->whereHas('penjualan', function ($q) use ($startDateTime, $endDateTime) {
                        $q->whereBetween('tanggal', [$startDateTime, $endDateTime]);
                    })
                    ->get();

                $summary = [
                    'total_nominal'   => (int) $data->sum('subtotal'),
                    'total_jasa'      => (int) $data->sum('qty'),
                    'total_transaksi' => $data->pluck('id_transaksi')->unique()->count(),
                ];
                break;

            case 'penjualan':
            default:
                $jenis = 'penjualan';
                $data = Penjualan::with(['user', 'pelanggan', 'details'])
                    ->whereBetween('tanggal', [$startDateTime, $endDateTime])
                    ->orderBy('tanggal', 'desc')
                    ->get();

                $summary = [
                    'total_nominal'   => (int) $data->sum('total'),
                    'total_transaksi' => $data->count(),
                    'rata_rata'       => $data->count() > 0 ? (int) round($data->avg('total')) : 0,
                ];
                break;
        }

        return view('pages.admin.laporan', compact(
            'jenis',
            'tanggalMulai',
            'tanggalAkhir',
            'data',
            'summary'
        ));
    }
}

