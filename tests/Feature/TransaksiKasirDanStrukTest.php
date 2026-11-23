<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiKasirDanStrukTest extends TestCase
{
    use RefreshDatabase;

    private function createKasir(string $nama = 'Kasir Budi'): User
    {
        return User::factory()->create([
            'nama' => $nama,
            'role' => 'kasir',
            'status' => 'aktif',
        ]);
    }

    private function createAdmin(string $nama = 'Admin Utama'): User
    {
        return User::factory()->create([
            'nama' => $nama,
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }

    public function test_halaman_kasir_menampilkan_stok_sesuai_database(): void
    {
        $kasir = $this->createKasir();
        $kategori = Kategori::create(['nama_kategori' => 'Oli Mesin']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Oli Enduro 4T Racing',
            'satuan' => 'Botol',
            'harga_jual' => 55000,
            'stok' => 15,
        ]);

        $response = $this->actingAs($kasir)->get(route('kasir.transaksi'));
        $response->assertOk();
        $response->assertSee('Oli Enduro 4T Racing');
        $response->assertSee('Tersedia (15)');

        // Ubah stok di DB lalu muat ulang, nilai ikut berubah
        $barang->update(['stok' => 8]);
        $responseUpdated = $this->actingAs($kasir)->get(route('kasir.transaksi'));
        $responseUpdated->assertOk();
        $responseUpdated->assertSee('Menipis (8)');

        // Ubah stok menjadi 0
        $barang->update(['stok' => 0]);
        $responseHabis = $this->actingAs($kasir)->get(route('kasir.transaksi'));
        $responseHabis->assertOk();
        $responseHabis->assertSee('Habis (0)');
    }

    public function test_endpoint_kasir_transaksi_stok_mengembalikan_json_dan_butuh_login(): void
    {
        // Guest harus dialihkan ke login
        $guestResponse = $this->get(route('kasir.transaksi.stok'));
        $guestResponse->assertRedirect(route('login'));

        $kasir = $this->createKasir();
        $kategori = Kategori::create(['nama_kategori' => 'Ban']);
        $b1 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban IRC 80/90-14',
            'satuan' => 'Pcs',
            'harga_jual' => 165000,
            'stok' => 20,
        ]);
        $b2 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban Dalam Swallow 14',
            'satuan' => 'Pcs',
            'harga_jual' => 35000,
            'stok' => 5,
        ]);

        $response = $this->actingAs($kasir)->getJson(route('kasir.transaksi.stok'));
        $response->assertOk();
        $response->assertHeader('Cache-Control');
        $response->assertJsonFragment([
            'id_barang' => $b1->id_barang,
            'stok' => 20,
        ]);
        $response->assertJsonFragment([
            'id_barang' => $b2->id_barang,
            'stok' => 5,
        ]);

        // Pastikan endpoint ini read-only dan tidak mengubah data apa pun
        $this->assertEquals(20, $b1->fresh()->stok);
        $this->assertEquals(5, $b2->fresh()->stok);
    }

    public function test_rute_struk_memuat_informasi_lengkap_dan_otorisasi(): void
    {
        $kasir1 = $this->createKasir('Kasir Satu');
        $kasir2 = $this->createKasir('Kasir Dua');
        $admin = $this->createAdmin('Admin Bos');

        $kategori = Kategori::create(['nama_kategori' => 'Busi']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Busi NGK Iridium',
            'satuan' => 'Pcs',
            'harga_jual' => 45000,
            'stok' => 10,
        ]);

        $jasa = Jasa::create([
            'nama_jasa' => 'Servis Ringan + Pasang',
            'tarif' => 15000,
        ]);

        // Buat transaksi milik kasir 1
        $penjualan = Penjualan::create([
            'no_transaksi' => 'TRX-20261119-0099',
            'id_user' => $kasir1->id_user,
            'tanggal' => now(),
            'total' => 60000,
            'nominal_bayar' => 100000,
            'kembalian' => 40000,
        ]);

        PenjualanDetail::create([
            'id_transaksi' => $penjualan->id_transaksi,
            'jenis_item' => 'barang',
            'id_barang' => $barang->id_barang,
            'nama_item' => 'Busi NGK Iridium Snapshot',
            'harga_satuan' => 45000,
            'qty' => 1,
            'subtotal' => 45000,
        ]);

        PenjualanDetail::create([
            'id_transaksi' => $penjualan->id_transaksi,
            'jenis_item' => 'jasa',
            'id_jasa' => $jasa->id_jasa,
            'nama_item' => 'Servis Ringan Snapshot',
            'harga_satuan' => 15000,
            'qty' => 1,
            'subtotal' => 15000,
        ]);

        // 1. Guest dialihkan ke login
        $responseGuest = $this->get(route('kasir.transaksi.struk', $penjualan->id_transaksi));
        $responseGuest->assertRedirect(route('login'));

        // 2. Kasir 1 (pemilik transaksi) boleh mencetak struknya
        $responseKasir1 = $this->actingAs($kasir1)->get(route('kasir.transaksi.struk', $penjualan->id_transaksi));
        $responseKasir1->assertOk();
        $responseKasir1->assertSee('#TRX-20261119-0099');
        $responseKasir1->assertSee('Kasir Satu');
        $responseKasir1->assertSee('Busi NGK Iridium Snapshot');
        $responseKasir1->assertSee('[JASA] Servis Ringan Snapshot');
        $responseKasir1->assertSee('45.000');
        $responseKasir1->assertSee('15.000');
        $responseKasir1->assertSee('60.000');
        $responseKasir1->assertSee('100.000');
        $responseKasir1->assertSee('40.000');
        $responseKasir1->assertSee('TOTAL');
        $responseKasir1->assertSee('TUNAI');
        $responseKasir1->assertSee('KEMBALI');
        $responseKasir1->assertSee('Total Item');

        // 3. Kasir 2 (bukan pemilik) dilarang mencetak struk kasir 1 (403 Forbidden)
        $responseKasir2 = $this->actingAs($kasir2)->get(route('kasir.transaksi.struk', $penjualan->id_transaksi));
        $responseKasir2->assertForbidden();

        // 4. Admin boleh mencetak struk transaksi siapa pun melalui rute admin
        $responseAdmin = $this->actingAs($admin)->get(route('admin.riwayat.struk', $penjualan->id_transaksi));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('#TRX-20261119-0099');
    }

    public function test_struk_berukuran_tetap_80mm_tanpa_opsi_58mm(): void
    {
        $kasir = $this->createKasir();
        $penjualan = Penjualan::create([
            'no_transaksi' => 'TRX-TEST-WIDTH',
            'id_user' => $kasir->id_user,
            'tanggal' => now(),
            'total' => 50000,
            'nominal_bayar' => 50000,
            'kembalian' => 0,
        ]);

        $response = $this->actingAs($kasir)->get(route('kasir.transaksi.struk', $penjualan->id_transaksi));
        $response->assertOk();
        $response->assertSee('80mm auto');
        $response->assertSee('72mm');
        $response->assertDontSee('58mm');
    }

    public function test_struk_memakai_snapshot_data_transaksi_bukan_master_terbaru(): void
    {
        $kasir = $this->createKasir();
        $kategori = Kategori::create(['nama_kategori' => 'Gear']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Gear Set SSS 428',
            'satuan' => 'Set',
            'harga_jual' => 200000,
            'stok' => 5,
        ]);

        // Simpan transaksi kasir
        $this->actingAs($kasir)->post(route('kasir.transaksi.store'), [
            'nominal_bayar' => 200000,
            'items' => [
                [
                    'jenis' => 'barang',
                    'id' => $barang->id_barang,
                    'qty' => 1,
                ],
            ],
        ]);

        $penjualan = Penjualan::latest('id_transaksi')->first();

        // Admin kemudian mengubah nama dan menaikkan harga master barang menjadi Rp 250.000
        $barang->update([
            'nama_barang' => 'Gear Set SSS Gold Edition',
            'harga_jual' => 250000,
        ]);

        // Struk yang dicetak HARUS tetap memakai nama lama dan harga snapshot lama (Rp 200.000)
        $response = $this->actingAs($kasir)->get(route('kasir.transaksi.struk', $penjualan->id_transaksi));
        $response->assertOk();
        $response->assertSee('Gear Set SSS 428');
        $response->assertDontSee('Gear Set SSS Gold Edition');
        $response->assertSee('200.000');
    }

    public function test_transaksi_berhasil_menyimpan_flash_data_untuk_modal_dan_struk(): void
    {
        $kasir = $this->createKasir();
        $kategori = Kategori::create(['nama_kategori' => 'Bohlam']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Bohlam Depan Osram',
            'satuan' => 'Pcs',
            'harga_jual' => 25000,
            'stok' => 10,
        ]);

        $response = $this->actingAs($kasir)->post(route('kasir.transaksi.store'), [
            'nominal_bayar' => 50000,
            'items' => [
                [
                    'jenis' => 'barang',
                    'id' => $barang->id_barang,
                    'qty' => 1,
                ],
            ],
        ]);

        $response->assertRedirect(route('kasir.transaksi'));
        $response->assertSessionHas('status');
        $response->assertSessionHas('transaksi_sukses');

        $flash = session('transaksi_sukses');
        $this->assertIsArray($flash);
        $this->assertEquals(25000, $flash['total']);
        $this->assertEquals(50000, $flash['nominal_bayar']);
        $this->assertEquals(25000, $flash['kembalian']);
        $this->assertStringContainsString('/kasir/transaksi/', $flash['struk_url']);
    }
}
