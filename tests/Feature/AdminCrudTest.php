<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Jasa;
use App\Models\Kategori;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_crud_pengguna(): void
    {
        // 1. Create
        $response = $this->actingAs($this->admin)->post(route('admin.pengguna.store'), [
            'nama' => 'Staff Baru',
            'email' => 'staffbaru@gmail.com',
            'role' => 'kasir',
            'status' => 'aktif',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect(route('admin.pengguna'));
        $this->assertDatabaseHas('ms_users', [
            'nama' => 'Staff Baru',
            'email' => 'staffbaru@gmail.com',
            'role' => 'kasir',
        ]);

        $user = User::where('email', 'staffbaru@gmail.com')->first();
        $this->assertNotNull($user);

        // 2. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.pengguna.update', $user), [
            'nama' => 'Staff Diperbarui',
            'email' => 'staffbaru@gmail.com',
            'role' => 'kasir',
            'status' => 'nonaktif',
        ]);
        $updateResponse->assertRedirect(route('admin.pengguna'));
        $this->assertDatabaseHas('ms_users', [
            'id_user' => $user->id_user,
            'nama' => 'Staff Diperbarui',
            'status' => 'nonaktif',
        ]);

        // 3. Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.pengguna.destroy', $user));
        $deleteResponse->assertRedirect(route('admin.pengguna'));
        $this->assertDatabaseMissing('ms_users', [
            'id_user' => $user->id_user,
        ]);

        // 4. Admin cannot delete self
        $selfDelete = $this->actingAs($this->admin)->delete(route('admin.pengguna.destroy', $this->admin));
        $selfDelete->assertRedirect(route('admin.pengguna'));
        $this->assertDatabaseHas('ms_users', [
            'id_user' => $this->admin->id_user,
        ]);

        // 5. Admin cannot deactivate self
        $selfDeactivate = $this->actingAs($this->admin)->put(route('admin.pengguna.update', $this->admin), [
            'nama' => $this->admin->nama,
            'email' => $this->admin->email,
            'role' => $this->admin->role,
            'status' => 'nonaktif',
        ]);
        $selfDeactivate->assertSessionHasErrors();
        $this->assertEquals('aktif', $this->admin->fresh()->status);
    }

    public function test_admin_can_crud_kategori(): void
    {
        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Oli Mesin',
        ]);
        $response->assertRedirect(route('admin.kategori'));
        $this->assertDatabaseHas('ms_kategori', ['nama_kategori' => 'Oli Mesin']);

        $kategori = Kategori::where('nama_kategori', 'Oli Mesin')->first();

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.kategori.update', $kategori), [
            'nama_kategori' => 'Pelumas Mesin',
        ]);
        $updateResponse->assertRedirect(route('admin.kategori'));
        $this->assertDatabaseHas('ms_kategori', ['nama_kategori' => 'Pelumas Mesin']);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategori));
        $deleteResponse->assertRedirect(route('admin.kategori'));
        $this->assertDatabaseMissing('ms_kategori', ['id_kategori' => $kategori->id_kategori]);
    }

    public function test_admin_can_crud_supplier(): void
    {
        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.supplier.store'), [
            'nama_supplier' => 'PT Astra Honda Motor',
            'no_telepon' => '081234567890',
            'alamat' => 'Jakarta Barat',
        ]);
        $response->assertRedirect(route('admin.supplier'));
        $this->assertDatabaseHas('ms_suppliier', ['nama_supplier' => 'PT Astra Honda Motor']);

        $supplier = Supplier::where('nama_supplier', 'PT Astra Honda Motor')->first();

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.supplier.update', $supplier), [
            'nama_supplier' => 'PT Astra Honda Motor Tbk',
            'no_telepon' => '081299998888',
            'alamat' => 'Jakarta Utara',
        ]);
        $updateResponse->assertRedirect(route('admin.supplier'));
        $this->assertDatabaseHas('ms_suppliier', [
            'id_supplier' => $supplier->id_supplier,
            'nama_supplier' => 'PT Astra Honda Motor Tbk',
        ]);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.supplier.destroy', $supplier));
        $deleteResponse->assertRedirect(route('admin.supplier'));
        $this->assertDatabaseMissing('ms_suppliier', ['id_supplier' => $supplier->id_supplier]);
    }

    public function test_admin_can_crud_layanan(): void
    {
        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.layanan.store'), [
            'nama_jasa' => 'Servis CVT Lengkap',
            'tarif' => 45000,
        ]);
        $response->assertRedirect(route('admin.layanan'));
        $this->assertDatabaseHas('ms_jasa', [
            'nama_jasa' => 'Servis CVT Lengkap',
            'tarif' => 45000,
        ]);

        $jasa = Jasa::where('nama_jasa', 'Servis CVT Lengkap')->first();

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.layanan.update', $jasa), [
            'nama_jasa' => 'Servis CVT + Pembersihan',
            'tarif' => 50000,
        ]);
        $updateResponse->assertRedirect(route('admin.layanan'));
        $this->assertDatabaseHas('ms_jasa', [
            'id_jasa' => $jasa->id_jasa,
            'nama_jasa' => 'Servis CVT + Pembersihan',
            'tarif' => 50000,
        ]);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.layanan.destroy', $jasa));
        $deleteResponse->assertRedirect(route('admin.layanan'));
        $this->assertDatabaseMissing('ms_jasa', ['id_jasa' => $jasa->id_jasa]);
    }

    public function test_admin_can_crud_pelanggan(): void
    {
        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.pelanggan.store'), [
            'nama' => 'Budi Santoso',
            'plat_nomor' => 'B 1234 ABC',
            'no_telepon' => '081233445566',
        ]);
        $response->assertRedirect(route('admin.pelanggan'));
        $this->assertDatabaseHas('ms_pelanggan', [
            'nama' => 'Budi Santoso',
            'plat_nomor' => 'B 1234 ABC',
        ]);

        $pelanggan = Pelanggan::where('nama', 'Budi Santoso')->first();

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.pelanggan.update', $pelanggan), [
            'nama' => 'Budi Santoso S.T.',
            'plat_nomor' => 'B 1234 XYZ',
            'no_telepon' => '081233445577',
        ]);
        $updateResponse->assertRedirect(route('admin.pelanggan'));
        $this->assertDatabaseHas('ms_pelanggan', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nama' => 'Budi Santoso S.T.',
            'plat_nomor' => 'B 1234 XYZ',
        ]);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.pelanggan.destroy', $pelanggan));
        $deleteResponse->assertRedirect(route('admin.pelanggan'));
        $this->assertDatabaseMissing('ms_pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);
    }

    public function test_layanan_input_validation(): void
    {
        // 1. Required validation
        $response = $this->actingAs($this->admin)->post(route('admin.layanan.store'), [
            'nama_jasa' => '',
            'tarif' => '',
        ]);
        $response->assertSessionHasErrors(['nama_jasa', 'tarif']);

        // 2. Min character validation
        $responseMin = $this->actingAs($this->admin)->post(route('admin.layanan.store'), [
            'nama_jasa' => 'AB',
            'tarif' => -100,
        ]);
        $responseMin->assertSessionHasErrors(['nama_jasa', 'tarif']);

        // 3. Unique validation
        Jasa::create(['nama_jasa' => 'Tune Up Injeksi', 'tarif' => 60000]);
        $responseUnique = $this->actingAs($this->admin)->post(route('admin.layanan.store'), [
            'nama_jasa' => 'Tune Up Injeksi',
            'tarif' => 65000,
        ]);
        $responseUnique->assertSessionHasErrors(['nama_jasa']);
    }

    public function test_pelanggan_input_validation(): void
    {
        // 1. Required validation
        $response = $this->actingAs($this->admin)->post(route('admin.pelanggan.store'), [
            'nama' => '',
        ]);
        $response->assertSessionHasErrors(['nama']);

        // 2. Min length validation
        $responseMin = $this->actingAs($this->admin)->post(route('admin.pelanggan.store'), [
            'nama' => 'A',
            'no_telepon' => '123',
        ]);
        $responseMin->assertSessionHasErrors(['nama', 'no_telepon']);

        // 3. Uppercase conversion and trimming
        $this->actingAs($this->admin)->post(route('admin.pelanggan.store'), [
            'nama' => '  Pak Joko  ',
            'plat_nomor' => 'b 9999 zzz',
            'no_telepon' => '081298765432',
        ]);
        $this->assertDatabaseHas('ms_pelanggan', [
            'nama' => 'Pak Joko',
            'plat_nomor' => 'B 9999 ZZZ',
        ]);
    }

    public function test_admin_can_crud_barang(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Ban Motor']);

        // Create: Aturan bisnis baru: stok awal selalu otomatis 0
        $response = $this->actingAs($this->admin)->post(route('admin.barang.store'), [
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban Luar Tubeless 90/90-14',
            'satuan' => 'Pcs',
            'harga_jual' => 195000,
            'stok' => 15, // Input stok bebas harus diabaikan / dipaksa menjadi 0
        ]);
        $response->assertRedirect(route('admin.barang'));
        $this->assertDatabaseHas('ms_barang', [
            'nama_barang' => 'Ban Luar Tubeless 90/90-14',
            'stok' => 0,
            'harga_jual' => 195000,
        ]);

        $barang = Barang::where('nama_barang', 'Ban Luar Tubeless 90/90-14')->first();
        $this->assertEquals(0, $barang->stok);

        // Update: Informasi berubah, namun stok tetap tidak dapat diubah dari form edit master barang
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.barang.update', $barang), [
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban Tubeless 90/90-14 Pro',
            'satuan' => 'Pcs',
            'harga_jual' => 210000,
            'stok' => 20, // Tidak boleh mengubah stok
        ]);
        $updateResponse->assertRedirect(route('admin.barang'));
        $this->assertDatabaseHas('ms_barang', [
            'id_barang' => $barang->id_barang,
            'nama_barang' => 'Ban Tubeless 90/90-14 Pro',
            'harga_jual' => 210000,
            'stok' => 0, // Stok tetap 0
        ]);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.barang.destroy', $barang));
        $deleteResponse->assertRedirect(route('admin.barang'));
        $this->assertDatabaseMissing('ms_barang', ['id_barang' => $barang->id_barang]);
    }

    public function test_admin_barang_masuk_increments_and_decrements_stock(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Busi']);
        $supplier = Supplier::create(['nama_supplier' => 'Denso Central']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Busi Iridium',
            'satuan' => 'Pcs',
            'harga_jual' => 45000,
            'stok' => 10,
        ]);

        // Record barang masuk (15 pcs)
        $response = $this->actingAs($this->admin)->post(route('admin.barang-masuk.store'), [
            'no_referensi' => 'BM-TEST-001',
            'tanggal' => now()->toDateString(),
            'id_supplier' => $supplier->id_supplier,
            'id_barang' => $barang->id_barang,
            'jumlah' => 15,
            'harga_beli' => 30000,
            'keterangan' => 'Restock busi',
        ]);
        $response->assertRedirect(route('admin.barang-masuk'));

        // Stok barang harus bertambah: 10 + 15 = 25
        $this->assertEquals(25, $barang->fresh()->stok);
        $this->assertDatabaseHas('tr_pembelian', [
            'no_referensi' => 'BM-TEST-001',
            'jumlah' => 15,
            'subtotal' => 450000,
        ]);

        $pembelian = Pembelian::where('no_referensi', 'BM-TEST-001')->first();

        // Batalkan barang masuk
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.barang-masuk.destroy', $pembelian));
        $deleteResponse->assertRedirect(route('admin.barang-masuk'));

        // Stok barang harus berkurang kembali: 25 - 15 = 10
        $this->assertEquals(10, $barang->fresh()->stok);
        $this->assertDatabaseMissing('tr_pembelian', ['id_tr_pembelian' => $pembelian->id_tr_pembelian]);
    }

    public function test_admin_stok_opname_synchronizes_physical_stock(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Aki']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Aki GS Astra GTZ5S',
            'satuan' => 'Unit',
            'harga_jual' => 250000,
            'stok' => 10,
        ]);

        // Catat stok opname (stok fisik hanya 8)
        $response = $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'id_barang' => $barang->id_barang,
            'stok_fisik' => 8,
            'alasan' => '2 unit rusak terkena benturan',
        ]);
        $response->assertRedirect(route('admin.stok-opname'));

        // Stok sistem di ms_barang harus disinkronkan menjadi 8
        $this->assertEquals(8, $barang->fresh()->stok);
        $this->assertDatabaseHas('ms_penyesuaian_detail', [
            'id_barang' => $barang->id_barang,
            'stok_sistem' => 10,
            'stok_fisik' => 8,
        ]);
    }

    public function test_admin_stok_opname_can_be_updated_and_reconciles_stock(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Kampas Rem']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Kampas Rem Depan Beat',
            'satuan' => 'Set',
            'harga_jual' => 45000,
            'stok' => 10,
        ]);

        // 1. Buat stok opname awal: fisik = 8 (stok berkurang dari 10 menjadi 8)
        $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'id_barang' => $barang->id_barang,
            'stok_fisik' => 8,
            'alasan' => '2 set rusak kemasan',
            'keterangan' => 'Audit awal',
        ]);

        $opname = Opname::latest('id_opname')->first();
        $this->assertEquals(8, $barang->fresh()->stok);

        // 2. Update stok opname: hitung ulang ternyata fisik ada 9 (selisih dari fisik lama: 9 - 8 = +1)
        $detail = $opname->details()->first();
        $response = $this->actingAs($this->admin)->put(route('admin.stok-opname.update', $opname), [
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Audit revisi hitung ulang',
            'details' => [
                [
                    'id_detail' => $detail->id_detail,
                    'stok_fisik' => 9,
                    'alasan' => '1 set ditemukan terselip di rak bawah',
                ],
            ],
        ]);
        $response->assertRedirect(route('admin.stok-opname'));
        $response->assertSessionHas('status');

        // Stok barang di sistem harus bertambah 1 menjadi 9
        $this->assertEquals(9, $barang->fresh()->stok);
        $this->assertDatabaseHas('ms_penyesuaian_detail', [
            'id_detail' => $detail->id_detail,
            'stok_fisik' => 9,
            'alasan' => '1 set ditemukan terselip di rak bawah',
        ]);
        $this->assertDatabaseHas('ms_penyesuaian', [
            'id_opname' => $opname->id_opname,
            'keterangan' => 'Audit revisi hitung ulang',
        ]);
    }

    public function test_admin_stok_opname_update_rejected_when_stock_becomes_negative(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Oli Mesran Super 1L',
            'satuan' => 'Botol',
            'harga_jual' => 35000,
            'stok' => 5,
        ]);

        // Buat opname: fisik ditemukan 10 (+5 botol)
        $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'id_barang' => $barang->id_barang,
            'stok_fisik' => 10,
            'alasan' => 'Tambahan stok gudang belakang',
        ]);
        $this->assertEquals(10, $barang->fresh()->stok);

        $opname = Opname::latest('id_opname')->first();
        $detail = $opname->details()->first();

        // Simulasikan 8 botol terjual, sisa stok tinggal 2 botol
        $barang->update(['stok' => 2]);

        // Coba edit opname menjadi stok fisik 1 (delta: 1 - 10 = -9).
        // Karena stok saat ini hanya 2, 2 + (-9) = -7 (negatif). Harus ditolak!
        $response = $this->actingAs($this->admin)->put(route('admin.stok-opname.update', $opname), [
            'tanggal' => now()->toDateString(),
            'details' => [
                [
                    'id_detail' => $detail->id_detail,
                    'stok_fisik' => 1,
                    'alasan' => 'Salah hitung sebelumnya',
                ],
            ],
        ]);
        $response->assertSessionHasErrors('stok_fisik');

        // Stok tidak berubah menjadi negatif, tetap 2
        $this->assertEquals(2, $barang->fresh()->stok);
    }

    public function test_admin_stok_opname_can_be_deleted_and_reverts_stock(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Busi']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Busi NGK CPR9EA',
            'satuan' => 'Pcs',
            'harga_jual' => 25000,
            'stok' => 10,
        ]);

        // Catat stok opname: fisik 7 (penyesuaian: -3 pcs)
        $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'id_barang' => $barang->id_barang,
            'stok_fisik' => 7,
            'alasan' => '3 busi pecah keramik',
        ]);
        $this->assertEquals(7, $barang->fresh()->stok);

        $opname = Opname::latest('id_opname')->first();

        // Hapus / batalkan sesi opname
        $response = $this->actingAs($this->admin)->delete(route('admin.stok-opname.destroy', $opname));
        $response->assertRedirect(route('admin.stok-opname'));
        $response->assertSessionHas('status');

        // Stok barang harus dikembalikan ke stok semula: 7 - (-3) = 10
        $this->assertEquals(10, $barang->fresh()->stok);
        $this->assertDatabaseMissing('ms_penyesuaian', ['id_opname' => $opname->id_opname]);
        $this->assertDatabaseMissing('ms_penyesuaian_detail', ['id_opname' => $opname->id_opname]);
    }

    public function test_admin_stok_opname_deletion_rejected_when_stock_insufficient(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Spion']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Spion Standar Vario',
            'satuan' => 'Set',
            'harga_jual' => 60000,
            'stok' => 2,
        ]);

        // Opname: fisik ditemukan 12 (+10 set)
        $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'id_barang' => $barang->id_barang,
            'stok_fisik' => 12,
            'alasan' => 'Ditemukan stok cadangan di kardus',
        ]);
        $this->assertEquals(12, $barang->fresh()->stok);

        $opname = Opname::latest('id_opname')->first();

        // Simulasikan 10 set terjual, sisa stok tinggal 2
        $barang->refresh()->update(['stok' => 2]);

        // Batalkan sesi opname (akan mengurangi 10 set, padahal stok sisa hanya 2). Harus ditolak!
        $response = $this->actingAs($this->admin)->delete(route('admin.stok-opname.destroy', $opname));
        $response->assertRedirect(route('admin.stok-opname'));
        $response->assertSessionHas('error');

        // Sesi opname tidak terhapus dan stok tetap 2
        $this->assertDatabaseHas('ms_penyesuaian', ['id_opname' => $opname->id_opname]);
        $this->assertEquals(2, $barang->fresh()->stok);
    }

    public function test_admin_stok_opname_batch_items_synchronizes_all_stock(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Oli & Filter']);
        $barang1 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Oli Gardan AHM',
            'satuan' => 'Tube',
            'harga_jual' => 18000,
            'stok' => 15,
        ]);
        $barang2 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Filter Oli Kaze',
            'satuan' => 'Pcs',
            'harga_jual' => 25000,
            'stok' => 8,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Audit batch bulanan',
            'items' => [
                [
                    'id_barang' => $barang1->id_barang,
                    'stok_fisik' => 12,
                    'alasan' => '3 botol rembes bocor',
                ],
                [
                    'id_barang' => $barang2->id_barang,
                    'stok_fisik' => 10,
                    'alasan' => '2 pcs tambahan belum terinput',
                ],
            ],
        ]);
        $response->assertRedirect(route('admin.stok-opname'));

        // Kedua barang tersinkronkan stok fisiknya
        $this->assertEquals(12, $barang1->fresh()->stok);
        $this->assertEquals(10, $barang2->fresh()->stok);

        $opname = Opname::latest('id_opname')->first();
        $this->assertCount(2, $opname->details);
    }

    public function test_admin_stok_opname_validation(): void
    {
        // 1. Validasi required
        $response = $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => '',
            'id_barang' => '',
            'stok_fisik' => '',
            'alasan' => '',
        ]);
        $response->assertSessionHasErrors(['tanggal', 'id_barang', 'stok_fisik', 'alasan']);

        // 2. Validasi barang tidak ada & stok fisik negatif
        $responseInvalid = $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'id_barang' => 999999,
            'stok_fisik' => -5,
            'alasan' => 'Test negatif',
        ]);
        $responseInvalid->assertSessionHasErrors(['id_barang', 'stok_fisik']);
    }

    public function test_admin_stok_opname_ui_badges_and_filters(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Kabel Gas']);
        $barang1 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Kabel Gas Vario 125',
            'satuan' => 'Pcs',
            'harga_jual' => 35000,
            'stok' => 10,
        ]);
        $barang2 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Kabel Gas Beat FI',
            'satuan' => 'Pcs',
            'harga_jual' => 30000,
            'stok' => 5,
        ]);

        // 1. Buat sesi pertama: ada selisih (fisik 8 vs sistem 10)
        $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Audit kabel vario selisih',
            'id_barang' => $barang1->id_barang,
            'stok_fisik' => 8,
            'alasan' => '2 pcs cacat kabel serabut',
        ]);

        // 2. Buat sesi kedua: sesuai tanpa selisih (fisik 5 vs sistem 5)
        $this->actingAs($this->admin)->post(route('admin.stok-opname.store'), [
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Audit kabel beat sesuai',
            'id_barang' => $barang2->id_barang,
            'stok_fisik' => 5,
            'alasan' => 'Sesuai hitung',
        ]);

        // 3. Akses halaman index (Semua Sesi)
        $responseAll = $this->actingAs($this->admin)->get(route('admin.stok-opname'));
        $responseAll->assertOk();
        $responseAll->assertSee('Ada Selisih');
        $responseAll->assertSee('Sesuai');
        $responseAll->assertSee('Audit kabel vario selisih');
        $responseAll->assertSee('Audit kabel beat sesuai');

        // 4. Akses filter Ada Selisih
        $responseSelisih = $this->actingAs($this->admin)->get(route('admin.stok-opname', ['status' => 'selisih']));
        $responseSelisih->assertOk();
        $responseSelisih->assertSee('Audit kabel vario selisih');
        $responseSelisih->assertDontSee('Audit kabel beat sesuai');

        // 5. Akses filter Sesuai
        $responseSesuai = $this->actingAs($this->admin)->get(route('admin.stok-opname', ['status' => 'sesuai']));
        $responseSesuai->assertOk();
        $responseSesuai->assertSee('Audit kabel beat sesuai');
        $responseSesuai->assertDontSee('Audit kabel vario selisih');
    }

    public function test_admin_riwayat_can_cancel_transaction_and_restore_stock(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Oli']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Oli Shell AX7',
            'satuan' => 'Botol',
            'harga_jual' => 60000,
            'stok' => 10,
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => 'Andi Wijaya',
            'plat_nomor' => 'D 9999 EF',
        ]);

        $penjualan = Penjualan::create([
            'no_transaksi' => 'INV-TEST-001',
            'id_user' => $this->admin->id_user,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'tanggal' => now(),
            'total' => 120000,
            'nominal_bayar' => 150000,
            'kembalian' => 30000,
        ]);

        // Detail penjualan: membeli 2 botol oli
        PenjualanDetail::create([
            'id_transaksi' => $penjualan->id_transaksi,
            'jenis_item' => 'barang',
            'id_barang' => $barang->id_barang,
            'nama_item' => $barang->nama_barang,
            'qty' => 2,
            'harga_satuan' => 60000,
            'subtotal' => 120000,
        ]);

        // Simulasikan stok terpotong saat transaksi dibuat: 10 - 2 = 8
        $barang->update(['stok' => 8]);

        // Batalkan transaksi melalui admin riwayat
        $response = $this->actingAs($this->admin)->delete(route('admin.riwayat.destroy', $penjualan));
        $response->assertRedirect(route('admin.riwayat'));

        // Transaksi terhapus dan stok dikembalikan: 8 + 2 = 10
        $this->assertEquals(10, $barang->fresh()->stok);
        $this->assertDatabaseMissing('tr_penjualan', ['id_transaksi' => $penjualan->id_transaksi]);
    }

    public function test_barang_masuk_multiple_items_increments_all_stock(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Pelumas']);
        $supplier = Supplier::create(['nama_supplier' => 'Pertamina Lubricants']);
        $barang1 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Fastron Gold 1L',
            'satuan' => 'Botol',
            'harga_jual' => 90000,
            'stok' => 5,
        ]);
        $barang2 = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Enduro Matic 0.8L',
            'satuan' => 'Botol',
            'harga_jual' => 48000,
            'stok' => 10,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.barang-masuk.store'), [
            'no_referensi' => 'BM-BATCH-001',
            'tanggal' => now()->toDateString(),
            'id_supplier' => $supplier->id_supplier,
            'keterangan' => 'Pengadaan batch pelumas',
            'items' => [
                [
                    'id_barang' => $barang1->id_barang,
                    'jumlah' => 10,
                    'harga_beli' => 75000,
                ],
                [
                    'id_barang' => $barang2->id_barang,
                    'jumlah' => 20,
                    'harga_beli' => 38000,
                ],
            ],
        ]);
        $response->assertRedirect(route('admin.barang-masuk'));

        // Seluruh stok bertambah sesuai detail: 5+10 = 15, 10+20 = 30
        $this->assertEquals(15, $barang1->fresh()->stok);
        $this->assertEquals(30, $barang2->fresh()->stok);
    }

    public function test_barang_masuk_invalid_barang_and_quantity_rejected(): void
    {
        $supplier = Supplier::create(['nama_supplier' => 'Astra Otoparts']);

        // 1. ID barang tidak valid
        $responseInvalidBarang = $this->actingAs($this->admin)->post(route('admin.barang-masuk.store'), [
            'no_referensi' => 'BM-ERR-001',
            'tanggal' => now()->toDateString(),
            'id_supplier' => $supplier->id_supplier,
            'id_barang' => 999999,
            'jumlah' => 5,
            'harga_beli' => 10000,
        ]);
        $responseInvalidBarang->assertSessionHasErrors('id_barang');

        // 2. Kuantitas 0 atau negatif
        $kategori = Kategori::create(['nama_kategori' => 'Filter']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Filter Udara',
            'satuan' => 'Pcs',
            'harga_jual' => 35000,
            'stok' => 0,
        ]);

        $responseZeroQty = $this->actingAs($this->admin)->post(route('admin.barang-masuk.store'), [
            'no_referensi' => 'BM-ERR-002',
            'tanggal' => now()->toDateString(),
            'id_supplier' => $supplier->id_supplier,
            'id_barang' => $barang->id_barang,
            'jumlah' => 0,
            'harga_beli' => 20000,
        ]);
        $responseZeroQty->assertSessionHasErrors('jumlah');
        $this->assertEquals(0, $barang->fresh()->stok);
    }

    public function test_barang_masuk_deletion_rejected_when_stock_insufficient(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Rantai']);
        $supplier = Supplier::create(['nama_supplier' => 'DID Chain']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Rantai 428H',
            'satuan' => 'Set',
            'harga_jual' => 120000,
            'stok' => 0,
        ]);

        // Catat barang masuk 10 pcs
        $this->actingAs($this->admin)->post(route('admin.barang-masuk.store'), [
            'no_referensi' => 'BM-TEST-DEL-01',
            'tanggal' => now()->toDateString(),
            'id_supplier' => $supplier->id_supplier,
            'id_barang' => $barang->id_barang,
            'jumlah' => 10,
            'harga_beli' => 80000,
        ]);

        $pembelian = Pembelian::where('no_referensi', 'BM-TEST-DEL-01')->first();
        $this->assertEquals(10, $barang->fresh()->stok);

        // Simulasikan 8 pcs terjual, sisa stok tinggal 2 pcs
        $barang->update(['stok' => 2]);

        // Coba batalkan/hapus barang masuk (10 pcs). Harus ditolak karena stok sisa hanya 2 pcs!
        $response = $this->actingAs($this->admin)->delete(route('admin.barang-masuk.destroy', $pembelian));
        $response->assertRedirect(route('admin.barang-masuk'));
        $response->assertSessionHas('error');

        // Data pembelian tidak terhapus dan stok tetap 2 (tidak negatif)
        $this->assertDatabaseHas('tr_pembelian', ['id_tr_pembelian' => $pembelian->id_tr_pembelian]);
        $this->assertEquals(2, $barang->fresh()->stok);
    }

    public function test_penjualan_pos_decrements_stock_successfully(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Ban']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban Tubeless Corsa',
            'satuan' => 'Pcs',
            'harga_jual' => 150000,
            'stok' => 10,
        ]);
        $jasa = Jasa::create([
            'nama_jasa' => 'Pasang Ban Tubeless',
            'tarif' => 20000,
        ]);

        $kasir = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
        ]);

        // Proses transaksi POS: beli 2 ban + 1 jasa pasang
        // Total = (2 * 150.000) + 20.000 = 320.000, bayar 350.000, kembalian 30.000
        $response = $this->actingAs($kasir)->post(route('kasir.transaksi.store'), [
            'nominal_bayar' => 350000,
            'items' => [
                [
                    'jenis' => 'barang',
                    'id' => $barang->id_barang,
                    'qty' => 2,
                ],
                [
                    'jenis' => 'jasa',
                    'id' => $jasa->id_jasa,
                    'qty' => 1,
                ],
            ],
        ]);
        $response->assertRedirect(route('kasir.transaksi'));
        $response->assertSessionHas('status');

        // Stok barang harus berkurang dari 10 menjadi 8
        $this->assertEquals(8, $barang->fresh()->stok);

        // Header dan detail penjualan tersimpan
        $penjualan = Penjualan::latest('id_transaksi')->first();
        $this->assertEquals(320000, $penjualan->total);
        $this->assertEquals(30000, $penjualan->kembalian);
        $this->assertDatabaseHas('detail_penjualan', [
            'id_transaksi' => $penjualan->id_transaksi,
            'id_barang' => $barang->id_barang,
            'qty' => 2,
        ]);
    }

    public function test_penjualan_pos_rejected_when_stock_insufficient(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Oli']);
        $barang = Barang::create([
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Oli Yamalube 0.8L',
            'satuan' => 'Botol',
            'harga_jual' => 50000,
            'stok' => 3, // Hanya tersedia 3 botol
        ]);

        $kasir = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
        ]);

        // Kasir mencoba menjual 5 botol (melebihi stok)
        $response = $this->actingAs($kasir)->post(route('kasir.transaksi.store'), [
            'nominal_bayar' => 250000,
            'items' => [
                [
                    'jenis' => 'barang',
                    'id' => $barang->id_barang,
                    'qty' => 5,
                ],
            ],
        ]);
        $response->assertSessionHasErrors('items');

        // Stok tidak berubah
        $this->assertEquals(3, $barang->fresh()->stok);
    }

    public function test_cashier_cannot_access_admin_routes(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
        ]);

        // Kasir mencoba mengakses endpoint khusus Admin
        $response1 = $this->actingAs($kasir)->get(route('admin.dashboard'));
        $response1->assertForbidden();

        $response2 = $this->actingAs($kasir)->get(route('admin.barang'));
        $response2->assertForbidden();

        $response3 = $this->actingAs($kasir)->get(route('admin.barang-masuk'));
        $response3->assertForbidden();

        $response4 = $this->actingAs($kasir)->get(route('admin.pengguna'));
        $response4->assertForbidden();
    }

    public function test_cashier_can_crud_pelanggan(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
        ]);

        // 1. View pelanggan list
        $indexResponse = $this->actingAs($kasir)->get(route('kasir.pelanggan'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Data Pelanggan');

        // 2. Create pelanggan
        $createResponse = $this->actingAs($kasir)->post(route('kasir.pelanggan.store'), [
            'nama' => 'Ahmad Kasir Customer',
            'plat_nomor' => 'D 1234 KSR',
            'no_telepon' => '081234567890',
        ]);
        $createResponse->assertRedirect(route('kasir.pelanggan'));
        $this->assertDatabaseHas('ms_pelanggan', [
            'nama' => 'Ahmad Kasir Customer',
            'plat_nomor' => 'D 1234 KSR',
        ]);

        $pelanggan = Pelanggan::where('nama', 'Ahmad Kasir Customer')->first();
        $this->assertNotNull($pelanggan);

        // 3. Update pelanggan
        $updateResponse = $this->actingAs($kasir)->put(route('kasir.pelanggan.update', $pelanggan), [
            'nama' => 'Ahmad Kasir Customer Updated',
            'plat_nomor' => 'D 5678 KSR',
            'no_telepon' => '081987654321',
        ]);
        $updateResponse->assertRedirect(route('kasir.pelanggan'));
        $this->assertDatabaseHas('ms_pelanggan', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nama' => 'Ahmad Kasir Customer Updated',
            'plat_nomor' => 'D 5678 KSR',
        ]);

        // 4. Delete pelanggan
        $deleteResponse = $this->actingAs($kasir)->delete(route('kasir.pelanggan.destroy', $pelanggan));
        $deleteResponse->assertRedirect(route('kasir.pelanggan'));
        $this->assertDatabaseMissing('ms_pelanggan', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
        ]);

        // 5. Validation check
        $invalidResponse = $this->actingAs($kasir)->post(route('kasir.pelanggan.store'), [
            'nama' => '',
            'no_telepon' => '123',
        ]);
        $invalidResponse->assertSessionHasErrors(['nama', 'no_telepon']);
    }
}
