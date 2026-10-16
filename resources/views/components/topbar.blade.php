{{-- ═══════════════════════════════════════════════
    Topbar Component (Tailwind CSS)
    ═══════════════════════════════════════════════ --}}
@php
    $isAdmin = (auth()->user()->role ?? 'admin') === 'admin';
    $dashRoute = $isAdmin ? route('admin.dashboard') : route('kasir.dashboard');
    $routeName = Route::currentRouteName();

    $breadcrumbs = [];
    switch ($routeName) {
        // Admin routes
        case 'admin.dashboard':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => null],
            ];
            break;
        case 'admin.pengguna':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Master Data', 'url' => route('admin.barang')],
                ['label' => 'Data Pengguna', 'url' => null],
            ];
            break;
        case 'admin.barang':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Master Data', 'url' => route('admin.barang')],
                ['label' => 'Data Barang', 'url' => null],
            ];
            break;
        case 'admin.kategori':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Master Data', 'url' => route('admin.barang')],
                ['label' => 'Kategori Barang', 'url' => null],
            ];
            break;
        case 'admin.supplier':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Master Data', 'url' => route('admin.barang')],
                ['label' => 'Data Supplier', 'url' => null],
            ];
            break;
        case 'admin.layanan':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Master Data', 'url' => route('admin.barang')],
                ['label' => 'Data Layanan', 'url' => null],
            ];
            break;
        case 'admin.pelanggan':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Data Pelanggan', 'url' => null],
            ];
            break;
        case 'admin.barang-masuk':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Gudang', 'url' => route('admin.barang-masuk')],
                ['label' => 'Barang Masuk', 'url' => null],
            ];
            break;
        case 'admin.stok-opname':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Gudang', 'url' => route('admin.stok-opname')],
                ['label' => 'Stok Opname', 'url' => null],
            ];
            break;
        case 'admin.riwayat':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Riwayat Transaksi', 'url' => null],
            ];
            break;
        case 'admin.laporan':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Laporan Penjualan', 'url' => null],
            ];
            break;
        case 'admin.profil':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Akun', 'url' => null],
                ['label' => 'Profil Pengguna', 'url' => null],
            ];
            break;

        // Kasir routes
        case 'kasir.dashboard':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => null],
            ];
            break;
        case 'kasir.transaksi':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Transaksi Penjualan (POS)', 'url' => null],
            ];
            break;
        case 'kasir.pelanggan':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Data Pelanggan', 'url' => null],
            ];
            break;
        case 'kasir.stok':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Stok Barang', 'url' => null],
            ];
            break;
        case 'kasir.riwayat':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Riwayat Transaksi', 'url' => null],
            ];
            break;
        case 'kasir.laporan':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Laporan Kasir', 'url' => null],
            ];
            break;
        case 'kasir.profil':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Akun', 'url' => null],
                ['label' => 'Profil Kasir', 'url' => null],
            ];
            break;

        case 'playground':
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => 'Developer', 'url' => null],
                ['label' => 'Component Playground', 'url' => null],
            ];
            break;

        default:
            $cleanName = ucwords(str_replace(['admin.', 'kasir.', '-', '_'], ['', '', ' ', ' '], (string) $routeName));
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => $dashRoute],
                ['label' => $cleanName ?: 'Halaman', 'url' => null],
            ];
            break;
    }
@endphp

<header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
    <!-- Left: Sidebar Toggle + Divider + Breadcrumb Path -->
    <div class="flex items-center gap-3.5 min-w-0">
        <button type="button" 
                onclick="toggleSidebar()" 
                class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors cursor-pointer focus:outline-none shrink-0"
                title="Buka / Tutup Sidebar">
            @include('components.icon', ['name' => 'sidebar', 'size' => 19])
        </button>

        <span class="h-4 w-px bg-gray-200 shrink-0"></span>

        <!-- Breadcrumb Links -->
        <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs sm:text-sm overflow-x-auto py-1 scrollbar-none">
            @foreach ($breadcrumbs as $index => $item)
                @if ($index > 0)
                    <span class="text-gray-300 font-normal select-none">/</span>
                @endif

                @if ($loop->last || empty($item['url']))
                    <span class="{{ $loop->last ? 'font-bold text-gray-900' : 'font-medium text-gray-500' }} truncate">
                        {{ $item['label'] }}
                    </span>
                @else
                    <a href="{{ $item['url'] }}" 
                       class="font-medium text-gray-500 hover:text-blue-600 transition-colors truncate">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

    <!-- Right Actions: Tanggal & Logout -->
    <div class="flex items-center gap-3 shrink-0">
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200/80 rounded-xl text-xs font-semibold text-gray-600">
            <span class="text-gray-400 font-normal">Hari ini:</span>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin logout?');">
            @csrf
            <button type="submit" 
                    title="Keluar / Logout" 
                    class="w-9 h-9 rounded-xl flex items-center justify-center text-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                @include('components.icon', ['name' => 'logout', 'size' => 20])
            </button>
        </form>
    </div>
</header>
