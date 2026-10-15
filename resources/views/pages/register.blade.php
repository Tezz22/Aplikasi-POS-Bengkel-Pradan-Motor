<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Akun - Pradan Motor POS</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite & CDN Fallback) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#2563eb',
                            sidebar: '#182033',
                            active: '#293652',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="bg-gray-50 text-slate-800 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-2 min-h-[520px]">
        <!-- Left Hero / Brand Side -->
        <div class="bg-[#182033] p-8 md:p-12 flex flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-2.5 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                        P
                    </div>
                    <span class="font-extrabold text-xl tracking-tight text-white">Pradan Motor</span>
                </div>

                <h2 class="text-2xl md:text-3xl font-extrabold leading-tight text-white mb-4">
                    Pendaftaran Akun Baru Staf Bengkel
                </h2>
                <p class="text-slate-400 text-xs md:text-sm leading-relaxed">
                    Daftarkan akun staf kasir atau admin baru untuk mulai menggunakan sistem POS Pradan Motor.
                </p>
            </div>

            <div class="relative z-10 pt-8 border-t border-[#222d47] text-xs text-slate-400 flex items-center justify-between">
                <span>&copy; {{ date('Y') }} Pradan Motor</span>
                <span class="text-blue-400 font-medium">v2.0 Tailwind</span>
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="p-8 md:p-12 flex flex-col justify-center bg-white">
            <div class="max-w-sm w-full mx-auto">
                <div class="mb-5">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Buat Akun Baru ✨</h1>
                    <p class="text-xs text-gray-400 mt-1">Lengkapi data diri untuk registrasi staf</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Nama staf" class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="staf@pradanmotor.com" class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi</label>
                            <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                            Daftar Sekarang
                        </button>
                    </div>

                    <p class="text-center text-xs text-gray-500 pt-2">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:underline">Masuk di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>