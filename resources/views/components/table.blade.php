@props([
    'id'                => null,
    'headers'           => null,
    'searchable'        => false,
    'searchPlaceholder' => null,
    'actions'           => null,
    'footer'            => null,
    'paginate'          => true,
    'perPage'           => 10,
    'perPageOptions'    => [10, 25, 50, 100],
])

@php
    static $tableCounter = 0;
    $tableCounter++;
    $tableId = $id ?? ('data-table-' . $tableCounter);
    $perPageOptions = is_array($perPageOptions) ? $perPageOptions : [10, 25, 50, 100];
    if (!in_array($perPage, $perPageOptions)) {
        $perPage = $perPageOptions[0] ?? 10;
    }
@endphp

<div id="{{ $tableId }}-container" 
     class="data-table-container bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden"
     data-table-id="{{ $tableId }}"
     data-paginate="{{ $paginate ? 'true' : 'false' }}"
     data-per-page="{{ $perPage }}">
    {{-- Optional Toolbar (Search on Left, Actions & Tab Penampilan Data on Right) --}}
    @if((isset($searchable) && $searchable) || (isset($actions) && $actions) || $paginate)
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            {{-- Pojok Kiri: Pencarian --}}
            @if(isset($searchable) && $searchable)
                <div class="w-full sm:w-72">
                    <x-search :target="'#' . $tableId" :placeholder="$searchPlaceholder ?? 'Cari di tabel...'" />
                </div>
            @else
                <div></div>
            @endif

            {{-- Pojok Kanan: Actions & Dropdown Penampilan Data --}}
            <div class="flex flex-wrap items-center gap-3 shrink-0 sm:ml-auto">
                @if(isset($actions) && $actions)
                    <div class="flex items-center gap-2.5">
                        {!! $actions !!}
                    </div>
                @endif

                @if($paginate)
                    <div class="flex items-center gap-2 shrink-0">
                        <label for="{{ $tableId }}-per-page" class="text-xs text-gray-500 font-medium whitespace-nowrap">Tampilkan:</label>
                        <div class="relative">
                            <select id="{{ $tableId }}-per-page" 
                                    data-per-page-for="{{ $tableId }}" 
                                    aria-label="Jumlah baris per halaman"
                                    class="data-table-per-page appearance-none bg-white border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl pl-3 pr-8 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all cursor-pointer shadow-2xs">
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" {{ $option == $perPage ? 'selected' : '' }}>
                                        {{ $option }} 
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Responsive Table --}}
    <div class="overflow-x-auto">
        <table id="{{ $tableId }}" class="w-full text-left border-collapse text-xs">
            @if(isset($headers) && is_array($headers))
                <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-400 font-semibold uppercase text-[10px] tracking-wider select-none">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="py-3 px-4 first:pl-6 last:pr-6 {{ is_array($header) ? ($header['class'] ?? '') : '' }}">
                                {{ is_array($header) ? ($header['text'] ?? '') : $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="divide-y divide-gray-50 text-gray-700">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    {{-- Pagination & Data Display Footer --}}
    @if($paginate)
        <div id="{{ $tableId }}-pagination-footer" class="p-3.5 sm:px-5 sm:py-3 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs select-none">
            {{-- Left: Row counters / Info --}}
            <div class="data-table-info text-xs text-gray-500 font-medium" data-info-for="{{ $tableId }}">
                Menampilkan <span class="font-bold text-gray-800" data-info="start">0</span> sampai <span class="font-bold text-gray-800" data-info="end">0</span> dari <span class="font-bold text-gray-800" data-info="total">0</span> data
            </div>

            {{-- Right: Pagination Navigation Controls --}}
            <div class="flex items-center gap-1 data-table-pagination overflow-x-auto" data-pagination-for="{{ $tableId }}">
                {{-- Injected dynamically by JavaScript --}}
            </div>
        </div>
    @endif

    {{-- Optional Custom Footer slot --}}
    @if(isset($footer) && $footer)
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 text-xs text-gray-500">
            {!! $footer !!}
        </div>
    @endif
</div>
