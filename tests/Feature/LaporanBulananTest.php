<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanBulananTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $this->kasir = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_access_laporan_bulanan_and_see_chart_and_top_sellers(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
        $oli = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Oli Yamalube Silver',
            'satuan'      => 'Botol',
            'harga_jual'  => 45000,
            'stok'        => 20,
        ]);
        $busi = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Busi NGK CPR9EA',
            'satuan'      => 'Pcs',
            'harga_jual'  => 25000,
            'stok'        => 30,
        ]);

        // Transaksi di bulan Maret 2026
        $trxMaret = Penjualan::create([
            'no_transaksi'  => 'TRX-2026-03-01',
            'id_user'       => $this->kasir->id_user,
            'tanggal'       => Carbon::create(2026, 3, 15, 10, 0, 0),
            'total'         => 115000,
            'nominal_bayar' => 120000,
            'kembalian'     => 5000,
        ]);

        PenjualanDetail::create([
            'id_transaksi' => $trxMaret->id_transaksi,
            'jenis_item'   => 'barang',
            'id_barang'    => $oli->id_barang,
            'nama_item'    => $oli->nama_barang,
            'qty'          => 2,
            'harga_satuan' => 45000,
            'subtotal'     => 90000,
        ]);

        PenjualanDetail::create([
            'id_transaksi' => $trxMaret->id_transaksi,
            'jenis_item'   => 'barang',
            'id_barang'    => $busi->id_barang,
            'nama_item'    => $busi->nama_barang,
            'qty'          => 1,
            'harga_satuan' => 25000,
            'subtotal'     => 25000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan', [
            'jenis' => 'bulanan',
            'tahun' => 2026,
        ]));

        $response->assertOk();
        $response->assertSee('Grafik Penjualan & Barang Terlaris Bulanan');
        $response->assertSee('chartPenjualanBulanan');
        $response->assertSee('chartBarangLarisBulanan');
        $response->assertSee('Oli Yamalube Silver');
        $response->assertSee('Total Penjualan Tahun 2026');
        $response->assertSee('115.000');
    }

    public function test_kasir_can_access_laporan_bulanan_and_see_chart_and_top_sellers(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Sparepart']);
        $ban = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban Tubeless FDR 90/90-14',
            'satuan'      => 'Pcs',
            'harga_jual'  => 180000,
            'stok'        => 10,
        ]);

        // Transaksi di bulan Mei 2026
        $trxMei = Penjualan::create([
            'no_transaksi'  => 'TRX-2026-05-01',
            'id_user'       => $this->kasir->id_user,
            'tanggal'       => Carbon::create(2026, 5, 10, 14, 0, 0),
            'total'         => 360000,
            'nominal_bayar' => 400000,
            'kembalian'     => 40000,
        ]);

        PenjualanDetail::create([
            'id_transaksi' => $trxMei->id_transaksi,
            'jenis_item'   => 'barang',
            'id_barang'    => $ban->id_barang,
            'nama_item'    => $ban->nama_barang,
            'qty'          => 2,
            'harga_satuan' => 180000,
            'subtotal'     => 360000,
        ]);

        $response = $this->actingAs($this->kasir)->get(route('kasir.laporan', [
            'tipe'  => 'bulanan',
            'tahun' => 2026,
        ]));

        $response->assertOk();
        $response->assertSee('Grafik Penjualan & Barang Terlaris Bulanan');
        $response->assertSee('chartPenjualanBulanan');
        $response->assertSee('chartBarangLarisBulanan');
        $response->assertSee('Ban Tubeless FDR 90/90-14');
        $response->assertSee('360.000');
    }

    public function test_filter_bulan_displays_daily_dates_and_product_names(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Oli']);
        $oli = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Oli Yamalube Matic',
            'satuan'      => 'Botol',
            'harga_jual'  => 55000,
            'stok'        => 20,
        ]);

        // Transaksi tanggal 15 Januari 2026
        $trx = Penjualan::create([
            'no_transaksi'  => 'TRX-2026-01-15',
            'id_user'       => $this->kasir->id_user,
            'tanggal'       => Carbon::create(2026, 1, 15, 9, 0, 0),
            'total'         => 110000,
            'nominal_bayar' => 110000,
            'kembalian'     => 0,
        ]);

        PenjualanDetail::create([
            'id_transaksi' => $trx->id_transaksi,
            'jenis_item'   => 'barang',
            'id_barang'    => $oli->id_barang,
            'nama_item'    => $oli->nama_barang,
            'qty'          => 2,
            'harga_satuan' => 55000,
            'subtotal'     => 110000,
        ]);

        $request = new \Illuminate\Http\Request([
            'tahun' => 2026,
            'bulan_filter' => 1,
        ]);

        $data = \App\Support\LaporanBulananService::getData($request);

        $this->assertTrue($data['isFilterBulan']);
        $this->assertEquals(1, $data['bulanTerpilih']);
        $this->assertEquals('Januari', $data['namaBulanTerpilih']);

        // Assert chartLabels berisi tanggal: Tgl 1 .. Tgl 31
        $this->assertCount(31, $data['chartLabels']);
        $this->assertEquals('Tgl 1', $data['chartLabels'][0]);
        $this->assertEquals('Tgl 15', $data['chartLabels'][14]);
        $this->assertEquals(110000, $data['chartNominal'][14]);
        $this->assertEquals(1, $data['chartTransaksi'][14]);

        // Assert chartLarisLabels langsung menampilkan nama produk
        $this->assertContains('Oli Yamalube Matic', $data['chartLarisLabels']);
        $this->assertEquals(2, $data['chartLarisQty'][0]);

        // Assert lewat HTTP Response
        $response = $this->actingAs($this->admin)->get(route('admin.laporan', [
            'jenis' => 'bulanan',
            'tahun' => 2026,
            'bulan_filter' => 1,
        ]));

        $response->assertOk();
        $response->assertSee('Grafik Penjualan Harian - Bulan Januari 2026');
        $response->assertSee('Grafik Barang Paling Laris - Bulan Januari 2026');
        $response->assertSee('Tgl 15');
        $response->assertSee('Oli Yamalube Matic');
        // Pastikan teks emoji lama dan box rincian juara tiap bulan tidak ada
        $response->assertDontSee('🏷️ Rincian Produk Juara Tiap Bulan');
    }
}
