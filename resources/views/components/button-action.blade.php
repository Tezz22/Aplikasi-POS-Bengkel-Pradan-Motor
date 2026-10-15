@props([
    'type'    => 'edit',
    'onclick' => null,
    'href'    => null,
    'title'   => null,
])

@php
    $type = $type ?? 'edit';

    $actionConfig = [
        'edit' => [
            'icon'  => 'edit',
            'title' => 'Edit Data',
            'class' => 'text-gray-500 hover:text-blue-600 hover:bg-blue-50',
        ],
        'delete' => [
            'icon'  => 'delete',
            'title' => 'Hapus Data',
            'class' => 'text-gray-500 hover:text-rose-600 hover:bg-rose-50',
        ],
        'info' => [
            'icon'  => 'view',
            'title' => 'Lihat Detail',
            'class' => 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50',
        ],
        'print' => [
            'icon'  => 'printer',
            'title' => 'Cetak',
            'class' => 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50',
        ],
    ];

    $cfg = $actionConfig[$type] ?? $actionConfig['edit'];
    $tooltip = $title ?? $cfg['title'];
    $baseClass = "w-8 h-8 rounded-lg inline-flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20 " . $cfg['class'];
@endphp

@if(isset($href) && $href)
    <a href="{{ $href }}" title="{{ $tooltip }}" class="{{ $baseClass }}" @if(isset($onclick) && $onclick) onclick="{{ $onclick }}" @endif>
        @include('components.icon', ['name' => $cfg['icon'], 'size' => 16])
    </a>
@else
    <button type="button" title="{{ $tooltip }}" class="{{ $baseClass }}" @if(isset($onclick) && $onclick) onclick="{{ $onclick }}" @endif>
        @include('components.icon', ['name' => $cfg['icon'], 'size' => 16])
    </button>
@endif
