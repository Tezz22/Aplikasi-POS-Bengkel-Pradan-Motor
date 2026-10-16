{{-- ═══════════════════════════════════════════════
    Sidebar Component (Tailwind CSS)
    Fixed height, collapsible shell with icon-only mode, scrollable nav menu and fixed bottom profile
    ═══════════════════════════════════════════════ --}}
@php($isAdmin = (auth()->user()->role ?? 'admin') === 'admin')
@php($currentRoute = Route::currentRouteName())

<aside id="app-sidebar" class="w-64 bg-[#182033] text-slate-300 flex flex-col shrink-0 h-screen overflow-hidden border-r border-[#222d47] select-none">
    <!-- Brand / Header (Fixed at top, shrink-0) -->
    <div class="h-16 shrink-0 flex items-center justify-between px-4 border-b border-[#222d47] bg-[#182033] brand-container">
        <a href="{{ $isAdmin ? route('admin.dashboard') : route('kasir.dashboard') }}" 
           class="brand-link flex items-center gap-3 group min-w-0" 
           title="Pradan Motor POS">
            <img src="{{ asset('assets/images/logo/logo-icon.svg') }}" 
                 alt="Logo Pradan Motor" 
                 class="w-8 h-8 rounded-xl object-contain shrink-0 shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform">
            <span class="sidebar-text text-white font-extrabold text-base tracking-wide group-hover:text-blue-400 transition-colors truncate">
                Pradan Motor
            </span>
        </a>
    </div>

    <!-- Navigation List (Scrolls only if items exceed screen height) -->
    <nav class="flex-1 overflow-y-auto min-h-0 px-3 py-4 space-y-1.5" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent;">
        @if ($isAdmin)
            {{-- ─── Admin Menu ─── --}}
            <a href="{{ route('admin.dashboard') }}" 
               title="Dashboard" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'admin.dashboard' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'admin.dashboard' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'dashboard', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Dashboard</span>
            </a>

            {{-- Master Data Dropdown --}}
            @php($masterRoutes = ['admin.pengguna', 'admin.barang', 'admin.kategori', 'admin.supplier', 'admin.layanan'])
            @php($masterOpen = in_array($currentRoute, $masterRoutes))
            <div>
                <button type="button" 
                        title="Master Data" 
                        onclick="if (document.getElementById('app-sidebar').classList.contains('collapsed')) { toggleSidebar(); } else { this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.arrow-icon').classList.toggle('rotate-180'); }" 
                        class="sidebar-item w-full flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $masterOpen ? 'bg-[#293652]/70 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    <div class="flex items-center gap-3.5">
                        <span class="{{ $masterOpen ? 'text-blue-400' : 'text-slate-400' }}">
                            @include('components.icon', ['name' => 'master-data', 'size' => 20])
                        </span>
                        <span class="sidebar-text truncate">Master Data</span>
                    </div>
                    <span class="sidebar-text arrow-icon transition-transform duration-200 {{ $masterOpen ? 'rotate-180 text-white' : 'text-slate-400' }}">
                        @include('components.icon', ['name' => 'chevron-down', 'size' => 15])
                    </span>
                </button>
                <div class="sidebar-dropdown-menu {{ $masterOpen ? '' : 'hidden' }} pl-9 pr-2 py-1.5 space-y-1">
                    <a href="{{ route('admin.pengguna') }}" class="flex items-center gap-2.5 py-2 px-3 rounded-lg text-xs font-medium transition-colors {{ $currentRoute === 'admin.pengguna' ? 'text-blue-400 font-semibold bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'admin.pengguna' ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                        Pengguna
                    </a>
                    <a href="{{ route('admin.barang') }}" class="flex items-center gap-2.5 py-2 px-3 rounded-lg text-xs font-medium transition-colors {{ $currentRoute === 'admin.barang' ? 'text-blue-400 font-semibold bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'admin.barang' ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                        Data Barang
                    </a>
                    <a href="{{ route('admin.kategori') }}" class="flex items-center gap-2.5 py-2 px-3 rounded-lg text-xs font-medium transition-colors {{ $currentRoute === 'admin.kategori' ? 'text-blue-400 font-semibold bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'admin.kategori' ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                        Kategori Barang
                    </a>
                    <a href="{{ route('admin.supplier') }}" class="flex items-center gap-2.5 py-2 px-3 rounded-lg text-xs font-medium transition-colors {{ $currentRoute === 'admin.supplier' ? 'text-blue-400 font-semibold bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'admin.supplier' ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                        Data Supplier
                    </a>
                    <a href="{{ route('admin.layanan') }}" class="flex items-center gap-2.5 py-2 px-3 rounded-lg text-xs font-medium transition-colors {{ $currentRoute === 'admin.layanan' ? 'text-blue-400 font-semibold bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'admin.layanan' ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                        Data Layanan
                    </a>
                </div>
            </div>

            {{-- Pelanggan --}}
            <a href="{{ route('admin.pelanggan') }}" 
               title="Pelanggan" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'admin.pelanggan' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'admin.pelanggan' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'pelanggan', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Pelanggan</span>
            </a>

            {{-- Gudang Dropdown --}}
            @php($gudangRoutes = ['admin.barang-masuk', 'admin.stok-opname'])
            @php($gudangOpen = in_array($currentRoute, $gudangRoutes))
            <div>
                <button type="button" 
                        title="Gudang" 
                        onclick="if (document.getElementById('app-sidebar').classList.contains('collapsed')) { toggleSidebar(); } else { this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.arrow-icon').classList.toggle('rotate-180'); }" 
                        class="sidebar-item w-full flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $gudangOpen ? 'bg-[#293652]/70 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                    <div class="flex items-center gap-3.5">
                        <span class="{{ $gudangOpen ? 'text-blue-400' : 'text-slate-400' }}">
                            @include('components.icon', ['name' => 'gudang', 'size' => 20])
                        </span>
                        <span class="sidebar-text truncate">Gudang</span>
                    </div>
                    <span class="sidebar-text arrow-icon transition-transform duration-200 {{ $gudangOpen ? 'rotate-180 text-white' : 'text-slate-400' }}">
                        @include('components.icon', ['name' => 'chevron-down', 'size' => 15])
                    </span>
                </button>
                <div class="sidebar-dropdown-menu {{ $gudangOpen ? '' : 'hidden' }} pl-9 pr-2 py-1.5 space-y-1">
                    <a href="{{ route('admin.barang-masuk') }}" class="flex items-center gap-2.5 py-2 px-3 rounded-lg text-xs font-medium transition-colors {{ $currentRoute === 'admin.barang-masuk' ? 'text-blue-400 font-semibold bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'admin.barang-masuk' ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                        Barang Masuk
                    </a>
                    <a href="{{ route('admin.stok-opname') }}" class="flex items-center gap-2.5 py-2 px-3 rounded-lg text-xs font-medium transition-colors {{ $currentRoute === 'admin.stok-opname' ? 'text-blue-400 font-semibold bg-white/5' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'admin.stok-opname' ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                        Stok Opname
                    </a>
                </div>
            </div>

            {{-- Riwayat Transaksi --}}
            <a href="{{ route('admin.riwayat') }}" 
               title="Riwayat Transaksi" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'admin.riwayat' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'admin.riwayat' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'riwayat', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Riwayat Transaksi</span>
            </a>

            {{-- Laporan --}}
            <a href="{{ route('admin.laporan') }}" 
               title="Laporan" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'admin.laporan' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'admin.laporan' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'laporan', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Laporan</span>
            </a>

        @else
            {{-- ─── Kasir Menu ─── --}}
            <a href="{{ route('kasir.dashboard') }}" 
               title="Dashboard" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'kasir.dashboard' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'kasir.dashboard' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'dashboard', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Dashboard</span>
            </a>

            <a href="{{ route('kasir.transaksi') }}" 
               title="Transaksi Penjualan (POS)" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'kasir.transaksi' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'kasir.transaksi' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'transaksi', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Transaksi Penjualan</span>
            </a>

            <a href="{{ route('kasir.pelanggan') }}" 
               title="Pelanggan" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'kasir.pelanggan' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'kasir.pelanggan' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'pelanggan', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Pelanggan</span>
            </a>

            <a href="{{ route('kasir.stok') }}" 
               title="Stok Barang" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'kasir.stok' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'kasir.stok' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'stok', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Stok Barang</span>
            </a>

            <a href="{{ route('kasir.riwayat') }}" 
               title="Riwayat Transaksi" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'kasir.riwayat' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'kasir.riwayat' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'riwayat', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Riwayat Transaksi</span>
            </a>

            <a href="{{ route('kasir.laporan') }}" 
               title="Laporan Kasir" 
               class="sidebar-item flex items-center gap-3.5 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all {{ $currentRoute === 'kasir.laporan' ? 'bg-[#293652] text-white shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                <span class="{{ $currentRoute === 'kasir.laporan' ? 'text-blue-400' : 'text-slate-400' }}">
                    @include('components.icon', ['name' => 'laporan', 'size' => 20])
                </span>
                <span class="sidebar-text truncate">Laporan Kasir</span>
            </a>
        @endif
    </nav>

    <!-- User Profile Footer (Fixed at the bottom of the sidebar at all times) -->
    <div class="shrink-0 p-3.5 border-t border-[#222d47] flex items-center bg-[#131a29]/90 z-20 profile-container">
        <a href="{{ $isAdmin ? route('admin.profil') : route('kasir.profil') }}" 
           class="profile-link flex items-center gap-3 w-full group"
           title="{{ auth()->user()->name ?? ($isAdmin ? 'Tezzy' : 'Joo') }} ({{ ucfirst(auth()->user()->role ?? ($isAdmin ? 'Admin' : 'Kasir')) }})">
            <div class="relative shrink-0">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=faces" 
                     alt="Avatar" 
                     class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-500/30 group-hover:ring-blue-400 transition-all">
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-[#182033]"></span>
            </div>
            <div class="sidebar-text flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate group-hover:text-blue-300 transition-colors">
                    {{ auth()->user()->name ?? ($isAdmin ? 'Tezzy' : 'Joo') }}
                </p>
                <p class="text-xs font-medium text-blue-400 capitalize">
                    {{ auth()->user()->role ?? ($isAdmin ? 'Admin' : 'Kasir') }}
                </p>
            </div>
        </a>
    </div>
</aside>

