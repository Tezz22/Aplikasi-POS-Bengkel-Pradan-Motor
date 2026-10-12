<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'kasir.dashboard');
});
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'kasir.dashboard');
    }

    return view('pages.login');
})->name('login');
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        // Kolom users.role harus berisi "admin" atau "kasir".
        $role = strtolower((string) $request->user()->role);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'kasir') {
            return redirect()->route('kasir.dashboard');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Role akun tidak dikenali. Hubungi administrator.',
        ])->onlyInput('email');
    }

    return back()->withErrors([
        'email' => 'Email atau password tidak sesuai.',
    ])->onlyInput('email');
});
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
Route::get('/registrasi',fn()=>view('pages.register'))->name('register');
Route::prefix('kasir')->name('kasir.')->middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('pages.dashboard'))->name('dashboard');
    Route::get('/transaksi', fn () => view('pages.transaksi'))->name('transaksi');
    Route::get('/pelanggan', fn () => view('pages.pelanggan'))->name('pelanggan');
    Route::get('/stok', fn () => view('pages.stok'))->name('stok');
    Route::get('/riwayat', fn () => view('pages.riwayat'))->name('riwayat');
    Route::get('/laporan', fn () => view('pages.laporan'))->name('laporan');
    Route::get('/profil', fn () => view('pages.profil'))->name('profil');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('pages.admin-dashboard'))->name('dashboard');
    Route::get('/pengguna', fn () => view('pages.pengguna'))->name('pengguna');
    Route::get('/barang', fn () => view('pages.barang'))->name('barang');
    Route::get('/kategori', fn () => view('pages.kategori'))->name('kategori');
    Route::get('/supplier', fn () => view('pages.supplier'))->name('supplier');
    Route::get('/layanan', fn () => view('pages.layanan'))->name('layanan');
    Route::get('/pelanggan', fn () => view('pages.admin-pelanggan'))->name('pelanggan');
    Route::get('/barang-masuk', fn () => view('pages.barang-masuk'))->name('barang-masuk');
    Route::get('/stok-opname', fn () => view('pages.stok-opname'))->name('stok-opname');
    Route::get('/laporan', fn () => view('pages.admin-laporan'))->name('laporan');
    Route::get('/profil', fn () => view('pages.profil'))->name('profil');
});