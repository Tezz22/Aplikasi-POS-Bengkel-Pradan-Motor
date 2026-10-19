<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Jasa;
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

    public function test_admin_can_crud_barang(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Ban Motor']);

        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.barang.store'), [
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban Luar Tubeless 90/90-14',
            'satuan' => 'Pcs',
            'harga_jual' => 195000,
            'stok' => 15,
        ]);
        $response->assertRedirect(route('admin.barang'));
        $this->assertDatabaseHas('ms_barang', [
            'nama_barang' => 'Ban Luar Tubeless 90/90-14',
            'stok' => 15,
            'harga_jual' => 195000,
        ]);

        $barang = Barang::where('nama_barang', 'Ban Luar Tubeless 90/90-14')->first();

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.barang.update', $barang), [
            'id_kategori' => $kategori->id_kategori,
            'nama_barang' => 'Ban Tubeless 90/90-14 Pro',
            'satuan' => 'Pcs',
            'harga_jual' => 210000,
            'stok' => 20,
        ]);
        $updateResponse->assertRedirect(route('admin.barang'));
        $this->assertDatabaseHas('ms_barang', [
            'id_barang' => $barang->id_barang,
            'nama_barang' => 'Ban Tubeless 90/90-14 Pro',
            'harga_jual' => 210000,
            'stok' => 20,
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
        $this->assertDatabaseMissing('tr_pembelian', ['id_pembelian' => $pembelian->id_pembelian]);
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
}
