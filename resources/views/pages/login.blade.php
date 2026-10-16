<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Pradan Motor POS</title>

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
            <!-- Background Glow -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <img src="{{ asset('assets/images/logo/logo-icon.svg') }}" alt="Logo Pradan Motor" class="w-9 h-9 rounded-xl object-contain shrink-0 shadow-md shadow-blue-500/30">
                    <span class="font-extrabold text-2xl tracking-tight text-white">Pradan Motor</span>
                </div>

                <h2 class="text-2xl md:text-3xl font-extrabold leading-tight text-white mb-4">
                    Sistem POS & Manajemen Bengkel Motor Modern
                </h2>
                <p class="text-slate-400 text-xs md:text-sm leading-relaxed">
                    Kelola transaksi kasir, suku cadang, inventaris barang masuk, dan audit stok opname dalam satu ekosistem terpadu.
                </p>
            </div>

            <div class="relative z-10 pt-8 border-t border-[#222d47] text-xs text-slate-400 flex items-center justify-between">
                <span>&copy; {{ date('Y') }} Pradan Motor</span>
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="p-8 md:p-12 flex flex-col justify-center bg-white">
            <div class="max-w-sm w-full mx-auto">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Selamat Datang 👋</h1>
                    <p class="text-xs text-gray-400 mt-1">Masuk untuk mengakses aplikasi POS dan dashboard Anda</p>
                </div>

                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               placeholder="admin@pradanmotor.com" 
                               class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-gray-700">Password</label>
                        </div>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="••••••••" 
                               class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-gray-600">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                            Masuk ke Aplikasi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>