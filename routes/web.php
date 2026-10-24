<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'kasir.dashboard');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');
Route::post('/profil/password', function (Request $request) {
    $validated = $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
    ], [
        'current_password.current_password' => 'Password saat ini tidak sesuai.',
        'password.min' => 'Password baru minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        'password.different' => 'Password baru harus berbeda dari password saat ini.',
    ]);

    $user = $request->user();
    $user->password = $validated['password'];
    $user->save();

    return back()->with('status', 'Password berhasil diperbarui.');
})->middleware('auth')->name('profile.password.update');
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PenggunaController as AdminPenggunaController;
use App\Http\Controllers\Admin\BarangController as AdminBarangController;
use App\Http\Controllers\Admin\KategoriController as AdminKategoriController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Admin\LayananController as AdminLayananController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\BarangMasukController as AdminBarangMasukController;
use App\Http\Controllers\Admin\StokOpnameController as AdminStokOpnameController;
use App\Http\Controllers\Admin\RiwayatController as AdminRiwayatController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\KasirController;

Route::get('/registrasi', fn () => view('pages.register'))->name('register');
Route::get('/playground', fn () => view('pages.playground'))->name('playground');

Route::prefix('kasir')->name('kasir.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi', [KasirController::class, 'transaksi'])->name('transaksi');
    Route::get('/pelanggan', [KasirController::class, 'pelanggan'])->name('pelanggan');
    Route::get('/stok', [KasirController::class, 'stok'])->name('stok');
    Route::get('/riwayat', [KasirController::class, 'riwayat'])->name('riwayat');
    Route::get('/laporan', [KasirController::class, 'laporan'])->name('laporan');
    Route::get('/profil', [KasirController::class, 'profil'])->name('profil');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Pengguna CRUD
    Route::get('/pengguna', [AdminPenggunaController::class, 'index'])->name('pengguna');
    Route::post('/pengguna', [AdminPenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{user}', [AdminPenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{user}', [AdminPenggunaController::class, 'destroy'])->name('pengguna.destroy');

    // Barang CRUD
    Route::get('/barang', [AdminBarangController::class, 'index'])->name('barang');
    Route::post('/barang', [AdminBarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{barang}', [AdminBarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [AdminBarangController::class, 'destroy'])->name('barang.destroy');

    // Kategori CRUD
    Route::get('/kategori', [AdminKategoriController::class, 'index'])->name('kategori');
    Route::post('/kategori', [AdminKategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{kategori}', [AdminKategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [AdminKategoriController::class, 'destroy'])->name('kategori.destroy');

    // Supplier CRUD
    Route::get('/supplier', [AdminSupplierController::class, 'index'])->name('supplier');
    Route::post('/supplier', [AdminSupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{supplier}', [AdminSupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{supplier}', [AdminSupplierController::class, 'destroy'])->name('supplier.destroy');

    // Layanan / Jasa CRUD
    Route::get('/layanan', [AdminLayananController::class, 'index'])->name('layanan');
    Route::post('/layanan', [AdminLayananController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/{jasa}', [AdminLayananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{jasa}', [AdminLayananController::class, 'destroy'])->name('layanan.destroy');

    // Pelanggan CRUD
    Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan');
    Route::post('/pelanggan', [AdminPelangganController::class, 'store'])->name('pelanggan.store');
    Route::put('/pelanggan/{pelanggan}', [AdminPelangganController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{pelanggan}', [AdminPelangganController::class, 'destroy'])->name('pelanggan.destroy');

    // Barang Masuk (Pembelian)
    Route::get('/barang-masuk', [AdminBarangMasukController::class, 'index'])->name('barang-masuk');
    Route::post('/barang-masuk', [AdminBarangMasukController::class, 'store'])->name('barang-masuk.store');
    Route::delete('/barang-masuk/{pembelian}', [AdminBarangMasukController::class, 'destroy'])->name('barang-masuk.destroy');

    // Stok Opname
    Route::get('/stok-opname', [AdminStokOpnameController::class, 'index'])->name('stok-opname');
    Route::post('/stok-opname', [AdminStokOpnameController::class, 'store'])->name('stok-opname.store');
    Route::delete('/stok-opname/{opname}', [AdminStokOpnameController::class, 'destroy'])->name('stok-opname.destroy');

    // Riwayat Transaksi Penjualan
    Route::get('/riwayat', [AdminRiwayatController::class, 'index'])->name('riwayat');
    Route::delete('/riwayat/{penjualan}', [AdminRiwayatController::class, 'destroy'])->name('riwayat.destroy');

    // Laporan & Profil
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan');
    Route::get('/profil', [AdminProfilController::class, 'index'])->name('profil');
});