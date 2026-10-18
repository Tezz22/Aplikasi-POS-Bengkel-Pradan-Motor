<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Supplier;
use App\Models\Jasa;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Opname;
use App\Models\OpnameDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SEED USERS (ms_users)
        $admin1 = User::updateOrCreate(
            ['email' => 'admin@pradanmotor.com'],
            [
                'nama'     => 'Tezzy Admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'status'   => 'aktif',
            ]
        );

        $admin2 = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama'     => 'Admin Utama',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'status'   => 'aktif',
            ]
        );

        $kasir1 = User::updateOrCreate(
            ['email' => 'kasir@pradanmotor.com'],
            [
                'nama'     => 'Joo Kasir',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
                'status'   => 'aktif',
            ]
        );

        $kasir2 = User::updateOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'nama'     => 'Kasir Pradan',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
                'status'   => 'aktif',
            ]
        );

        $kasir3 = User::updateOrCreate(
            ['email' => 'rina@pradanmotor.com'],
            [
                'nama'     => 'Rina Wati',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
                'status'   => 'aktif',
            ]
        );

        $kasir4 = User::updateOrCreate(
            ['email' => 'budi@pradanmotor.com'],
            [
                'nama'     => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
                'status'   => 'aktif',
            ]
        );

        // 2. SEED KATEGORI (ms_kategori: nama_kategori max 25 chars)
        $kategoriList = [
            'Oli & Pelumas',
            'Sistem Rem',
            'Kelistrikan',
            'Ban & Roda',
            'Suku Cadang Mesin',
            'Transmisi CVT',
            'Aksesoris Bengkel',
        ];

        $katModels = [];
        foreach ($kategoriList as $namaKat) {
            $katModels[$namaKat] = Kategori::updateOrCreate(
                ['nama_kategori' => $namaKat],
                ['nama_kategori' => $namaKat]
            );
        }

        // 3. SEED SUPPLIER (ms_suppliier: nama_supplier max 50, no_telepon max 15, alamat max 255)
        $supplierList = [
            [
                'nama_supplier' => 'CV Rusdi Speed Parts',
                'no_telepon'   => '021-55443322',
                'alamat'        => 'Kawasan Industri Pulogadung Blok B No. 14, Jakarta Timur',
            ],
            [
                'nama_supplier' => 'UD Amba Motor Parts',
                'no_telepon'   => '021-88776655',
                'alamat'        => 'Jl. Raya Bekasi KM 21 No. 8, Cakung, Jakarta Timur',
            ],
            [
                'nama_supplier' => 'PT Astra Otoparts Tbk',
                'no_telepon'   => '021-4603888',
                'alamat'        => 'Jl. Pegangsaan Dua Km. 2.2, Kelapa Gading, Jakarta Utara',
            ],
            [
                'nama_supplier' => 'PT Castrol Indonesia',
                'no_telepon'   => '021-5272888',
                'alamat'        => 'Talavera Office Park Lt. 21, Jl. TB Simatupang, Jakarta Selatan',
            ],
            [
                'nama_supplier' => 'CV Sumber Makmur Motor',
                'no_telepon'   => '022-7788990',
                'alamat'        => 'Jl. Soekarno Hatta No. 120, Bandung',
            ],
        ];

        $supModels = [];
        foreach ($supplierList as $s) {
            $supModels[$s['nama_supplier']] = Supplier::updateOrCreate(
                ['nama_supplier' => $s['nama_supplier']],
                $s
            );
        }

        // 4. SEED BARANG (ms_barang: nama_barang max 30 chars, satuan max 20)
        $barangList = [
            [
                'nama_barang' => 'Oli MPX2 0.8L Matic',
                'kategori'    => 'Oli & Pelumas',
                'satuan'      => 'Botol',
                'harga_jual'  => 52000,
                'stok'        => 25,
            ],
            [
                'nama_barang' => 'Kampas Rem Depan Vario',
                'kategori'    => 'Sistem Rem',
                'satuan'      => 'Pcs',
                'harga_jual'  => 45000,
                'stok'        => 40,
            ],
            [
                'nama_barang' => 'Busi NGK CPR9EA-9',
                'kategori'    => 'Kelistrikan',
                'satuan'      => 'Pcs',
                'harga_jual'  => 25000,
                'stok'        => 50,
            ],
            [
                'nama_barang' => 'Oli Gardan Matic 100ml',
                'kategori'    => 'Oli & Pelumas',
                'satuan'      => 'Botol',
                'harga_jual'  => 17000,
                'stok'        => 30,
            ],
            [
                'nama_barang' => 'Aki GS Astra GTZ5S',
                'kategori'    => 'Kelistrikan',
                'satuan'      => 'Pcs',
                'harga_jual'  => 230000,
                'stok'        => 12,
            ],
            [
                'nama_barang' => 'Ban Luar FDR 90/90-14',
                'kategori'    => 'Ban & Roda',
                'satuan'      => 'Pcs',
                'harga_jual'  => 210000,
                'stok'        => 15,
            ],
            [
                'nama_barang' => 'V-Belt Kit Beat FI',
                'kategori'    => 'Transmisi CVT',
                'satuan'      => 'Set',
                'harga_jual'  => 105000,
                'stok'        => 18,
            ],
            [
                'nama_barang' => 'Filter Udara Vario 125',
                'kategori'    => 'Suku Cadang Mesin',
                'satuan'      => 'Pcs',
                'harga_jual'  => 50000,
                'stok'        => 22,
            ],
            [
                'nama_barang' => 'Roller CVT TDR 10gr',
                'kategori'    => 'Transmisi CVT',
                'satuan'      => 'Set',
                'harga_jual'  => 85000,
                'stok'        => 16,
            ],
            [
                'nama_barang' => 'Kampas Tromol Honda',
                'kategori'    => 'Sistem Rem',
                'satuan'      => 'Pcs',
                'harga_jual'  => 40000,
                'stok'        => 28,
            ],
        ];

        $brgModels = [];
        foreach ($barangList as $b) {
            $kat = $katModels[$b['kategori']];
            $brgModels[$b['nama_barang']] = Barang::updateOrCreate(
                ['nama_barang' => $b['nama_barang']],
                [
                    'id_kategori' => $kat->id_kategori,
                    'nama_barang' => $b['nama_barang'],
                    'satuan'      => $b['satuan'],
                    'harga_jual'  => $b['harga_jual'],
                    'stok'        => $b['stok'],
                ]
            );
        }

        // 5. SEED PELANGGAN (ms_pelanggan: nama max 50, plat_nomor max 12, no_telepon max 15)
        $pelangganList = [
            [
                'nama'       => 'Andi Saputra',
                'plat_nomor' => 'B 1234 ABC',
                'no_telepon' => '081234567890',
            ],
            [
                'nama'       => 'Budi Wicaksono',
                'plat_nomor' => 'B 5678 XYZ',
                'no_telepon' => '081398765432',
            ],
            [
                'nama'       => 'Citra Mandasari',
                'plat_nomor' => 'D 4321 DEF',
                'no_telepon' => '085712345678',
            ],
            [
                'nama'       => 'Dian Pratama',
                'plat_nomor' => 'B 8899 GHI',
                'no_telepon' => '082144556677',
            ],
            [
                'nama'       => 'Eka Ramadhani',
                'plat_nomor' => 'F 2233 JKL',
                'no_telepon' => '087899887766',
            ],
        ];

        $plgModels = [];
        foreach ($pelangganList as $p) {
            $plgModels[$p['nama']] = Pelanggan::updateOrCreate(
                ['nama' => $p['nama']],
                $p
            );
        }

        // 6. SEED JASA (ms_jasa: nama_jasa max 120, tarif int)
        $jasaList = [
            ['nama_jasa' => 'Servis Ringan / Tune Up', 'tarif' => 45000],
            ['nama_jasa' => 'Ganti Oli Mesin & Gardan', 'tarif' => 10000],
            ['nama_jasa' => 'Servis CVT Lengkap', 'tarif' => 65000],
            ['nama_jasa' => 'Bongkar Pasang Ban Luar', 'tarif' => 20000],
            ['nama_jasa' => 'Ganti Kampas Rem Depan/Belakang', 'tarif' => 15000],
            ['nama_jasa' => 'Kuras & Ganti Minyak Rem', 'tarif' => 25000],
            ['nama_jasa' => 'Overhaul / Turun Mesin', 'tarif' => 350000],
        ];

        $jasaModels = [];
        foreach ($jasaList as $j) {
            $jasaModels[$j['nama_jasa']] = Jasa::updateOrCreate(
                ['nama_jasa' => $j['nama_jasa']],
                $j
            );
        }

        // 7. SEED PEMBELIAN (tr_pembelian: id_barang, id_supplier, id_user, no_referensi max 25, tanggal, keterangan, jumlah, harga_beli, subtotal)
        $pembelianData = [
            [
                'barang'       => 'Oli MPX2 0.8L Matic',
                'supplier'     => 'CV Rusdi Speed Parts',
                'no_referensi' => 'REF-202610-001',
                'tanggal'      => '2026-10-01',
                'keterangan'   => 'Restock stok oli bulanan',
                'jumlah'       => 20,
                'harga_beli'   => 42000,
                'subtotal'     => 840000,
            ],
            [
                'barang'       => 'Kampas Rem Depan Vario',
                'supplier'     => 'UD Amba Motor Parts',
                'no_referensi' => 'REF-202610-002',
                'tanggal'      => '2026-10-03',
                'keterangan'   => 'Pasokan kampas rem cakram',
                'jumlah'       => 30,
                'harga_beli'   => 30000,
                'subtotal'     => 900000,
            ],
            [
                'barang'       => 'Busi NGK CPR9EA-9',
                'supplier'     => 'PT Astra Otoparts Tbk',
                'no_referensi' => 'REF-202610-003',
                'tanggal'      => '2026-10-05',
                'keterangan'   => 'Busi NGK motor matic',
                'jumlah'       => 40,
                'harga_beli'   => 18000,
                'subtotal'     => 720000,
            ],
            [
                'barang'       => 'Aki GS Astra GTZ5S',
                'supplier'     => 'PT Astra Otoparts Tbk',
                'no_referensi' => 'REF-202610-004',
                'tanggal'      => '2026-10-08',
                'keterangan'   => 'Restock aki kering',
                'jumlah'       => 10,
                'harga_beli'   => 185000,
                'subtotal'     => 1850000,
            ],
            [
                'barang'       => 'Ban Luar FDR 90/90-14',
                'supplier'     => 'CV Sumber Makmur Motor',
                'no_referensi' => 'REF-202610-005',
                'tanggal'      => '2026-10-10',
                'keterangan'   => 'Pasokan ban luar tubeless FDR',
                'jumlah'       => 12,
                'harga_beli'   => 165000,
                'subtotal'     => 1980000,
            ],
        ];

        foreach ($pembelianData as $pb) {
            $brg = $brgModels[$pb['barang']];
            $sup = $supModels[$pb['supplier']];
            Pembelian::updateOrCreate(
                ['no_referensi' => $pb['no_referensi']],
                [
                    'id_barang'    => $brg->id_barang,
                    'id_supplier'  => $sup->id_supplier,
                    'id_user'      => $admin1->id_user,
                    'no_referensi' => $pb['no_referensi'],
                    'tanggal'      => $pb['tanggal'],
                    'keterangan'   => $pb['keterangan'],
                    'jumlah'       => $pb['jumlah'],
                    'harga_beli'   => $pb['harga_beli'],
                    'subtotal'     => $pb['subtotal'],
                ]
            );
        }

        // 8. SEED PENJUALAN & DETAIL PENJUALAN (tr_penjualan & detail_penjualan)
        $penjualanData = [
            [
                'no_transaksi'  => 'TRX-20261015-001',
                'tanggal'       => '2026-10-15 10:30:00',
                'pelanggan'     => 'Andi Saputra',
                'user_id'       => $kasir1->id_user,
                'total'         => 97000,
                'nominal_bayar' => 100000,
                'kembalian'     => 3000,
                'details'       => [
                    [
                        'jenis_item'   => 'barang',
                        'id_barang'    => $brgModels['Oli MPX2 0.8L Matic']->id_barang,
                        'id_jasa'      => null,
                        'nama_item'    => 'Oli MPX2 0.8L Matic',
                        'harga_satuan' => 52000,
                        'qty'          => 1,
                        'subtotal'     => 52000,
                    ],
                    [
                        'jenis_item'   => 'jasa',
                        'id_barang'    => null,
                        'id_jasa'      => $jasaModels['Servis Ringan / Tune Up']->id_jasa,
                        'nama_item'    => 'Servis Ringan / Tune Up',
                        'harga_satuan' => 45000,
                        'qty'          => 1,
                        'subtotal'     => 45000,
                    ],
                ],
            ],
            [
                'no_transaksi'  => 'TRX-20261015-002',
                'tanggal'       => '2026-10-15 13:45:00',
                'pelanggan'     => 'Budi Wicaksono',
                'user_id'       => $kasir1->id_user,
                'total'         => 60000,
                'nominal_bayar' => 60000,
                'kembalian'     => 0,
                'details'       => [
                    [
                        'jenis_item'   => 'barang',
                        'id_barang'    => $brgModels['Kampas Rem Depan Vario']->id_barang,
                        'id_jasa'      => null,
                        'nama_item'    => 'Kampas Rem Depan Vario',
                        'harga_satuan' => 45000,
                        'qty'          => 1,
                        'subtotal'     => 45000,
                    ],
                    [
                        'jenis_item'   => 'jasa',
                        'id_barang'    => null,
                        'id_jasa'      => $jasaModels['Ganti Kampas Rem Depan/Belakang']->id_jasa,
                        'nama_item'    => 'Ganti Kampas Rem Depan/Belakang',
                        'harga_satuan' => 15000,
                        'qty'          => 1,
                        'subtotal'     => 15000,
                    ],
                ],
            ],
            [
                'no_transaksi'  => 'TRX-20261016-001',
                'tanggal'       => '2026-10-16 11:15:00',
                'pelanggan'     => 'Citra Mandasari',
                'user_id'       => $kasir2->id_user,
                'total'         => 69000,
                'nominal_bayar' => 70000,
                'kembalian'     => 1000,
                'details'       => [
                    [
                        'jenis_item'   => 'barang',
                        'id_barang'    => $brgModels['Oli MPX2 0.8L Matic']->id_barang,
                        'id_jasa'      => null,
                        'nama_item'    => 'Oli MPX2 0.8L Matic',
                        'harga_satuan' => 52000,
                        'qty'          => 1,
                        'subtotal'     => 52000,
                    ],
                    [
                        'jenis_item'   => 'barang',
                        'id_barang'    => $brgModels['Oli Gardan Matic 100ml']->id_barang,
                        'id_jasa'      => null,
                        'nama_item'    => 'Oli Gardan Matic 100ml',
                        'harga_satuan' => 17000,
                        'qty'          => 1,
                        'subtotal'     => 17000,
                    ],
                ],
            ],
            [
                'no_transaksi'  => 'TRX-20261016-002',
                'tanggal'       => '2026-10-16 14:00:00',
                'pelanggan'     => 'Dian Pratama',
                'user_id'       => $kasir1->id_user,
                'total'         => 230000,
                'nominal_bayar' => 250000,
                'kembalian'     => 20000,
                'details'       => [
                    [
                        'jenis_item'   => 'barang',
                        'id_barang'    => $brgModels['Ban Luar FDR 90/90-14']->id_barang,
                        'id_jasa'      => null,
                        'nama_item'    => 'Ban Luar FDR 90/90-14',
                        'harga_satuan' => 210000,
                        'qty'          => 1,
                        'subtotal'     => 210000,
                    ],
                    [
                        'jenis_item'   => 'jasa',
                        'id_barang'    => null,
                        'id_jasa'      => $jasaModels['Bongkar Pasang Ban Luar']->id_jasa,
                        'nama_item'    => 'Bongkar Pasang Ban Luar',
                        'harga_satuan' => 20000,
                        'qty'          => 1,
                        'subtotal'     => 20000,
                    ],
                ],
            ],
            [
                'no_transaksi'  => 'TRX-20261017-001',
                'tanggal'       => '2026-10-17 09:20:00',
                'pelanggan'     => 'Eka Ramadhani',
                'user_id'       => $kasir2->id_user,
                'total'         => 190000,
                'nominal_bayar' => 200000,
                'kembalian'     => 10000,
                'details'       => [
                    [
                        'jenis_item'   => 'barang',
                        'id_barang'    => $brgModels['V-Belt Kit Beat FI']->id_barang,
                        'id_jasa'      => null,
                        'nama_item'    => 'V-Belt Kit Beat FI',
                        'harga_satuan' => 105000,
                        'qty'          => 1,
                        'subtotal'     => 105000,
                    ],
                    [
                        'jenis_item'   => 'barang',
                        'id_barang'    => $brgModels['Roller CVT TDR 10gr']->id_barang,
                        'id_jasa'      => null,
                        'nama_item'    => 'Roller CVT TDR 10gr',
                        'harga_satuan' => 85000,
                        'qty'          => 1,
                        'subtotal'     => 85000,
                    ],
                ],
            ],
        ];

        foreach ($penjualanData as $pj) {
            $details = $pj['details'];
            $plg = $plgModels[$pj['pelanggan']] ?? null;

            $penjualan = Penjualan::updateOrCreate(
                ['no_transaksi' => $pj['no_transaksi']],
                [
                    'id_user'       => $pj['user_id'],
                    'id_pelanggan'  => $plg ? $plg->id_pelanggan : null,
                    'id_kendaraan'  => null,
                    'no_transaksi'  => $pj['no_transaksi'],
                    'tanggal'       => $pj['tanggal'],
                    'total'         => $pj['total'],
                    'nominal_bayar' => $pj['nominal_bayar'],
                    'kembalian'     => $pj['kembalian'],
                ]
            );

            $penjualan->details()->delete();
            foreach ($details as $d) {
                $penjualan->details()->create($d);
            }
        }

        // 9. SEED STOK OPNAME (ms_penyesuaian & ms_penyesuaian_detail)
        $opnameData = [
            [
                'tanggal'    => '2026-10-01 17:00:00',
                'user_id'    => $admin1->id_user,
                'keterangan' => 'Penyesuaian stok awal bulan Oktober',
                'details'    => [
                    [
                        'barang'      => 'Oli MPX2 0.8L Matic',
                        'stok_sistem' => 26,
                        'stok_fisik'  => 25,
                        'alasan'      => '1 botol rembes/bocor saat handling di rak',
                    ],
                    [
                        'barang'      => 'Kampas Rem Depan Vario',
                        'stok_sistem' => 40,
                        'stok_fisik'  => 40,
                        'alasan'      => 'Stok fisik sesuai data sistem',
                    ],
                ]
            ],
            [
                'tanggal'    => '2026-10-15 18:00:00',
                'user_id'    => $admin1->id_user,
                'keterangan' => 'Audit pertengahan bulan suku cadang kelistrikan',
                'details'    => [
                    [
                        'barang'      => 'Busi NGK CPR9EA-9',
                        'stok_sistem' => 52,
                        'stok_fisik'  => 50,
                        'alasan'      => '2 pcs busi rusak isolator keramiknya',
                    ],
                ]
            ],
        ];

        foreach ($opnameData as $op) {
            $opname = Opname::create([
                'id_user'    => $op['user_id'],
                'tanggal'    => $op['tanggal'],
                'keterangan' => $op['keterangan'],
            ]);

            foreach ($op['details'] as $dt) {
                $brg = $brgModels[$dt['barang']];
                $opname->details()->create([
                    'id_barang'   => $brg->id_barang,
                    'stok_sistem' => $dt['stok_sistem'],
                    'stok_fisik'  => $dt['stok_fisik'],
                    'alasan'      => $dt['alasan'],
                ]);
            }
        }
    }
}
