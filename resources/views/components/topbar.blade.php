{{-- ═══════════════════════════════════════════════
    Topbar Component (Tailwind CSS)
    ═══════════════════════════════════════════════ --}}
<header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 lg:px-8 shrink-0">
    <!-- Greeting & User Name -->
    <div>
        <span class="text-xs text-gray-400 block font-normal">Selamat Bekerja,</span>
        <span class="text-base font-bold text-gray-900 tracking-tight leading-tight">
            {{ auth()->user()->name ?? ((auth()->user()->role ?? 'admin') === 'admin' ? 'Tezzy' : 'Joo') }}
        </span>
    </div>

    <!-- Right Actions: Logout -->
    <div class="flex items-center gap-3">
        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin logout?');">
            @csrf
            <button type="submit" 
                    title="Keluar / Logout" 
                    class="w-10 h-10 rounded-xl flex items-center justify-center text-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                @include('components.icon', ['name' => 'logout', 'size' => 22])
            </button>
        </form>
    </div>
</header>
