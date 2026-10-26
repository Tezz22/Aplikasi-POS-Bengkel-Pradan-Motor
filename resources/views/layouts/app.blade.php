<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pradan Motor') - Sistem POS Bengkel</title>

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
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }

        /* Collapsible Sidebar Styles */
        #app-sidebar {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #app-sidebar.collapsed,
        html.sidebar-init-collapsed #app-sidebar {
            width: 5rem !important; /* 80px */
        }
        #app-sidebar.collapsed .sidebar-text,
        html.sidebar-init-collapsed #app-sidebar .sidebar-text {
            display: none !important;
        }
        #app-sidebar.collapsed .sidebar-item,
        html.sidebar-init-collapsed #app-sidebar .sidebar-item {
            justify-content: center !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        #app-sidebar.collapsed .sidebar-dropdown-menu,
        html.sidebar-init-collapsed #app-sidebar .sidebar-dropdown-menu {
            display: none !important;
        }
        #app-sidebar.collapsed .brand-container,
        html.sidebar-init-collapsed #app-sidebar .brand-container {
            justify-content: center !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        #app-sidebar.collapsed .brand-link,
        html.sidebar-init-collapsed #app-sidebar .brand-link {
            justify-content: center !important;
        }
        #app-sidebar.collapsed .profile-container,
        html.sidebar-init-collapsed #app-sidebar .profile-container {
            justify-content: center !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        #app-sidebar.collapsed .profile-link,
        html.sidebar-init-collapsed #app-sidebar .profile-link {
            justify-content: center !important;
        }
    </style>
    <script>
        // Check stored sidebar state to prevent flicker
        (function() {
            if (localStorage.getItem('sidebar_collapsed') === '1') {
                document.documentElement.classList.add('sidebar-init-collapsed');
            }
        })();

        function toggleSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            if (!sidebar) return;
            const isCollapsed = sidebar.classList.toggle('collapsed');
            document.documentElement.classList.toggle('sidebar-init-collapsed', isCollapsed);
            localStorage.setItem('sidebar_collapsed', isCollapsed ? '1' : '0');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebar_collapsed') === '1') {
                document.getElementById('app-sidebar')?.classList.add('collapsed');
            }
        });
    </script>
</head>
<body class="bg-[#f8f9fa] text-slate-800 antialiased h-screen overflow-hidden">
    <!-- Fixed Shell Container -->
    <div class="flex h-screen w-screen overflow-hidden">
        <!-- Sidebar (Fixed height, only internal nav scrollable) -->
        @include('components.sidebar')

        <!-- Main Workspace (Fixed height, holds fixed Topbar + scrollable Content) -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
            <!-- Topbar (Fixed at top, non-scrolling) -->
            @include('components.topbar')

            <!-- Main Page Content (ONLY THIS AREA SCROLLS) -->
            <main class="flex-1 overflow-y-auto min-h-0 p-6 lg:p-8">
                <div class="max-w-7xl w-full mx-auto pb-16">
                    {{-- Flash Message Success --}}
                    @if (session('status'))
                        <div class="mb-6 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium shadow-xs">
                            <div class="flex items-center gap-2">
                                @include('components.icon', ['name' => 'stok-opname', 'size' => 18])
                                <span>{{ session('status') }}</span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                        </div>
                    @endif

                    {{-- Flash Message Error / Session --}}
                    @if (session('error'))
                        <div class="mb-6 flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium shadow-xs">
                            <div class="flex items-center gap-2">
                                @include('components.icon', ['name' => 'alert-triangle', 'size' => 18])
                                <span>{{ session('error') }}</span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
                        </div>
                    @endif
                    @if (isset($errors) && $errors->any())
                        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium shadow-xs">
                            <div class="flex items-center gap-2 mb-1">
                                @include('components.icon', ['name' => 'alert-triangle', 'size' => 18])
                                <span class="font-bold">Terjadi Kesalahan:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-5">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Quick Global Helpers for Table Search & Modal -->
    <script>
        // Realtime table search
        document.querySelectorAll('[data-search]').forEach(input => {
            input.addEventListener('input', () => {
                const target = document.querySelector(input.dataset.search);
                const query = input.value.toLowerCase().trim();
                if (target) {
                    target.querySelectorAll('tbody tr').forEach(row => {
                        row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
                    });
                }
            });
        });

        // Simple modal opener helper
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Close open modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });

        @if ($errors->any() && old('_modal_id'))
            document.addEventListener('DOMContentLoaded', function() {
                openModal(@json(old('_modal_id')));
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>