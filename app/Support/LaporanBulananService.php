<?php

namespace App\Support;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanBulananService
{
    /**
     * Nama-nama bulan dalam bahasa Indonesia.
     */
    public const NAMA_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Menghasilkan dataset lengkap untuk laporan grafik penjualan & barang terlaris.
     */
    public static function getData(Request $request): array
    {
        // 1. Ambil daftar tahun transaksi yang tersedia di database
        $daftarTahun = Penjualan::selectRaw('DISTINCT YEAR(tanggal) as yr')
            ->pluck('yr')
            ->filter()
            ->sortDesc()
            ->values()
            ->all();

        $tahunIni = (int) date('Y');
        if (empty($daftarTahun)) {
            $daftarTahun = [$tahunIni];
        } elseif (! in_array($tahunIni, $daftarTahun)) {
            array_unshift($daftarTahun, $tahunIni);
        }

        // 2. Tahun yang dipilih
        $tahunDefault = $daftarTahun[0];
        if ($request->filled('tanggal_mulai')) {
            $tahunDefault = (int) Carbon::parse($request->tanggal_mulai)->format('Y');
        }
        $tahun = (int) $request->get('tahun', $tahunDefault);
        if (! in_array($tahun, $daftarTahun)) {
            $tahun = $daftarTahun[0];
        }

        // 3. Filter bulan (apakah dipilih 1 bulan tertentu atau 'all' untuk 1 tahun penuh)
        $bulanFilter = $request->get('bulan_filter', 'all');
        $isFilterBulan = ($bulanFilter !== 'all' && is_numeric($bulanFilter) && (int) $bulanFilter >= 1 && (int) $bulanFilter <= 12);
        $bulanTerpilih = $isFilterBulan ? (int) $bulanFilter : null;
        $namaBulanTerpilih = $isFilterBulan ? (self::NAMA_BULAN[$bulanTerpilih] ?? '') : null;

        // Inisialisasi variabel output
        $chartLabels = [];
        $chartNominal = [];
        $chartTransaksi = [];
        $periodNominal = 0;
        $periodTransaksi = 0;
        $periodQty = 0;
        $rataRataPeriodik = 0;

        if ($isFilterBulan) {
            // Mode: Filter 1 Bulan tertentu -> Tampilkan grafik penjualan harian berdasarkan tanggal (Tgl 1 s/d Tgl N)
            $carbonDate = Carbon::create($tahun, $bulanTerpilih, 1);
            $jumlahHari = $carbonDate->daysInMonth;

            $penjualanHarian = Penjualan::selectRaw('
                    DAY(tanggal) as tgl,
                    COUNT(*) as total_transaksi,
                    SUM(total) as total_nominal
                ')
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulanTerpilih)
                ->groupBy('tgl')
                ->get()
                ->keyBy('tgl');

            $hariAktifCount = 0;
            for ($d = 1; $d <= $jumlahHari; $d++) {
                $row = $penjualanHarian->get($d);
                $nom = $row ? (int) $row->total_nominal : 0;
                $cnt = $row ? (int) $row->total_transaksi : 0;

                $chartLabels[] = 'Tgl ' . $d;
                $chartNominal[] = $nom;
                $chartTransaksi[] = $cnt;

                $periodNominal += $nom;
                $periodTransaksi += $cnt;
                if ($cnt > 0) {
                    $hariAktifCount++;
                }
            }

            $rataRataPeriodik = $hariAktifCount > 0 ? (int) round($periodNominal / $hariAktifCount) : 0;
        } else {
            // Mode: 1 Tahun Penuh -> Tampilkan grafik penjualan 12 bulan (Januari s/d Desember)
            $penjualanBulanan = Penjualan::selectRaw('
                    MONTH(tanggal) as bulan,
                    COUNT(*) as total_transaksi,
                    SUM(total) as total_nominal
                ')
                ->whereYear('tanggal', $tahun)
                ->groupBy('bulan')
                ->get()
                ->keyBy('bulan');

            $bulanAktifCount = 0;
            for ($m = 1; $m <= 12; $m++) {
                $row = $penjualanBulanan->get($m);
                $nom = $row ? (int) $row->total_nominal : 0;
                $cnt = $row ? (int) $row->total_transaksi : 0;

                $chartLabels[] = self::NAMA_BULAN[$m];
                $chartNominal[] = $nom;
                $chartTransaksi[] = $cnt;

                $periodNominal += $nom;
                $periodTransaksi += $cnt;
                if ($cnt > 0) {
                    $bulanAktifCount++;
                }
            }

            $rataRataPeriodik = $bulanAktifCount > 0 ? (int) round($periodNominal / $bulanAktifCount) : 0;
        }

        // 4. Data Barang Paling Laris pada periode terpilih (Bulan spesifik atau Tahun penuh)
        $detailQuery = PenjualanDetail::selectRaw('
                detail_penjualan.id_barang,
                detail_penjualan.nama_item,
                SUM(detail_penjualan.qty) as total_terjual,
                SUM(detail_penjualan.subtotal) as total_nominal
            ')
            ->join('tr_penjualan', 'detail_penjualan.id_transaksi', '=', 'tr_penjualan.id_transaksi')
            ->where('detail_penjualan.jenis_item', 'barang')
            ->whereYear('tr_penjualan.tanggal', $tahun);

        if ($isFilterBulan) {
            $detailQuery->whereMonth('tr_penjualan.tanggal', $bulanTerpilih);
        }

        $topBarangList = $detailQuery
            ->groupBy('detail_penjualan.id_barang', 'detail_penjualan.nama_item')
            ->orderByDesc('total_terjual')
            ->take(10)
            ->get();

        // Hitung total unit barang yang terjual pada periode ini
        $periodQty = (int) $topBarangList->sum('total_terjual');

        // Susun dataset grafik barang terlaris langsung berdasarkan nama produk
        $chartLarisLabels = [];
        $chartLarisQty = [];
        $chartLarisNominal = [];

        foreach ($topBarangList as $item) {
            $chartLarisLabels[] = $item->nama_item;
            $chartLarisQty[] = (int) $item->total_terjual;
            $chartLarisNominal[] = (int) $item->total_nominal;
        }

        // Produk juara #1 pada periode terpilih
        $juaraBarang = $topBarangList->first();

        // 5. Query rekapitulasi 12 bulan (selalu sediakan untuk rekapBulan, juaraPerBulan, dan barangTerlarisPerBulan jika diperlukan)
        $penjualanBulananSemua = Penjualan::selectRaw('
                MONTH(tanggal) as bulan,
                COUNT(*) as total_transaksi,
                SUM(total) as total_nominal
            ')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $rekapBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $row = $penjualanBulananSemua->get($m);
            $rekapBulan[$m] = [
                'bulan_angka'     => $m,
                'bulan_nama'      => self::NAMA_BULAN[$m],
                'total_nominal'   => $row ? (int) $row->total_nominal : 0,
                'total_transaksi' => $row ? (int) $row->total_transaksi : 0,
                'total_qty'       => 0,
            ];
        }

        // Ambil detail penjualan untuk rekap bulanan
        $semuaDetail = PenjualanDetail::selectRaw('
                MONTH(tr_penjualan.tanggal) as bulan,
                detail_penjualan.id_barang,
                detail_penjualan.nama_item,
                SUM(detail_penjualan.qty) as total_terjual,
                SUM(detail_penjualan.subtotal) as total_nominal
            ')
            ->join('tr_penjualan', 'detail_penjualan.id_transaksi', '=', 'tr_penjualan.id_transaksi')
            ->where('detail_penjualan.jenis_item', 'barang')
            ->whereYear('tr_penjualan.tanggal', $tahun)
            ->groupBy('bulan', 'detail_penjualan.id_barang', 'detail_penjualan.nama_item')
            ->orderBy('bulan')
            ->orderByDesc('total_terjual')
            ->get();

        $barangTerlarisPerBulan = $semuaDetail->groupBy('bulan');
        $juaraPerBulan = [];

        for ($m = 1; $m <= 12; $m++) {
            $itemsBulanIni = $barangTerlarisPerBulan->get($m);
            if ($itemsBulanIni && $itemsBulanIni->isNotEmpty()) {
                $top = $itemsBulanIni->first();
                $juaraPerBulan[$m] = [
                    'nama_item'     => $top->nama_item,
                    'total_terjual' => (int) $top->total_terjual,
                    'total_nominal' => (int) $top->total_nominal,
                ];
                $rekapBulan[$m]['total_qty'] = (int) $itemsBulanIni->sum('total_terjual');
            } else {
                $juaraPerBulan[$m] = null;
            }
        }

        return [
            'tahun'                  => $tahun,
            'daftarTahun'            => $daftarTahun,
            'bulanFilter'            => $bulanFilter,
            'isFilterBulan'          => $isFilterBulan,
            'bulanTerpilih'          => $bulanTerpilih,
            'namaBulanTerpilih'      => $namaBulanTerpilih,
            'namaBulanList'          => self::NAMA_BULAN,

            // Dataset Grafik Penjualan (Harian jika 1 bulan, Bulanan jika 1 tahun)
            'chartLabels'            => $chartLabels,
            'chartNominal'           => $chartNominal,
            'chartTransaksi'         => $chartTransaksi,

            // Dataset Grafik Barang Terlaris (Langsung menampilkan nama barang)
            'chartLarisLabels'       => $chartLarisLabels,
            'chartLarisQty'          => $chartLarisQty,
            'chartLarisNominal'      => $chartLarisNominal,

            // Data Top Barang
            'topBarangList'          => $topBarangList,
            'juaraBarang'            => $juaraBarang,

            // Ringkasan 12 bulan
            'rekapBulan'             => $rekapBulan,
            'juaraPerBulan'          => $juaraPerBulan,
            'barangTerlarisPerBulan' => $barangTerlarisPerBulan,

            // Ringkasan Statistik Periode
            'totalPeriodNominal'     => $periodNominal,
            'totalPeriodTransaksi'   => $periodTransaksi,
            'totalPeriodQty'         => $periodQty,
            'rataRataPeriodik'       => $rataRataPeriodik,

            // Kompatibilitas variabel lama
            'totalTahunanNominal'    => $periodNominal,
            'totalTahunanTransaksi'  => $periodTransaksi,
            'totalTahunanQty'        => $periodQty,
            'rataRataBulanan'        => $rataRataPeriodik,
            'juaraTahunan'           => $juaraBarang,
            'topBarangTahunan'       => $topBarangList,
        ];
    }
}
