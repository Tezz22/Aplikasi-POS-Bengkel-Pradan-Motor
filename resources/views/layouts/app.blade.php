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

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 12mm 12mm;
            }
            #app-sidebar, 
            header, 
            .topbar-actions, 
            #form-filter-laporan, 
            .export-buttons, 
            .page-header-container,
            [id$="-pagination-footer"],
            .data-table-container > div:first-child,
            .action-col,
            [role="dialog"] {
                display: none !important;
            }
            body, .h-screen, .w-screen, .overflow-hidden, .flex-1 {
                height: auto !important;
                width: 100% !important;
                overflow: visible !important;
                background: #ffffff !important;
                color: #0f172a !important;
            }
            main {
                overflow: visible !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .max-w-7xl {
                max-width: 100% !important;
                padding: 0 !important;
            }
            .data-table-container {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: transparent !important;
            }
            /* Tampilkan seluruh baris data pada tabel cetak (override pagination js) */
            .data-table-container table tbody tr {
                display: table-row !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            table th, table td {
                border: 1px solid #94a3b8 !important;
                padding: 5px 8px !important;
                font-size: 10.5px !important;
                color: #0f172a !important;
            }
            table thead th {
                background-color: #f1f5f9 !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                border-bottom: 2px solid #475569 !important;
            }
            table tfoot td {
                background-color: #f8fafc !important;
                font-weight: 700 !important;
                border-top: 2px solid #475569 !important;
            }
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

    {{-- Modal Konfirmasi Logout --}}
    <x-modal 
        id="modal-logout" 
        title="Konfirmasi Logout" 
        :formAction="route('logout')"
        formMethod="POST"
        submitText="Logout"
        submitVariant="danger"
        size="sm"
    >
        <div class="text-center py-2">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto mb-3.5 shadow-2xs">
                @include('components.icon', ['name' => 'logout', 'size' => 26])
            </div>
            <h4 class="text-sm font-bold text-gray-900 mb-1.5">Keluar dari Sistem?</h4>
            <p class="text-xs text-gray-500 leading-relaxed max-w-[280px] mx-auto">
                Apakah Anda yakin ingin keluar? Pastikan semua transaksi atau data yang sedang dikerjakan telah disimpan.
            </p>
        </div>
    </x-modal>

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

                this.perPageButtons = container.querySelectorAll(`[data-per-page-val][data-table-target="${this.tableId}"]`);
                if (!this.perPageButtons || this.perPageButtons.length === 0) {
                    this.perPageButtons = container.querySelectorAll('.data-table-per-page-btn');
                }
                this.perPageSelect = container.querySelector(`[data-per-page-for="${this.tableId}"]`) || container.querySelector('.data-table-per-page');
                this.infoEl = container.querySelector(`[data-info-for="${this.tableId}"]`) || container.querySelector('.data-table-info');
                this.paginationControls = container.querySelector(`[data-pagination-for="${this.tableId}"]`) || container.querySelector('.data-table-pagination');

                const initialPerPage = parseInt(this.container.dataset.perPage, 10);
                this.perPage = !isNaN(initialPerPage) ? initialPerPage : (this.perPageSelect ? parseInt(this.perPageSelect.value, 10) : 10);
                this.currentPage = 1;
                this.searchQuery = '';
                this.currentSort = { colIndex: null, direction: null };

                const firstTh = this.table.querySelector('thead th:first-child');
                const firstThText = firstTh ? firstTh.innerText.toLowerCase().trim() : '';
                this.hasNumberCol = ['no', 'no.', '#', 'nomor'].includes(firstThText);

                this.refreshRows();
                this.bindEvents();
                this.updatePerPageButtons();
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

                this.originalRows = [...this.allRows];
                if (this.currentSort.colIndex !== null && this.currentSort.direction) {
                    this.applySort(false);
                } else {
                    this.applyFilter();
                }
            }

            bindEvents() {
                if (this.perPageButtons && this.perPageButtons.length > 0) {
                    this.perPageButtons.forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            const val = parseInt(btn.dataset.perPageVal, 10);
                            if (val && val !== this.perPage) {
                                this.setPerPage(val);
                            }
                        });
                    });
                }

                if (this.perPageSelect) {
                    this.perPageSelect.addEventListener('change', () => {
                        this.setPerPage(parseInt(this.perPageSelect.value, 10) || 10);
                    });
                }

                this.bindSortEvents();
            }

            bindSortEvents() {
                const thead = this.table.querySelector('thead');
                if (!thead) return;

                const ths = thead.querySelectorAll('th');
                ths.forEach((th, idx) => {
                    const text = th.innerText.toLowerCase().trim();
                    const isExcluded = th.dataset.sortable === 'false' || 
                                       ['aksi', 'action', 'actions', 'no', 'no.', '#', 'nomor', ''].includes(text);
                    if (isExcluded) return;

                    th.classList.add('cursor-pointer', 'select-none', 'group');
                    if (!th.hasAttribute('title')) {
                        th.setAttribute('title', 'Klik untuk mengurutkan (ASC / DESC)');
                    }
                    if (!th.hasAttribute('tabindex')) {
                        th.setAttribute('tabindex', '0');
                    }

                    // Inject sort indicator if not pre-rendered by Blade component
                    if (!th.querySelector('.data-table-sort-indicator')) {
                        const rawContent = th.innerHTML;
                        th.innerHTML = `
                            <div class="inline-flex items-center gap-1.5">
                                <span class="data-table-th-text transition-colors">${rawContent}</span>
                                <span class="data-table-sort-indicator inline-flex items-center shrink-0 text-gray-300 transition-colors">
                                    <svg class="sort-icon-idle w-3 h-3 group-hover:text-gray-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m7 15 5 5 5-5"/>
                                        <path d="m7 9 5-5 5 5"/>
                                    </svg>
                                    <svg class="sort-icon-asc w-3 h-3 text-blue-600 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="18 15 12 9 6 15"/>
                                    </svg>
                                    <svg class="sort-icon-desc w-3 h-3 text-blue-600 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </span>
                            </div>
                        `;
                    }

                    const triggerSort = (e) => {
                        e.preventDefault();
                        this.sortBy(idx);
                    };

                    th.addEventListener('click', triggerSort);
                    th.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            triggerSort(e);
                        }
                    });
                });
            }

            sortBy(colIndex) {
                let nextDirection = 'asc';
                if (this.currentSort.colIndex === colIndex) {
                    nextDirection = this.currentSort.direction === 'asc' ? 'desc' : 'asc';
                }

                this.currentSort = { colIndex, direction: nextDirection };
                this.applySort(true);
                this.updateSortUI();
                this.currentPage = 1;
                this.render();
            }

            applySort(reorderDOM = true) {
                if (this.currentSort.colIndex === null || !this.currentSort.direction) {
                    this.applyFilter();
                    return;
                }

                const { colIndex, direction } = this.currentSort;
                const factor = direction === 'asc' ? 1 : -1;

                this.allRows.sort((rowA, rowB) => {
                    const cellA = rowA.children[colIndex];
                    const cellB = rowB.children[colIndex];
                    const valA = this.getCellValue(cellA);
                    const valB = this.getCellValue(cellB);

                    if (typeof valA === 'number' && typeof valB === 'number') {
                        return (valA - valB) * factor;
                    }

                    return String(valA).localeCompare(String(valB), 'id', { numeric: true, sensitivity: 'base' }) * factor;
                });

                if (reorderDOM) {
                    this.allRows.forEach(tr => this.tbody.appendChild(tr));
                }

                this.applyFilter();
            }

            getCellValue(cell) {
                if (!cell) return '';
                if (cell.dataset.sort !== undefined) return cell.dataset.sort;
                if (cell.dataset.value !== undefined) return cell.dataset.value;

                const raw = cell.innerText.trim();
                if (!raw || raw === '-') return '';

                // Indonesian Rupiah: "Rp 52.000", "Rp 1.500.000", "Rp. 25.000,00"
                const rpMatch = raw.match(/^rp\.?\s*([0-9.,]+)/i);
                if (rpMatch) {
                    const numStr = rpMatch[1].replace(/\./g, '').replace(',', '.');
                    const num = parseFloat(numStr);
                    if (!isNaN(num)) return num;
                }

                // Hashtag ID: "#12"
                if (/^#\d+$/.test(raw)) {
                    return parseInt(raw.substring(1), 10);
                }

                // Carbon date with month in Indonesian: e.g. "31 Okt 2026, 14:30" or "12 Jan 2026"
                const dateMatch = raw.match(/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})(?:,\s*(\d{1,2}):(\d{2}))?/);
                if (dateMatch) {
                    const months = {
                        jan: 0, januari: 0, feb: 1, februari: 1, mar: 2, maret: 2,
                        apr: 3, april: 3, mei: 4, may: 4, jun: 5, juni: 5,
                        jul: 6, juli: 6, agu: 7, agustus: 7, agt: 7, aug: 7,
                        sep: 8, september: 8, okt: 9, oktober: 9, oct: 9,
                        nov: 10, november: 10, des: 11, desember: 11, dec: 11
                    };
                    const mKey = dateMatch[2].toLowerCase();
                    if (months[mKey] !== undefined) {
                        const day = parseInt(dateMatch[1], 10);
                        const month = months[mKey];
                        const year = parseInt(dateMatch[3], 10);
                        const hour = dateMatch[4] ? parseInt(dateMatch[4], 10) : 0;
                        const min = dateMatch[5] ? parseInt(dateMatch[5], 10) : 0;
                        return new Date(year, month, day, hour, min).getTime();
                    }
                }

                // ISO date: "2026-10-31" or "2026-10-31 14:30:00"
                if (/^\d{4}-\d{2}-\d{2}/.test(raw)) {
                    const t = Date.parse(raw);
                    if (!isNaN(t)) return t;
                }

                // Number with unit/suffix, e.g. "10 Item", "5 Pcs", "90%"
                const suffixNumMatch = raw.match(/^([0-9]+(?:\.[0-9]+)?)\s*[a-zA-Z%]+$/);
                if (suffixNumMatch && !raw.includes('-')) {
                    const num = parseFloat(suffixNumMatch[1]);
                    if (!isNaN(num)) return num;
                }

                // Pure number or Indonesian thousand separated integer e.g. "1.500" or "50"
                const cleanNum = raw.replace(/\./g, '').replace(',', '.');
                if (/^-?\d+(\.\d+)?$/.test(cleanNum) && !raw.includes('-')) {
                    const num = parseFloat(cleanNum);
                    if (!isNaN(num)) return num;
                }

                return raw;
            }

            updateSortUI() {
                const thead = this.table.querySelector('thead');
                if (!thead) return;

                const ths = thead.querySelectorAll('th');
                ths.forEach((th, idx) => {
                    const indicator = th.querySelector('.data-table-sort-indicator');
                    const textSpan = th.querySelector('.data-table-th-text');
                    if (!indicator) return;

                    const iconIdle = indicator.querySelector('.sort-icon-idle');
                    const iconAsc = indicator.querySelector('.sort-icon-asc');
                    const iconDesc = indicator.querySelector('.sort-icon-desc');

                    if (this.currentSort.colIndex === idx) {
                        th.classList.add('bg-blue-50/70');
                        if (textSpan) {
                            textSpan.classList.add('text-blue-600', 'font-bold');
                            textSpan.classList.remove('text-gray-400');
                        }

                        if (this.currentSort.direction === 'asc') {
                            th.setAttribute('aria-sort', 'ascending');
                            th.setAttribute('title', 'Diurutkan ASC (A-Z / Kecil-Besar). Klik untuk DESC.');
                            if (iconIdle) iconIdle.classList.add('hidden');
                            if (iconAsc) iconAsc.classList.remove('hidden');
                            if (iconDesc) iconDesc.classList.add('hidden');
                        } else {
                            th.setAttribute('aria-sort', 'descending');
                            th.setAttribute('title', 'Diurutkan DESC (Z-A / Besar-Kecil). Klik untuk ASC.');
                            if (iconIdle) iconIdle.classList.add('hidden');
                            if (iconAsc) iconAsc.classList.add('hidden');
                            if (iconDesc) iconDesc.classList.remove('hidden');
                        }
                    } else {
                        th.classList.remove('bg-blue-50/70');
                        th.setAttribute('aria-sort', 'none');
                        th.setAttribute('title', 'Klik untuk mengurutkan (ASC / DESC)');
                        if (textSpan) {
                            textSpan.classList.remove('text-blue-600', 'font-bold');
                        }
                        if (iconIdle) iconIdle.classList.remove('hidden');
                        if (iconAsc) iconAsc.classList.add('hidden');
                        if (iconDesc) iconDesc.classList.add('hidden');
                    }
                });
            }

            setPerPage(val) {
                this.perPage = val || 10;
                this.currentPage = 1;
                this.updatePerPageButtons();
                this.render();
            }

            updatePerPageButtons() {
                if (this.perPageButtons && this.perPageButtons.length > 0) {
                    this.perPageButtons.forEach(btn => {
                        const val = parseInt(btn.dataset.perPageVal, 10);
                        const isActive = val === this.perPage;
                        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                        if (isActive) {
                            btn.className = 'data-table-per-page-btn px-2.5 py-1 rounded-lg text-xs transition-all select-none cursor-pointer bg-white text-blue-600 shadow-2xs font-bold';
                        } else {
                            btn.className = 'data-table-per-page-btn px-2.5 py-1 rounded-lg text-xs transition-all select-none cursor-pointer text-gray-500 hover:text-gray-900 font-medium';
                        }
                    });
                }
                if (this.perPageSelect && parseInt(this.perPageSelect.value, 10) !== this.perPage) {
                    this.perPageSelect.value = this.perPage;
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
                const isPaginated = this.container.dataset.paginate !== 'false';
                const totalPages = isPaginated ? Math.max(1, Math.ceil(total / this.perPage)) : 1;

                if (this.currentPage > totalPages) {
                    this.currentPage = totalPages;
                }
                if (this.currentPage < 1) {
                    this.currentPage = 1;
                }

                const startIndex = isPaginated ? (this.currentPage - 1) * this.perPage : 0;
                const endIndex = isPaginated ? Math.min(startIndex + this.perPage, total) : total;

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
                        const row = this.filteredRows[i];
                        if (row) {
                            row.style.display = '';
                            if (this.hasNumberCol && row.children && row.children[0]) {
                                row.children[0].textContent = (i + 1).toString();
                            }
                        }
                    }
                }

                if (isPaginated) {
                    this.renderInfo(totalOriginal, total, startIndex, endIndex);
                    this.renderControls(totalPages, total);
                }
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

                // Tombol Sebelumnya (<)
                html += `
                    <button type="button" 
                            data-page="${this.currentPage - 1}" 
                            ${isPrevDisabled ? 'disabled' : ''} 
                            aria-label="Halaman sebelumnya"
                            title="Halaman sebelumnya"
                            class="h-8 w-8 rounded-lg border text-xs font-medium flex items-center justify-center transition-all select-none ${
                                isPrevDisabled 
                                    ? 'border-gray-100 bg-gray-50/50 text-gray-300 cursor-not-allowed' 
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer shadow-2xs'
                            }">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
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

                // Tombol Selanjutnya (>)
                html += `
                    <button type="button" 
                            data-page="${this.currentPage + 1}" 
                            ${isNextDisabled ? 'disabled' : ''} 
                            aria-label="Halaman selanjutnya"
                            title="Halaman selanjutnya"
                            class="h-8 w-8 rounded-lg border text-xs font-medium flex items-center justify-center transition-all select-none ${
                                isNextDisabled 
                                    ? 'border-gray-100 bg-gray-50/50 text-gray-300 cursor-not-allowed' 
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900 cursor-pointer shadow-2xs'
                            }">
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
            document.querySelectorAll('.data-table-container').forEach(container => {
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