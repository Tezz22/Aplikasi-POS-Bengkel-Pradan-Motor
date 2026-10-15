@extends('layouts.app')

@section('title', 'Profil Kasir')

@section('content')
    @include('components.page-header', [
        'title'    => 'Profil Kasir',
        'subtitle' => 'Kelola informasi profil pribadi dan keamanan password',
        'icon'     => 'profil'
    ])

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col items-center text-center">
            <div class="relative mb-4">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=160&h=160&fit=crop&crop=faces" 
                     alt="Avatar" 
                     class="w-24 h-24 rounded-full object-cover ring-4 ring-emerald-500/20">
                <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 rounded-full ring-2 ring-white"></span>
            </div>
            <h3 class="text-base font-bold text-gray-900">{{ auth()->user()->name ?? 'Joo' }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ auth()->user()->email ?? 'kasir@pradanmotor.com' }}</p>
            <div class="mt-3">
                @include('components.badge', [
                    'type' => 'success',
                    'size' => 'md',
                    'text' => 'Kasir Bengkel'
                ])
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <h3 class="text-sm font-bold text-gray-900 mb-1">Ganti Password</h3>
            <p class="text-xs text-gray-400 mb-5">Pastikan password selalu dijaga kerahasiaannya</p>

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @include('components.input', [
                    'label' => 'Password Saat Ini',
                    'name'  => 'current_password',
                    'type'  => 'password',
                    'required' => true
                ])

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @include('components.input', [
                        'label' => 'Password Baru (Min. 8 Karakter)',
                        'name'  => 'password',
                        'type'  => 'password',
                        'required' => true
                    ])
                    @include('components.input', [
                        'label' => 'Konfirmasi Password Baru',
                        'name'  => 'password_confirmation',
                        'type'  => 'password',
                        'required' => true
                    ])
                </div>

                <div class="pt-3">
                    @include('components.button', [
                        'type'    => 'submit',
                        'variant' => 'primary',
                        'text'    => 'Perbarui Password'
                    ])
                </div>
            </form>
        </div>
    </div>
@endsection
