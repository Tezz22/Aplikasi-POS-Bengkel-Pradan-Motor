@extends('layouts.app')

@section('title', 'Component Playground')

@section('content')
    {{-- 1. Page Header Component --}}
    <x-page-header 
        title="Component Playground" 
        subtitle="Halaman eksplorasi dan uji coba seluruh Blade Components aplikasi POS Pradan Motor" 
        icon="master-data"
    >
        <x-slot:actions>
            <x-button 
                variant="secondary" 
                size="sm" 
                icon="refresh" 
                text="Refresh" 
                onclick="window.location.reload()" 
            />
            <x-button 
                variant="primary" 
                size="sm" 
                icon="plus" 
                text="Buka Modal Uji Coba" 
                onclick="openModal('playground-demo-modal')" 
            />
        </x-slot:actions>
    </x-page-header>

    <div class="space-y-8">
        {{-- 2. Stat Cards & Summary Cards --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Stat Cards (<code class="text-blue-600 text-xs font-mono">&lt;x-stat-card&gt;</code>)</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card 
                    label="TOTAL PENJUALAN" 
                    value="Rp 12.850.000" 
                    icon="dollar" 
                    color="emerald" 
                    sub="+14.2% dari minggu lalu" 
                />
                <x-stat-card 
                    label="TRANSAKSI HARI INI" 
                    value="48 Trx" 
                    icon="bag" 
                    color="blue" 
                    sub="Rata-rata 6 trx / jam" 
                />
                <x-stat-card 
                    label="PELANGGAN SERVIS" 
                    value="23 Motor" 
                    icon="layanan" 
                    color="purple" 
                    sub="Unit masuk bengkel" 
                />
                <x-stat-card 
                    label="STOK MENIPIS" 
                    value="3 Item" 
                    icon="alert" 
                    color="rose" 
                    sub="Perlu order ke supplier" 
                />
            </div>
        </div>

        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Summary Cards (<code class="text-blue-600 text-xs font-mono">&lt;x-summary-card&gt;</code>)</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-summary-card 
                    label="TOTAL BARANG" 
                    value="150" 
                    unit="SKU" 
                    sub="Suku cadang aktif" 
                    icon="barang" 
                    type="info" 
                />
                <x-summary-card 
                    label="STOK AMAN" 
                    value="138" 
                    unit="Produk" 
                    sub="Stok di atas batas minimum" 
                    icon="check" 
                    type="success" 
                />
                <x-summary-card 
                    label="STOK KRITIS" 
                    value="9" 
                    unit="Produk" 
                    sub="Stok kurang dari 10" 
                    icon="alert" 
                    type="warning" 
                />
                <x-summary-card 
                    label="STOK HABIS" 
                    value="3" 
                    unit="Produk" 
                    sub="Segera restock gudang" 
                    icon="close" 
                    type="danger" 
                />
            </div>
        </div>

        {{-- 3. Buttons & Action Buttons --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Button Components (<code class="text-blue-600 text-xs font-mono">&lt;x-button&gt;</code> & <code class="text-blue-600 text-xs font-mono">&lt;x-button-action&gt;</code>)</h3>
            </div>

            <!-- Button Variants -->
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-2">Button Variants (size="md"):</p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <x-button variant="primary" icon="plus" text="Primary Button" onclick="alert('Primary clicked')" />
                        <x-button variant="secondary" icon="refresh" text="Secondary" onclick="alert('Secondary clicked')" />
                        <x-button variant="success" icon="check" text="Success Button" onclick="alert('Success clicked')" />
                        <x-button variant="danger" icon="trash" text="Danger Button" onclick="alert('Danger clicked')" />
                        <x-button variant="outline-primary" icon="edit" text="Outline Primary" onclick="alert('Outline clicked')" />
                        <x-button variant="outline-success" icon="dollar" text="Outline Success" onclick="alert('Outline success clicked')" />
                        <x-button variant="outline-danger" icon="close" text="Outline Danger" onclick="alert('Outline danger clicked')" />
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-2">Button Sizes:</p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <x-button size="sm" variant="primary" icon="plus" text="Small (size='sm')" />
                        <x-button size="md" variant="primary" icon="plus" text="Medium (size='md')" />
                        <x-button size="lg" variant="primary" icon="plus" text="Large (size='lg')" />
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-2">Action Buttons (<code class="font-mono text-blue-600">&lt;x-button-action&gt;</code>):</p>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl w-fit">
                        <x-button-action type="info" onclick="alert('Detail info')" />
                        <x-button-action type="edit" onclick="alert('Edit aksi')" />
                        <x-button-action type="print" onclick="alert('Cetak struk')" />
                        <x-button-action type="delete" onclick="alert('Hapus data')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Badges --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Badge Components (<code class="text-blue-600 text-xs font-mono">&lt;x-badge&gt;</code>)</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-2">Badge Types (size="sm"):</p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <x-badge type="success" text="Lunas / Tersedia (success)" />
                        <x-badge type="warning" text="Stok Menipis / Pending (warning)" />
                        <x-badge type="danger" text="Stok Habis / Batal (danger)" />
                        <x-badge type="info" text="Admin / QRIS (info)" />
                        <x-badge type="neutral" text="Non-Member / Umum (neutral)" />
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-2">Badge Sizes (size="md"):</p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <x-badge type="success" size="md" text="Badge Medium Success" />
                        <x-badge type="info" size="md" text="Badge Medium Info" />
                        <x-badge type="warning" size="md" text="Badge Medium Warning" />
                        <x-badge type="danger" size="md" text="Badge Medium Danger" />
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Form Inputs, Select, and Search --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Form Controls (<code class="text-blue-600 text-xs font-mono">&lt;x-input&gt;</code>, <code class="text-blue-600 text-xs font-mono">&lt;x-select&gt;</code>, <code class="text-blue-600 text-xs font-mono">&lt;x-search&gt;</code>)</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-5">
                <x-input 
                    label="Input Standar" 
                    name="demo_text" 
                    placeholder="Ketik teks di sini..." 
                />
                <x-input 
                    label="Input Wajib Diisi (Required)" 
                    name="demo_required" 
                    placeholder="Contoh: Nama Pelanggan" 
                    :required="true" 
                />
                <x-input 
                    label="Input dengan Error State" 
                    name="demo_error" 
                    value="Data salah" 
                    error="Kolom ini memiliki format yang tidak valid." 
                />
                <x-input 
                    label="Input Readonly" 
                    name="demo_readonly" 
                    value="KODE-OTOMATIS-001" 
                    :readonly="true" 
                />
                <x-select 
                    label="Select Dropdown" 
                    name="demo_select" 
                    :options="[
                        'oli' => 'Oli & Pelumas',
                        'rem' => 'Sistem Pengereman',
                        'kelistrikan' => 'Kelistrikan & Busi',
                        'ban' => 'Ban & Roda',
                    ]" 
                    selected="oli" 
                />
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Live Table Search Input</label>
                    <x-search 
                        target="#playground-table" 
                        placeholder="Ketik untuk memfilter tabel..." 
                    />
                </div>
            </div>
        </div>

        {{-- 6. Table Component with Real-time Search --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Data Table Component (<code class="text-blue-600 text-xs font-mono">&lt;x-table&gt;</code>)</h3>
            </div>

            <x-table 
                id="playground-table" 
                :searchable="true" 
                searchPlaceholder="Cari data di tabel uji coba..." 
                :headers="['KODE', 'NAMA PRODUK', 'KATEGORI', 'HARGA', 'STOK', 'STATUS', 'AKSI']"
            >
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BRG-001</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">Oli Mesin MPX2 0.8L (Matic)</td>
                    <td class="py-3.5 px-4 text-gray-600">Oli & Pelumas</td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">Rp 52.000</td>
                    <td class="py-3.5 px-4 font-bold text-amber-500">9</td>
                    <td class="py-3.5 px-4">
                        <x-badge type="warning" text="Stok Menipis" />
                    </td>
                    <td class="py-3.5 px-4 last:pr-6">
                        <div class="flex items-center gap-1.5">
                            <x-button-action type="edit" onclick="alert('Edit BRG-001')" />
                            <x-button-action type="delete" onclick="alert('Hapus BRG-001')" />
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BRG-002</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">Kampas Rem Depan Honda Vario / Beat</td>
                    <td class="py-3.5 px-4 text-gray-600">Sistem Pengereman</td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">Rp 45.000</td>
                    <td class="py-3.5 px-4 font-bold text-gray-800">100</td>
                    <td class="py-3.5 px-4">
                        <x-badge type="success" text="Tersedia" />
                    </td>
                    <td class="py-3.5 px-4 last:pr-6">
                        <div class="flex items-center gap-1.5">
                            <x-button-action type="edit" onclick="alert('Edit BRG-002')" />
                            <x-button-action type="delete" onclick="alert('Hapus BRG-002')" />
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BRG-003</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">Busi Standar NGK CPR9EA-9</td>
                    <td class="py-3.5 px-4 text-gray-600">Kelistrikan & Busi</td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">Rp 25.000</td>
                    <td class="py-3.5 px-4 font-bold text-gray-800">50</td>
                    <td class="py-3.5 px-4">
                        <x-badge type="success" text="Tersedia" />
                    </td>
                    <td class="py-3.5 px-4 last:pr-6">
                        <div class="flex items-center gap-1.5">
                            <x-button-action type="edit" onclick="alert('Edit BRG-003')" />
                            <x-button-action type="delete" onclick="alert('Hapus BRG-003')" />
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">BRG-005</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-900">Filter Udara Honda Beat FI / ESP</td>
                    <td class="py-3.5 px-4 text-gray-600">Filter Udara & Bensin</td>
                    <td class="py-3.5 px-4 font-bold text-gray-900">Rp 48.000</td>
                    <td class="py-3.5 px-4 font-bold text-rose-500">0</td>
                    <td class="py-3.5 px-4">
                        <x-badge type="danger" text="Habis" />
                    </td>
                    <td class="py-3.5 px-4 last:pr-6">
                        <div class="flex items-center gap-1.5">
                            <x-button-action type="edit" onclick="alert('Edit BRG-005')" />
                            <x-button-action type="delete" onclick="alert('Hapus BRG-005')" />
                        </div>
                    </td>
                </tr>

                @php
                    $extraSamples = [
                        ['BRG-006', 'Aki Kering GS Astra GTZ5S', 'Kelistrikan', 'Rp 230.000', 12, 'success', 'Tersedia'],
                        ['BRG-007', 'Ban Luar FDR 90/90-14 Tubeless', 'Ban & Roda', 'Rp 210.000', 15, 'success', 'Tersedia'],
                        ['BRG-008', 'V-Belt Kit Honda Beat FI', 'Transmisi CVT', 'Rp 105.000', 18, 'success', 'Tersedia'],
                        ['BRG-009', 'Roller CVT TDR 10gr (Set)', 'Transmisi CVT', 'Rp 85.000', 16, 'success', 'Tersedia'],
                        ['BRG-010', 'Kampas Tromol Belakang Honda', 'Sistem Rem', 'Rp 40.000', 28, 'success', 'Tersedia'],
                        ['BRG-011', 'Oli Yamalube Silver 0.8L', 'Oli & Pelumas', 'Rp 48.000', 30, 'success', 'Tersedia'],
                        ['BRG-012', 'Oli Gardan / Gear Matik 120ml', 'Oli & Pelumas', 'Rp 18.000', 45, 'success', 'Tersedia'],
                        ['BRG-013', 'Lampu Depan LED H6 Osram', 'Kelistrikan', 'Rp 65.000', 8, 'warning', 'Stok Menipis'],
                        ['BRG-014', 'Rantai Roda 428H-110L Gold', 'Penggerak', 'Rp 145.000', 5, 'warning', 'Stok Menipis'],
                        ['BRG-015', 'Minyak Rem DOT 4 Jumbo 50ml', 'Sistem Rem', 'Rp 12.000', 40, 'success', 'Tersedia'],
                        ['BRG-016', 'Busi Iridium Daytona CR7HIX', 'Kelistrikan & Busi', 'Rp 55.000', 14, 'success', 'Tersedia'],
                        ['BRG-017', 'Bearing Roda Depan 6201 2RS', 'Ban & Roda', 'Rp 25.000', 25, 'success', 'Tersedia'],
                    ];
                @endphp

                @foreach($extraSamples as $sample)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3.5 px-4 first:pl-6 font-bold text-gray-900">{{ $sample[0] }}</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $sample[1] }}</td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $sample[2] }}</td>
                        <td class="py-3.5 px-4 font-bold text-gray-900">{{ $sample[3] }}</td>
                        <td class="py-3.5 px-4 font-bold {{ $sample[4] <= 10 ? ($sample[4] == 0 ? 'text-rose-500' : 'text-amber-500') : 'text-gray-800' }}">{{ $sample[4] }}</td>
                        <td class="py-3.5 px-4">
                            <x-badge :type="$sample[5]" :text="$sample[6]" />
                        </td>
                        <td class="py-3.5 px-4 last:pr-6">
                            <div class="flex items-center gap-1.5">
                                <x-button-action type="edit" onclick="alert('Edit {{ $sample[0] }}')" />
                                <x-button-action type="delete" onclick="alert('Hapus {{ $sample[0] }}')" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>

        {{-- 7. Icon Library Gallery --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Icon Library Gallery (<code class="text-blue-600 text-xs font-mono">&lt;x-icon&gt;</code>)</h3>
            </div>

            @php
                $allIcons = [
                    'dashboard', 'master-data', 'users', 'barang', 'kategori', 
                    'supplier', 'layanan', 'gudang', 'barang-masuk', 'stok-opname', 
                    'transaksi', 'riwayat', 'laporan', 'profil', 'stok', 
                    'sidebar', 'menu', 'plus', 'edit', 'delete', 
                    'view', 'search', 'close', 'logout', 'chevron-down', 
                    'chevron-right', 'dollar', 'refresh', 'bag', 'chart', 
                    'alert', 'clipboard', 'check'
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($allIcons as $ico)
                    <div class="p-3 bg-gray-50/70 border border-gray-100 rounded-xl flex flex-col items-center justify-center text-center gap-2 hover:bg-blue-50/40 hover:border-blue-200 transition-all group">
                        <div class="text-gray-600 group-hover:text-blue-600 transition-colors">
                            <x-icon :name="$ico" :size="24" />
                        </div>
                        <span class="text-[11px] font-mono text-gray-500 group-hover:text-gray-900 select-all truncate w-full">
                            {{ $ico }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 8. Modal Dialog Component Demo --}}
    <x-modal 
        id="playground-demo-modal" 
        title="Uji Coba Modal Component" 
        subtitle="Ini adalah contoh implementasi modal dialog yang interaktif" 
        size="md"
        submitText="Konfirmasi Aksi"
    >
        <p class="text-xs text-gray-600 leading-relaxed mb-4">
            Komponen modal ini dilengkapi backdrop blur, tombol tutup (close), header dengan judul & subjudul, serta footer aksi yang konsisten.
        </p>

        <div class="space-y-3">
            <x-input label="Nama Input Uji Coba" name="modal_nama" placeholder="Masukkan teks..." />
            <x-select 
                label="Pilihan Dropdown" 
                name="modal_select" 
                :options="['1' => 'Opsi Pertama', '2' => 'Opsi Kedua', '3' => 'Opsi Ketiga']" 
            />
        </div>
    </x-modal>
@endsection
