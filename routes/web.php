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
use App\Http\Controllers\AdminController;
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
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('pengguna');
    Route::get('/barang', [AdminController::class, 'barang'])->name('barang');
    Route::get('/kategori', [AdminController::class, 'kategori'])->name('kategori');
    Route::get('/supplier', [AdminController::class, 'supplier'])->name('supplier');
    Route::get('/layanan', [AdminController::class, 'layanan'])->name('layanan');
    Route::get('/pelanggan', [AdminController::class, 'pelanggan'])->name('pelanggan');
    Route::get('/barang-masuk', [AdminController::class, 'barangMasuk'])->name('barang-masuk');
    Route::get('/stok-opname', [AdminController::class, 'stokOpname'])->name('stok-opname');
    Route::get('/riwayat', [AdminController::class, 'riwayat'])->name('riwayat');
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/profil', [AdminController::class, 'profil'])->name('profil');
});