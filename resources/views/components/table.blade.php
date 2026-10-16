@props([
    'id'                => 'data-table',
    'headers'           => null,
    'searchable'        => false,
    'searchPlaceholder' => null,
    'actions'           => null,
    'footer'            => null,
])

@php
    $tableId = $id ?? 'data-table';
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
    {{-- Optional Toolbar (Search + Actions) --}}
    @if((isset($searchable) && $searchable) || (isset($actions) && $actions))
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            @if(isset($searchable) && $searchable)
                <div class="w-full sm:w-72">
                    <x-search :target="'#' . $tableId" :placeholder="$searchPlaceholder ?? 'Cari di tabel...'" />
                </div>
            @else
                <div></div>
            @endif

            @if(isset($actions) && $actions)
                <div class="flex items-center gap-2.5 shrink-0">
                    {!! $actions !!}
                </div>
            @endif
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

    {{-- Optional Footer / Pagination slot --}}
    @if(isset($footer) && $footer)
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 text-xs text-gray-500">
            {!! $footer !!}
        </div>
    @endif
</div>
