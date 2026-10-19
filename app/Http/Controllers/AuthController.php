<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'kasir.dashboard');
        }

        return view('pages.login');
    }

    public function login(Request $request)
    {
        $throttleKey = Str::transliterate(Str::lower($request->input('email', '')) . '|' . $request->ip());

        // Batasi maksimal 5 percobaan gagal dalam 60 detik (Brute Force Protection)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login gagal. Silakan coba kembali dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid. Masukkan alamat email yang benar (contoh: admin@pradanmotor.com).',
            'password.required' => 'Password wajib diisi.',
            'password.string'   => 'Password harus berupa teks valid.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);

            $user = $request->user();
            if ($user->status !== 'aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Kolom users.role harus berisi "admin" atau "kasir"
            $role = strtolower((string) $user->role);

            if ($role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($role === 'kasir') {
                return redirect()->intended(route('kasir.dashboard'));
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Role akun tidak dikenali. Hubungi administrator sistem.',
            ])->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
