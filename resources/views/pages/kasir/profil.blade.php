@extends('layouts.app')

@section('title', 'Profil Kasir')

@section('content')
    <x-page-header 
        title="Profil Kasir" 
        subtitle="Kelola informasi identitas akun dan keamanan kata sandi" 
        icon="profil" 
    />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Card Informasi Akun Kasir (Nama, Email, Role) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col justify-between">
            <div>
                <!-- Header Card & Icon User (Tanpa Foto) -->
                <div class="flex items-center gap-4 pb-5 mb-5 border-b border-gray-100">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-2xs shrink-0">
                        @include('components.icon', ['name' => 'user', 'size' => 26])
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-gray-900 truncate">{{ auth()->user()->name ?? 'Kasir' }}</h3>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ auth()->user()->email ?? 'kasir@pradanmotor.com' }}</p>
                        <div class="mt-2">
                            <x-badge 
                                type="success" 
                                size="sm" 
                                text="Kasir Bengkel" 
                            />
                        </div>
                    </div>
                </div>

                <!-- Detail Field Info -->
                <div class="space-y-3">
                    <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200/70 flex items-center justify-center text-gray-500 shadow-2xs shrink-0">
                                @include('components.icon', ['name' => 'user', 'size' => 15])
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Username</span>
                                <span class="text-xs font-bold text-gray-800 truncate block">{{ auth()->user()->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200/70 flex items-center justify-center text-gray-500 shadow-2xs shrink-0">
                                @include('components.icon', ['name' => 'mail', 'size' => 15])
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Alamat Email</span>
                                <span class="text-xs font-bold text-gray-800 truncate block">{{ auth()->user()->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200/70 flex items-center justify-center text-gray-500 shadow-2xs shrink-0">
                                @include('components.icon', ['name' => 'shield', 'size' => 15])
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Hak Akses</span>
                                <span class="text-xs font-bold text-gray-800">Kasir Bengkel</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] text-gray-400 flex items-center justify-between">
                <span>Sistem POS Pradan Motor</span>
                <span class="font-medium text-gray-500">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d M Y') : 'Aktif' }}</span>
            </div>
        </div>

        <!-- Form Ganti Password Kasir -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-2xs shrink-0">
                    @include('components.icon', ['name' => 'lock', 'size' => 18])
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Ganti Password</h3>
                    <p class="text-xs text-gray-400">Pastikan password selalu dijaga kerahasiaannya untuk keamanan transaksi</p>
                </div>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                @csrf
                <x-input 
                    label="Password Saat Ini" 
                    name="current_password" 
                    type="password" 
                    placeholder="Masukkan password saat ini"
                    :required="true" 
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input 
                        label="Password Baru" 
                        name="password" 
                        type="password" 
                        placeholder="Minimal 8 karakter"
                        :required="true" 
                    />
                    <x-input 
                        label="Konfirmasi Password Baru" 
                        name="password_confirmation" 
                        type="password" 
                        placeholder="Ulangi password baru"
                        :required="true" 
                    />
                </div>

                <div class="p-3.5 bg-emerald-50/50 border border-emerald-100/80 rounded-xl text-[11px] text-emerald-800 flex items-start gap-2.5">
                    <span class="text-emerald-600 font-bold shrink-0">ℹ</span>
                    <span>Kata sandi baru minimal 8 karakter dan harus berbeda dengan kata sandi saat ini. Jaga kerahasiaan kata sandi Anda.</span>
                </div>

                <div class="pt-2">
                    <x-button 
                        type="submit" 
                        variant="primary" 
                        icon="check"
                        text="Perbarui Password" 
                    />
                </div>
            </form>
        </div>
    </div>
@endsection
