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

    <!-- Global Helpers for Table Pagination, Search & Modal -->
    <script>
        class TablePagination {
            constructor(container) {
                this.container = container;
                this.table = container.querySelector('table');
                if (!this.table) return;
                this.tableId = this.table.id;
                this.tbody = this.table.querySelector('tbody');
                if (!this.tbody) return;

                this.table.tablePagination = this;
                this.container.tablePagination = this;

                this.perPageSelect = container.querySelector(`[data-per-page-for="${this.tableId}"]`) || container.querySelector('.data-table-per-page');
                this.infoEl = container.querySelector(`[data-info-for="${this.tableId}"]`) || container.querySelector('.data-table-info');
                this.paginationControls = container.querySelector(`[data-pagination-for="${this.tableId}"]`) || container.querySelector('.data-table-pagination');

                const initialPerPage = parseInt(this.container.dataset.perPage, 10);
                this.perPage = !isNaN(initialPerPage) ? initialPerPage : (this.perPageSelect ? parseInt(this.perPageSelect.value, 10) : 10);
                this.currentPage = 1;
                this.searchQuery = '';

                this.refreshRows();
                this.bindEvents();
                this.render();
            }

            refreshRows() {
                this.initialEmptyRow = null;
                const rows = Array.from(this.tbody.querySelectorAll('tr')).filter(tr => !tr.classList.contains('data-table-no-results'));
                
                this.allRows = [];
                rows.forEach(tr => {
                    const isColspan = tr.children.length === 1 && tr.children[0].hasAttribute('colspan');
                    if (isColspan) {
                        this.initialEmptyRow = tr;
                    } else {
                        this.allRows.push(tr);
                    }
                });

                this.applyFilter();
            }

            bindEvents() {
                if (this.perPageSelect) {
                    this.perPageSelect.addEventListener('change', () => {
                        this.perPage = parseInt(this.perPageSelect.value, 10) || 10;
                        this.currentPage = 1;
                        this.render();
                    });
                }
            }

            filter(query) {
                this.searchQuery = (query || '').toLowerCase().trim();
                this.applyFilter();
                this.currentPage = 1;
                this.render();
            }

            applyFilter() {
                if (!this.searchQuery) {
                    this.filteredRows = [...this.allRows];
                } else {
                    this.filteredRows = this.allRows.filter(row => {
                        return row.innerText.toLowerCase().includes(this.searchQuery);
                    });
                }
            }

            render() {
                const totalOriginal = this.allRows.length;
                const total = this.filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(total / this.perPage));

                if (this.currentPage > totalPages) {
                    this.currentPage = totalPages;
                }
                if (this.currentPage < 1) {
                    this.currentPage = 1;
                }

                const startIndex = (this.currentPage - 1) * this.perPage;
                const endIndex = Math.min(startIndex + this.perPage, total);

                // Hide all standard rows first
                this.allRows.forEach(row => {
                    row.style.display = 'none';
                });

                // Clear previous search no-results row
                const existingNoResults = this.tbody.querySelector('.data-table-no-results');
                if (existingNoResults) {
                    existingNoResults.remove();
                }

                if (totalOriginal === 0) {
                    if (this.initialEmptyRow) {
                        this.initialEmptyRow.style.display = '';
                    }
                } else if (total === 0) {
                    if (this.initialEmptyRow) {
                        this.initialEmptyRow.style.display = 'none';
                    }
                    const thCount = this.table.querySelectorAll('thead th').length || 7;
                    const noResultsTr = document.createElement('tr');
                    noResultsTr.className = 'data-table-no-results';
                    noResultsTr.innerHTML = `
                        <td colspan="${thCount}" class="py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                <svg class="w-6 h-6 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" stroke-width="1.8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="1.8" stroke-linecap="round"></line>
                                </svg>
                                <span class="text-xs">Tidak ada data yang cocok dengan pencarian "<strong>${this.escapeHtml(this.searchQuery)}</strong>"</span>
                            </div>
                        </td>
                    `;
                    this.tbody.appendChild(noResultsTr);
                } else {
                    if (this.initialEmptyRow) {
                        this.initialEmptyRow.style.display = 'none';
                    }
                    for (let i = startIndex; i < endIndex; i++) {
                        if (this.filteredRows[i]) {
                            this.filteredRows[i].style.display = '';
                        }
                    }
                }

                this.renderInfo(totalOriginal, total, startIndex, endIndex);
                this.renderControls(totalPages, total);
            }

            renderInfo(totalOriginal, total, startIndex, endIndex) {
                if (!this.infoEl) return;

                if (totalOriginal === 0) {
                    this.infoEl.innerHTML = 'Belum ada data';
                    return;
                }

                if (total === 0) {
                    this.infoEl.innerHTML = `Menampilkan <span class="font-bold text-gray-800">0</span> data <span class="text-gray-400 font-normal">(difilter dari ${totalOriginal} total data)</span>`;
                    return;
                }

                const displayStart = startIndex + 1;
                const displayEnd = endIndex;

                if (this.searchQuery && total !== totalOriginal) {
                    this.infoEl.innerHTML = `Menampilkan <span class="font-bold text-gray-800">${displayStart}</span> sampai <span class="font-bold text-gray-800">${displayEnd}</span> dari <span class="font-bold text-gray-800">${total}</span> data <span class="text-gray-400 font-normal">(difilter dari ${totalOriginal} total data)</span>`;
                } else {
                    this.infoEl.innerHTML = `Menampilkan <span class="font-bold text-gray-800">${displayStart}</span> sampai <span class="font-bold text-gray-800">${displayEnd}</span> dari <span class="font-bold text-gray-800">${total}</span> data`;
                }
            }

            renderControls(totalPages, total) {
                if (!this.paginationControls) return;

                const isPrevDisabled = this.currentPage <= 1 || total === 0;
                const isNextDisabled = this.currentPage >= totalPages || total === 0;

                let html = '';

                // Tombol Sebelumnya (Previous)
                html += `
                    <button type="button" 
                            data-page="${this.currentPage - 1}" 
                            ${isPrevDisabled ? 'disabled' : ''} 
                            aria-label="Halaman sebelumnya"
                            class="h-8 px-2.5 rounded-lg border text-xs font-medium flex items-center gap-1 transition-all select-none ${
                                isPrevDisabled 
                                    ? 'border-gray-100 bg-gray-50/50 text-gray-300 cursor-not-allowed' 
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer shadow-2xs'
                            }">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </button>
                `;

                // Nomor Halaman
                const pages = this.getPageNumbers(totalPages, this.currentPage);
                pages.forEach(p => {
                    if (p === '...') {
                        html += `<span class="h-8 min-w-[28px] sm:min-w-[32px] px-1 flex items-center justify-center text-xs text-gray-400 font-bold select-none">...</span>`;
                    } else {
                        const isActive = p === this.currentPage;
                        html += `
                            <button type="button" 
                                    data-page="${p}" 
                                    ${total === 0 ? 'disabled' : ''}
                                    class="h-8 min-w-[28px] sm:min-w-[32px] px-2 rounded-lg border text-xs font-semibold flex items-center justify-center transition-all cursor-pointer ${
                                        isActive 
                                            ? 'bg-blue-600 border-blue-600 text-white shadow-2xs' 
                                            : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900 shadow-2xs'
                                    }">
                                ${p}
                            </button>
                        `;
                    }
                });

                // Tombol Selanjutnya (Next)
                html += `
                    <button type="button" 
                            data-page="${this.currentPage + 1}" 
                            ${isNextDisabled ? 'disabled' : ''} 
                            aria-label="Halaman selanjutnya"
                            class="h-8 px-2.5 rounded-lg border text-xs font-medium flex items-center gap-1 transition-all select-none ${
                                isNextDisabled 
                                    ? 'border-gray-100 bg-gray-50/50 text-gray-300 cursor-not-allowed' 
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer shadow-2xs'
                            }">
                        <span class="hidden sm:inline">Selanjutnya</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                `;

                this.paginationControls.innerHTML = html;

                this.paginationControls.querySelectorAll('button[data-page]:not([disabled])').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const targetPage = parseInt(btn.dataset.page, 10);
                        if (targetPage && targetPage !== this.currentPage && targetPage >= 1 && targetPage <= totalPages) {
                            this.currentPage = targetPage;
                            this.render();
                        }
                    });
                });
            }

            getPageNumbers(total, current) {
                if (total <= 7) {
                    return Array.from({ length: total }, (_, i) => i + 1);
                }
                if (current <= 4) {
                    return [1, 2, 3, 4, 5, '...', total];
                }
                if (current >= total - 3) {
                    return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
                }
                return [1, '...', current - 1, current, current + 1, '...', total];
            }

            escapeHtml(str) {
                return (str || '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[m]));
            }
        }

        window.TablePagination = TablePagination;

        window.initDataTables = function() {
            document.querySelectorAll('.data-table-container[data-paginate="true"]').forEach(container => {
                if (!container.tablePagination) {
                    new TablePagination(container);
                } else {
                    container.tablePagination.refreshRows();
                    container.tablePagination.render();
                }
            });
        };

        // Realtime table search connected with TablePagination
        document.querySelectorAll('[data-search]').forEach(input => {
            input.addEventListener('input', () => {
                const target = document.querySelector(input.dataset.search);
                if (!target) return;

                if (target.tablePagination) {
                    target.tablePagination.filter(input.value);
                } else {
                    const query = input.value.toLowerCase().trim();
                    target.querySelectorAll('tbody tr').forEach(row => {
                        row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
                    });
                }
            });
        });

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', function() {
            window.initDataTables();
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