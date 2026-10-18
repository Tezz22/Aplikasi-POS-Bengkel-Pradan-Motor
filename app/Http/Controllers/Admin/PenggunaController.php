<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id_user', 'desc')->get();

        return view('pages.admin.pengguna', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:50'],
            'email'    => ['required', 'email', 'max:50', 'unique:ms_users,email'],
            'role'     => ['required', 'in:admin,kasir'],
            'status'   => ['nullable', 'in:aktif,nonaktif'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'nama.required'      => 'Nama lengkap wajib diisi.',
            'nama.max'           => 'Nama lengkap maksimal 50 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.max'          => 'Email maksimal 50 karakter.',
            'email.unique'       => 'Email sudah digunakan oleh pengguna lain.',
            'role.required'      => 'Role harus dipilih.',
            'role.in'            => 'Role tidak valid.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $validated['status'] = $validated['status'] ?? 'aktif';

        User::create($validated);

        return redirect()->route('admin.pengguna')->with('status', 'Pengguna baru berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:50'],
            'email'    => ['required', 'email', 'max:50', 'unique:ms_users,email,' . $user->id_user . ',id_user'],
            'role'     => ['required', 'in:admin,kasir'],
            'status'   => ['nullable', 'in:aktif,nonaktif'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'nama.required'      => 'Nama lengkap wajib diisi.',
            'nama.max'           => 'Nama lengkap maksimal 50 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.max'          => 'Email maksimal 50 karakter.',
            'email.unique'       => 'Email sudah digunakan oleh pengguna lain.',
            'role.required'      => 'Role harus dipilih.',
            'role.in'            => 'Role tidak valid.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'nama'   => $validated['nama'],
            'email'  => $validated['email'],
            'role'   => $validated['role'],
            'status' => $validated['status'] ?? $user->status,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()->route('admin.pengguna')->with('status', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(Request $request, User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id_user === $request->user()->id_user) {
            return redirect()->route('admin.pengguna')->withErrors(['Tidak dapat menghapus akun yang sedang digunakan.']);
        }

        $user->delete();

        return redirect()->route('admin.pengguna')->with('status', 'Pengguna berhasil dihapus!');
    }
}
