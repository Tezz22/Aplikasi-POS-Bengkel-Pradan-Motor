<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Supplier;
use App\Models\Jasa;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
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
        // 1. SEED USERS (7 users: 2 admin, 5 kasir)
        $admin1 = User::updateOrCreate(
            ['email' => 'admin@pradanmotor.com'],
            [
                'name'     => 'Tezzy',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        $admin2 = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin Pradan Motor',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        $kasir1 = User::updateOrCreate(
            ['email' => 'kasir@pradanmotor.com'],
            [
                'name'     => 'Joo',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ]
        );

        $kasir2 = User::updateOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name'     => 'Kasir Pradan Motor',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ]
        );

        $kasir3 = User::updateOrCreate(
            ['email' => 'rina@pradanmotor.com'],
            [
                'name'     => 'Rina Wati',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ]
        );

        $kasir4 = User::updateOrCreate(
            ['email' => 'budi@pradanmotor.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ]
        );

        $kasir5 = User::updateOrCreate(
            ['email' => 'ayu@pradanmotor.com'],
            [
                'name'     => 'Ayu Lestari',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ]
        );

        // 2. SEED KATEGORI (7 kategori)
        $kategoriList = [
            [
                'kode_kategori' => 'KTG-01',
                'nama_kategori' => 'Oli & Pelumas',
                'deskripsi'     => 'Oli mesin, gardan, shock, dan minyak rem berbagai merk ternama',
            ],
            [
                'kode_kategori' => 'KTG-02',
                'nama_kategori' => 'Sistem Pengereman',
                'deskripsi'     => 'Kampas rem cakram dan tromol depan/belakang motor matic & bebek',
            ],
            [
                'kode_kategori' => 'KTG-03',
                'nama_kategori' => 'Kelistrikan',
                'deskripsi'     => 'Aki motor kering/basah, busi standar/iridium, bohlam lampu, sekring',
            ],
            [
                'kode_kategori' => 'KTG-04',
                'nama_kategori' => 'Ban & Roda',
                'deskripsi'     => 'Ban luar tubeless, ban dalam, pentil, cairan anti bocor, laher roda',
            ],
            [
                'kode_kategori' => 'KTG-05',
                'nama_kategori' => 'Suku Cadang Mesin',
                'deskripsi'     => 'Piston, ring seher, klep, filter oli, packing blok mesin, paking set',
            ],
            [
                'kode_kategori' => 'KTG-06',
                'nama_kategori' => 'Transmisi & CVT',
                'deskripsi'     => 'V-Belt, roller set, kampas ganda, mangkok kopling, per cvt, slider',
            ],
            [
                'kode_kategori' => 'KTG-07',
                'nama_kategori' => 'Aksesoris & Perawatan',
                'deskripsi'     => 'Spion standar, handgrip, carb cleaner, injector cleaner, radiator coolant',
            ],
        ];

        $katModels = [];
        foreach ($kategoriList as $k) {
            $katModels[$k['kode_kategori']] = Kategori::updateOrCreate(
                ['kode_kategori' => $k['kode_kategori']],
                $k
            );
        }

        // 3. SEED SUPPLIER (6 supplier)
        $supplierList = [
            [
                'kode_supplier' => 'SPL-01',
                'nama_supplier' => 'CV Rusdi Speed',
                'no_telp'       => '021-55443322',
                'kontak_person' => 'Pak Rusdi (0812999888)',
                'alamat'        => 'Kawasan Industri Pulogadung Blok B No. 14, Jakarta Timur',
            ],
            [
                'kode_supplier' => 'SPL-02',
                'nama_supplier' => 'UD Amba Motor Parts',
                'no_telp'       => '021-88776655',
                'kontak_person' => 'Pak Amba (0818223344)',
                'alamat'        => 'Jl. Raya Bekasi KM 21 No. 8, Cakung, Jakarta Timur',
            ],
            [
                'kode_supplier' => 'SPL-03',
                'nama_supplier' => 'PT Astra Otoparts Tbk',
                'no_telp'       => '021-4603888',
                'kontak_person' => 'Ibu Linda (0811223344)',
                'alamat'        => 'Jl. Pegangsaan Dua Km. 2.2, Kelapa Gading, Jakarta Utara',
            ],
            [
                'kode_supplier' => 'SPL-04',
                'nama_supplier' => 'PT Castrol Indonesia',
                'no_telp'       => '021-5272888',
                'kontak_person' => 'Pak Hendra (0813778899)',
                'alamat'        => 'Talavera Office Park Lt. 21, Jl. TB Simatupang, Jakarta Selatan',
            ],
            [
                'kode_supplier' => 'SPL-05',
                'nama_supplier' => 'CV Sumber Makmur Motor',
                'no_telp'       => '022-7788990',
                'kontak_person' => 'Pak Hendro (0856112233)',
                'alamat'        => 'Jl. Soekarno Hatta No. 120, Bandung',
            ],
            [
                'kode_supplier' => 'SPL-06',
                'nama_supplier' => 'PT Federal Parts Sukses',
                'no_telp'       => '021-8934567',
                'kontak_person' => 'Pak Kevin (0878123456)',
                'alamat'        => 'Kawasan Industri Jababeka Blok C No. 5, Cikarang',
            ],
        ];

        $supModels = [];
        foreach ($supplierList as $s) {
            $supModels[$s['kode_supplier']] = Supplier::updateOrCreate(
                ['kode_supplier' => $s['kode_supplier']],
                $s
            );
        }

        // 4. SEED BARANG (10 produk suku cadang)
        $barangList = [
            [
                'kode_barang' => 'BRG-001',
                'barcode'     => '448855262223',
                'nama_barang' => 'Oli Mesin MPX2 0.8L (Matic)',
                'kategori_id' => $katModels['KTG-01']->id,
                'satuan'      => 'Botol',
                'harga_beli'  => 42000,
                'harga_jual'  => 52000,
                'stok'        => 9,
            ],
            [
                'kode_barang' => 'BRG-002',
                'barcode'     => '775511333994',
                'nama_barang' => 'Kampas Rem Depan Honda Vario / Beat',
                'kategori_id' => $katModels['KTG-02']->id,
                'satuan'      => 'Pcs',
                'harga_beli'  => 30000,
                'harga_jual'  => 45000,
                'stok'        => 100,
            ],
            [
                'kode_barang' => 'BRG-003',
                'barcode'     => '8992345410181',
                'nama_barang' => 'Busi Standar NGK CPR9EA-9',
                'kategori_id' => $katModels['KTG-03']->id,
                'satuan'      => 'Pcs',
                'harga_beli'  => 18000,
                'harga_jual'  => 25000,
                'stok'        => 50,
            ],
            [
                'kode_barang' => 'BRG-004',
                'barcode'     => '8997011500123',
                'nama_barang' => 'Oli Gardan Matic Yamalube 100ml',
                'kategori_id' => $katModels['KTG-01']->id,
                'satuan'      => 'Botol',
                'harga_beli'  => 12000,
                'harga_jual'  => 17000,
                'stok'        => 4,
            ],
            [
                'kode_barang' => 'BRG-005',
                'barcode'     => '8991234567890',
                'nama_barang' => 'Aki Kering GS Astra GTZ5S 12V 3.5Ah',
                'kategori_id' => $katModels['KTG-03']->id,
                'satuan'      => 'Pcs',
                'harga_beli'  => 185000,
                'harga_jual'  => 230000,
                'stok'        => 8,
            ],
            [
                'kode_barang' => 'BRG-006',
                'barcode'     => '8999876543210',
                'nama_barang' => 'Ban Luar Tubeless FDR 90/90-14',
                'kategori_id' => $katModels['KTG-04']->id,
                'satuan'      => 'Pcs',
                'harga_beli'  => 165000,
                'harga_jual'  => 210000,
                'stok'        => 15,
            ],
            [
                'kode_barang' => 'BRG-007',
                'barcode'     => '8995554443332',
                'nama_barang' => 'V-Belt Kit Honda Beat FI / Scoopy ESP',
                'kategori_id' => $katModels['KTG-06']->id,
                'satuan'      => 'Set',
                'harga_beli'  => 75000,
                'harga_jual'  => 105000,
                'stok'        => 12,
            ],
            [
                'kode_barang' => 'BRG-008',
                'barcode'     => '8991112223334',
                'nama_barang' => 'Filter Udara Vario 125/150 LED',
                'kategori_id' => $katModels['KTG-05']->id,
                'satuan'      => 'Pcs',
                'harga_beli'  => 35000,
                'harga_jual'  => 50000,
                'stok'        => 20,
            ],
            [
                'kode_barang' => 'BRG-009',
                'barcode'     => '8994443332221',
                'nama_barang' => 'Roller CVT TDR Racing 10gr (Set)',
                'kategori_id' => $katModels['KTG-06']->id,
                'satuan'      => 'Set',
                'harga_beli'  => 60000,
                'harga_jual'  => 85000,
                'stok'        => 6,
            ],
            [
                'kode_barang' => 'BRG-010',
                'barcode'     => '8998887776665',
                'nama_barang' => 'Kampas Rem Belakang Tromol Honda',
                'kategori_id' => $katModels['KTG-02']->id,
                'satuan'      => 'Pcs',
                'harga_beli'  => 28000,
                'harga_jual'  => 40000,
                'stok'        => 22,
            ],
        ];

        $brgModels = [];
        foreach ($barangList as $b) {
            $brgModels[$b['kode_barang']] = Barang::updateOrCreate(
                ['kode_barang' => $b['kode_barang']],
                $b
            );
        }

        // 5. SEED PELANGGAN (7 pelanggan motor)
        $pelangganList = [
            [
                'kode_pelanggan' => 'PLG-001',
                'nama'           => 'Andi Saputra',
                'no_hp'          => '0812-3456-7890',
                'plat_nomor'     => 'B 1234 ABC',
                'tipe_motor'     => 'Vario 150',
                'tahun'          => '2021',
                'alamat'         => 'Jl. Merdeka No. 10, Jakarta Pusat',
            ],
            [
                'kode_pelanggan' => 'PLG-002',
                'nama'           => 'Budi Wicaksono',
                'no_hp'          => '0813-9876-5432',
                'plat_nomor'     => 'B 5678 XYZ',
                'tipe_motor'     => 'Beat Street',
                'tahun'          => '2023',
                'alamat'         => 'Jl. Sudirman No. 45, Jakarta Selatan',
            ],
            [
                'kode_pelanggan' => 'PLG-003',
                'nama'           => 'Citra Mandasari',
                'no_hp'          => '0857-1234-5678',
                'plat_nomor'     => 'D 4321 DEF',
                'tipe_motor'     => 'Scoopy Prestige',
                'tahun'          => '2022',
                'alamat'         => 'Jl. Anggrek No. 12, Bandung',
            ],
            [
                'kode_pelanggan' => 'PLG-004',
                'nama'           => 'Dian Pratama',
                'no_hp'          => '0821-4455-6677',
                'plat_nomor'     => 'B 8899 GHI',
                'tipe_motor'     => 'NMAX 155',
                'tahun'          => '2020',
                'alamat'         => 'Jl. Kemang Raya No. 8, Jakarta Selatan',
            ],
            [
                'kode_pelanggan' => 'PLG-005',
                'nama'           => 'Eka Ramadhani',
                'no_hp'          => '0878-9988-7766',
                'plat_nomor'     => 'F 2233 JKL',
                'tipe_motor'     => 'Aerox 155',
                'tahun'          => '2022',
                'alamat'         => 'Jl. Pajajaran No. 23, Bogor',
            ],
            [
                'kode_pelanggan' => 'PLG-006',
                'nama'           => 'Fajar Kurniawan',
                'no_hp'          => '0819-3322-1100',
                'plat_nomor'     => 'B 6677 MNO',
                'tipe_motor'     => 'PCX 160',
                'tahun'          => '2023',
                'alamat'         => 'Jl. Fatmawati No. 5, Jakarta Selatan',
            ],
            [
                'kode_pelanggan' => 'PLG-007',
                'nama'           => 'Gita Lestari',
                'no_hp'          => '0852-6677-8899',
                'plat_nomor'     => 'B 9900 PQR',
                'tipe_motor'     => 'Mio M3',
                'tahun'          => '2019',
                'alamat'         => 'Jl. Cilandak No. 15, Jakarta Selatan',
            ],
        ];

        $plgModels = [];
        foreach ($pelangganList as $p) {
            $plgModels[$p['kode_pelanggan']] = Pelanggan::updateOrCreate(
                ['kode_pelanggan' => $p['kode_pelanggan']],
                $p
            );
        }

        // 6. SEED JASA / LAYANAN (7 layanan bengkel)
        $jasaList = [
            [
                'kode_layanan' => 'SRV-01',
                'nama_layanan' => 'Servis Ringan / Tune Up',
                'biaya'        => 45000,
                'durasi'       => '30 Menit',
                'keterangan'   => 'Pembersihan karbu/injeksi, filter udara, cek busi, setel gas & rem',
                'status'       => 'Tersedia',
            ],
            [
                'kode_layanan' => 'SRV-02',
                'nama_layanan' => 'Ganti Oli Mesin & Gardan',
                'biaya'        => 10000,
                'durasi'       => '15 Menit',
                'keterangan'   => 'Jasa kuras dan isi oli mesin serta transmisi gardan matic',
                'status'       => 'Tersedia',
            ],
            [
                'kode_layanan' => 'SRV-03',
                'nama_layanan' => 'Servis CVT Lengkap',
                'biaya'        => 65000,
                'durasi'       => '45 Menit',
                'keterangan'   => 'Bongkar pulley CVT, pembersihan mangkok & roller, lumas gemuk high-temp',
                'status'       => 'Tersedia',
            ],
            [
                'kode_layanan' => 'SRV-04',
                'nama_layanan' => 'Bongkar Pasang Ban Tubeless',
                'biaya'        => 20000,
                'durasi'       => '20 Menit',
                'keterangan'   => 'Jasa lepas & pasang ban tubeless menggunakan tyre changer mesin',
                'status'       => 'Tersedia',
            ],
            [
                'kode_layanan' => 'SRV-05',
                'nama_layanan' => 'Ganti Kampas Rem Depan / Belakang',
                'biaya'        => 15000,
                'durasi'       => '15 Menit',
                'keterangan'   => 'Jasa pasang kampas rem baru & setting jarak bebas handle rem',
                'status'       => 'Tersedia',
            ],
            [
                'kode_layanan' => 'SRV-06',
                'nama_layanan' => 'Kuras & Ganti Minyak Rem Cakram',
                'biaya'        => 25000,
                'durasi'       => '20 Menit',
                'keterangan'   => 'Bleeding minyak rem DOT 4 sistem hidrolik cakram depan/belakang',
                'status'       => 'Tersedia',
            ],
            [
                'kode_layanan' => 'SRV-07',
                'nama_layanan' => 'Overhaul / Turun Mesin',
                'biaya'        => 350000,
                'durasi'       => '180 Menit',
                'keterangan'   => 'Turun mesin penuh, pembersihan ruang bakar, pasang piston baru & skir klep',
                'status'       => 'Tersedia',
            ],
        ];

        $jasaModels = [];
        foreach ($jasaList as $j) {
            $jasaModels[$j['kode_layanan']] = Jasa::updateOrCreate(
                ['kode_layanan' => $j['kode_layanan']],
                $j
            );
        }

        // 7. SEED PEMBELIAN (BARANG MASUK - 5 transaksi)
        $pembelianList = [
            [
                'kode_masuk'  => 'BM-001',
                'tanggal'     => '2026-10-03',
                'supplier_id' => $supModels['SPL-01']->id,
                'user_id'     => $admin1->id,
                'no_faktur'   => 'SJ-202610-089',
                'total_harga' => 1250000,
                'catatan'     => 'Kiriman stok oli mesin dan gardan mingguan',
                'items'       => [
                    ['barang_id' => $brgModels['BRG-001']->id, 'qty' => 20, 'harga_beli' => 42000],
                    ['barang_id' => $brgModels['BRG-004']->id, 'qty' => 30, 'harga_beli' => 12000],
                ]
            ],
            [
                'kode_masuk'  => 'BM-002',
                'tanggal'     => '2026-09-29',
                'supplier_id' => $supModels['SPL-01']->id,
                'user_id'     => $admin1->id,
                'no_faktur'   => 'SJ-202609-112',
                'total_harga' => 850000,
                'catatan'     => 'Pasokan kampas rem depan dan belakang',
                'items'       => [
                    ['barang_id' => $brgModels['BRG-002']->id, 'qty' => 15, 'harga_beli' => 30000],
                    ['barang_id' => $brgModels['BRG-010']->id, 'qty' => 14, 'harga_beli' => 28000],
                ]
            ],
            [
                'kode_masuk'  => 'BM-003',
                'tanggal'     => '2026-09-15',
                'supplier_id' => $supModels['SPL-02']->id,
                'user_id'     => $admin1->id,
                'no_faktur'   => 'FACT-8812',
                'total_harga' => 2100000,
                'catatan'     => 'Aki motor kering dan busi standar NGK',
                'items'       => [
                    ['barang_id' => $brgModels['BRG-003']->id, 'qty' => 40, 'harga_beli' => 18000],
                    ['barang_id' => $brgModels['BRG-005']->id, 'qty' => 7,  'harga_beli' => 185000],
                ]
            ],
            [
                'kode_masuk'  => 'BM-004',
                'tanggal'     => '2026-09-01',
                'supplier_id' => $supModels['SPL-03']->id,
                'user_id'     => $admin1->id,
                'no_faktur'   => 'AST-9921',
                'total_harga' => 3400000,
                'catatan'     => 'Restock resmi sparepart CVT & filter udara Astra',
                'items'       => [
                    ['barang_id' => $brgModels['BRG-007']->id, 'qty' => 20, 'harga_beli' => 75000],
                    ['barang_id' => $brgModels['BRG-008']->id, 'qty' => 30, 'harga_beli' => 35000],
                ]
            ],
            [
                'kode_masuk'  => 'BM-005',
                'tanggal'     => '2026-08-20',
                'supplier_id' => $supModels['SPL-04']->id,
                'user_id'     => $admin1->id,
                'no_faktur'   => 'CST-4521',
                'total_harga' => 1800000,
                'catatan'     => 'Restock oli Castrol Power 1 & ban tubeless FDR',
                'items'       => [
                    ['barang_id' => $brgModels['BRG-006']->id, 'qty' => 10, 'harga_beli' => 165000],
                ]
            ],
        ];

        foreach ($pembelianList as $pm) {
            $items = $pm['items'];
            unset($pm['items']);

            $pemModel = Pembelian::updateOrCreate(['kode_masuk' => $pm['kode_masuk']], $pm);

            $pemModel->details()->delete();
            foreach ($items as $it) {
                $it['subtotal'] = $it['qty'] * $it['harga_beli'];
                $pemModel->details()->create($it);
            }
        }

        // 8. SEED PENJUALAN (TRANSAKSI KASIR - 8 transaksi)
        $penjualanList = [
            [
                'no_nota'           => '#TRX-20261015-001',
                'tanggal'          => '2026-10-15 14:20:00',
                'pelanggan_id'     => $plgModels['PLG-001']->id,
                'user_id'          => $kasir1->id,
                'subtotal'         => 85000,
                'total'            => 85000,
                'metode_pembayaran'=> 'Tunai',
                'status'           => 'Lunas',
                'catatan'          => 'Servis ringan & ganti oli',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-001']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Oli Mesin MPX2 0.8L',
                        'qty'       => 1,
                        'harga'     => 52000,
                    ],
                    [
                        'tipe'      => 'jasa',
                        'barang_id' => null,
                        'jasa_id'   => $jasaModels['SRV-01']->id,
                        'nama_item' => 'Servis Ringan / Tune Up',
                        'qty'       => 1,
                        'harga'     => 45000,
                    ],
                ]
            ],
            [
                'no_nota'           => '#TRX-20261015-002',
                'tanggal'          => '2026-10-15 15:45:00',
                'pelanggan_id'     => $plgModels['PLG-002']->id,
                'user_id'          => $kasir1->id,
                'subtotal'         => 120000,
                'total'            => 120000,
                'metode_pembayaran'=> 'QRIS',
                'status'           => 'Lunas',
                'catatan'          => 'Ganti kampas rem & kuras minyak rem',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-002']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Kampas Rem Depan Vario / Beat',
                        'qty'       => 1,
                        'harga'     => 45000,
                    ],
                    [
                        'tipe'      => 'jasa',
                        'barang_id' => null,
                        'jasa_id'   => $jasaModels['SRV-05']->id,
                        'nama_item' => 'Ganti Kampas Rem Depan',
                        'qty'       => 1,
                        'harga'     => 15000,
                    ],
                    [
                        'tipe'      => 'jasa',
                        'barang_id' => null,
                        'jasa_id'   => $jasaModels['SRV-06']->id,
                        'nama_item' => 'Kuras & Ganti Minyak Rem',
                        'qty'       => 1,
                        'harga'     => 25000,
                    ],
                ]
            ],
            [
                'no_nota'           => '#TRX-20261015-003',
                'tanggal'          => '2026-10-15 16:10:00',
                'pelanggan_id'     => $plgModels['PLG-003']->id,
                'user_id'          => $kasir3->id,
                'subtotal'         => 69000,
                'total'            => 69000,
                'metode_pembayaran'=> 'Tunai',
                'status'           => 'Lunas',
                'catatan'          => 'Ganti oli mesin & oli gardan',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-001']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Oli Mesin MPX2 0.8L',
                        'qty'       => 1,
                        'harga'     => 52000,
                    ],
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-004']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Oli Gardan Matic Yamalube',
                        'qty'       => 1,
                        'harga'     => 17000,
                    ],
                ]
            ],
            [
                'no_nota'           => '#TRX-20261015-004',
                'tanggal'          => '2026-10-15 17:00:00',
                'pelanggan_id'     => $plgModels['PLG-004']->id,
                'user_id'          => $kasir1->id,
                'subtotal'         => 245000,
                'total'            => 245000,
                'metode_pembayaran'=> 'Transfer',
                'status'           => 'Lunas',
                'catatan'          => 'Ganti aki GS Astra',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-005']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Aki Kering GS Astra GTZ5S',
                        'qty'       => 1,
                        'harga'     => 230000,
                    ],
                    [
                        'tipe'      => 'jasa',
                        'barang_id' => null,
                        'jasa_id'   => $jasaModels['SRV-05']->id,
                        'nama_item' => 'Jasa Pemasangan Aki',
                        'qty'       => 1,
                        'harga'     => 15000,
                    ],
                ]
            ],
            [
                'no_nota'           => '#TRX-20261015-005',
                'tanggal'          => '2026-10-15 17:35:00',
                'pelanggan_id'     => $plgModels['PLG-005']->id,
                'user_id'          => $kasir4->id,
                'subtotal'         => 75000,
                'total'            => 75000,
                'metode_pembayaran'=> 'Tunai',
                'status'           => 'Lunas',
                'catatan'          => 'Ganti busi & filter udara',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-003']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Busi Standar NGK',
                        'qty'       => 1,
                        'harga'     => 25000,
                    ],
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-008']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Filter Udara Vario',
                        'qty'       => 1,
                        'harga'     => 50000,
                    ],
                ]
            ],
            [
                'no_nota'           => '#TRX-20261015-006',
                'tanggal'          => '2026-10-15 18:20:00',
                'pelanggan_id'     => $plgModels['PLG-006']->id,
                'user_id'          => $kasir1->id,
                'subtotal'         => 170000,
                'total'            => 170000,
                'metode_pembayaran'=> 'QRIS',
                'status'           => 'Lunas',
                'catatan'          => 'Servis CVT dan ganti roller',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-009']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Roller CVT TDR Racing 10gr',
                        'qty'       => 1,
                        'harga'     => 85000,
                    ],
                    [
                        'tipe'      => 'jasa',
                        'barang_id' => null,
                        'jasa_id'   => $jasaModels['SRV-03']->id,
                        'nama_item' => 'Servis CVT Lengkap',
                        'qty'       => 1,
                        'harga'     => 65000,
                    ],
                ]
            ],
            [
                'no_nota'           => '#TRX-20261016-001',
                'tanggal'          => '2026-10-16 09:30:00',
                'pelanggan_id'     => $plgModels['PLG-007']->id,
                'user_id'          => $kasir1->id,
                'subtotal'         => 95000,
                'total'            => 95000,
                'metode_pembayaran'=> 'Tunai',
                'status'           => 'Lunas',
                'catatan'          => 'Ganti kampas rem belakang tromol',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-010']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Kampas Rem Belakang Tromol Honda',
                        'qty'       => 1,
                        'harga'     => 40000,
                    ],
                    [
                        'tipe'      => 'jasa',
                        'barang_id' => null,
                        'jasa_id'   => $jasaModels['SRV-01']->id,
                        'nama_item' => 'Servis Ringan / Tune Up',
                        'qty'       => 1,
                        'harga'     => 45000,
                    ],
                ]
            ],
            [
                'no_nota'           => '#TRX-20261016-002',
                'tanggal'          => '2026-10-16 11:15:00',
                'pelanggan_id'     => $plgModels['PLG-001']->id,
                'user_id'          => $kasir5->id,
                'subtotal'         => 230000,
                'total'            => 230000,
                'metode_pembayaran'=> 'QRIS',
                'status'           => 'Lunas',
                'catatan'          => 'Ganti ban luar tubeless FDR',
                'details'          => [
                    [
                        'tipe'      => 'barang',
                        'barang_id' => $brgModels['BRG-006']->id,
                        'jasa_id'   => null,
                        'nama_item' => 'Ban Luar Tubeless FDR 90/90-14',
                        'qty'       => 1,
                        'harga'     => 210000,
                    ],
                    [
                        'tipe'      => 'jasa',
                        'barang_id' => null,
                        'jasa_id'   => $jasaModels['SRV-04']->id,
                        'nama_item' => 'Bongkar Pasang Ban Tubeless',
                        'qty'       => 1,
                        'harga'     => 20000,
                    ],
                ]
            ],
        ];

        foreach ($penjualanList as $pj) {
            $details = $pj['details'];
            unset($pj['details']);

            $pjModel = Penjualan::updateOrCreate(['no_nota' => $pj['no_nota']], $pj);

            $pjModel->details()->delete();
            foreach ($details as $d) {
                $d['subtotal'] = $d['qty'] * $d['harga'];
                $pjModel->details()->create($d);
            }
        }

        // 9. SEED STOK OPNAME (5 sesi audit stok)
        $opnameList = [
            [
                'kode_opname' => 'OPN-20261001-001',
                'tanggal'     => '2026-10-01',
                'supplier_id' => $supModels['SPL-01']->id,
                'user_id'     => $admin1->id,
                'kategori'    => 'Semua',
                'catatan'     => 'Audit berkala awal bulan Oktober',
                'details'     => [
                    ['barang_id' => $brgModels['BRG-001']->id, 'stok_sistem' => 14, 'stok_fisik' => 9,  'selisih' => -5, 'catatan' => 'Bocor saat bongkar muat'],
                    ['barang_id' => $brgModels['BRG-002']->id, 'stok_sistem' => 100, 'stok_fisik' => 100, 'selisih' => 0,  'catatan' => 'Sesuai'],
                    ['barang_id' => $brgModels['BRG-003']->id, 'stok_sistem' => 52, 'stok_fisik' => 50,  'selisih' => -2, 'catatan' => 'Pecah elektroda'],
                ]
            ],
            [
                'kode_opname' => 'OPN-20260915-001',
                'tanggal'     => '2026-09-15',
                'supplier_id' => $supModels['SPL-01']->id,
                'user_id'     => $admin1->id,
                'kategori'    => 'Oli',
                'catatan'     => 'Pemeriksaan stok fisik oli mesin & gardan',
                'details'     => [
                    ['barang_id' => $brgModels['BRG-001']->id, 'stok_sistem' => 20, 'stok_fisik' => 20, 'selisih' => 0, 'catatan' => 'Sesuai'],
                    ['barang_id' => $brgModels['BRG-004']->id, 'stok_sistem' => 5,  'stok_fisik' => 4,  'selisih' => -1, 'catatan' => 'Dipakai untuk test motor'],
                ]
            ],
            [
                'kode_opname' => 'OPN-20260901-001',
                'tanggal'     => '2026-09-01',
                'supplier_id' => $supModels['SPL-02']->id,
                'user_id'     => $admin1->id,
                'kategori'    => 'Sparepart',
                'catatan'     => 'Audit stok kampas rem dan busi',
                'details'     => [
                    ['barang_id' => $brgModels['BRG-002']->id, 'stok_sistem' => 80, 'stok_fisik' => 80, 'selisih' => 0, 'catatan' => 'Sesuai'],
                    ['barang_id' => $brgModels['BRG-010']->id, 'stok_sistem' => 24, 'stok_fisik' => 22, 'selisih' => -2, 'catatan' => 'Kardus rusak'],
                ]
            ],
            [
                'kode_opname' => 'OPN-20260815-001',
                'tanggal'     => '2026-08-15',
                'supplier_id' => $supModels['SPL-04']->id,
                'user_id'     => $admin1->id,
                'kategori'    => 'Ban',
                'catatan'     => 'Pengecekan stok ban luar tubeless',
                'details'     => [
                    ['barang_id' => $brgModels['BRG-006']->id, 'stok_sistem' => 15, 'stok_fisik' => 15, 'selisih' => 0, 'catatan' => 'Sesuai'],
                ]
            ],
            [
                'kode_opname' => 'OPN-20260801-001',
                'tanggal'     => '2026-08-01',
                'supplier_id' => $supModels['SPL-03']->id,
                'user_id'     => $admin1->id,
                'kategori'    => 'Semua',
                'catatan'     => 'Audit stok fisik pertengahan tahun',
                'details'     => [
                    ['barang_id' => $brgModels['BRG-005']->id, 'stok_sistem' => 8,  'stok_fisik' => 8,  'selisih' => 0, 'catatan' => 'Sesuai'],
                    ['barang_id' => $brgModels['BRG-007']->id, 'stok_sistem' => 12, 'stok_fisik' => 12, 'selisih' => 0, 'catatan' => 'Sesuai'],
                    ['barang_id' => $brgModels['BRG-008']->id, 'stok_sistem' => 20, 'stok_fisik' => 20, 'selisih' => 0, 'catatan' => 'Sesuai'],
                ]
            ],
        ];

        foreach ($opnameList as $op) {
            $details = $op['details'];
            unset($op['details']);

            $opModel = Opname::updateOrCreate(['kode_opname' => $op['kode_opname']], $op);

            $opModel->details()->delete();
            foreach ($details as $od) {
                $opModel->details()->create($od);
            }
        }
    }
}
