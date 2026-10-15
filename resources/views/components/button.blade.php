@props([
    'variant'  => 'primary',
    'size'     => 'md',
    'icon'     => null,
    'iconSize' => null,
    'href'     => null,
    'type'     => 'button',
    'onclick'  => null,
    'class'    => '',
    'text'     => null,
])

@php
    $variant = $variant ?? 'primary';
    $size = $size ?? 'md';
    $calcIconSize = $iconSize ?? ($size === 'sm' ? 14 : 16);

    $variantClasses = [
        'primary'         => 'bg-blue-600 hover:bg-blue-700 text-white shadow-xs focus:ring-blue-500/20',
        'secondary'       => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 shadow-2xs focus:ring-gray-200',
        'danger'          => 'bg-rose-600 hover:bg-rose-700 text-white shadow-xs focus:ring-rose-500/20',
        'success'         => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs focus:ring-emerald-500/20',
        'outline-primary' => 'border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500/20',
        'outline-danger'  => 'border border-rose-300 text-rose-600 hover:bg-rose-50 focus:ring-rose-500/20',
        'outline-success' => 'border border-emerald-300 text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-500/20',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs rounded-lg gap-1.5',
        'md' => 'px-4 py-2 text-xs font-medium rounded-xl gap-2',
        'lg' => 'px-5 py-2.5 text-sm font-semibold rounded-xl gap-2.5',
    ];

    $base = 'inline-flex items-center justify-center font-medium transition-all duration-150 focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed select-none cursor-pointer';
    $varStyle = $variantClasses[$variant] ?? $variantClasses['primary'];
    $szStyle  = $sizeClasses[$size] ?? $sizeClasses['md'];
    $finalClass = "{$base} {$varStyle} {$szStyle} " . ($class ?? '');
@endphp

@if(isset($href) && $href)
    <a href="{{ $href }}" class="{{ $finalClass }}" @if(isset($onclick) && $onclick) onclick="{{ $onclick }}" @endif>
        @if(isset($icon) && $icon)
            @include('components.icon', ['name' => $icon, 'size' => $calcIconSize])
        @endif
        <span>{{ $slot ?? ($text ?? 'Tombol') }}</span>
    </a>
@else
    <button type="{{ $type ?? 'button' }}" 
            class="{{ $finalClass }}" 
            @if(isset($onclick) && $onclick) onclick="{{ $onclick }}" @endif>
        @if(isset($icon) && $icon)
            @include('components.icon', ['name' => $icon, 'size' => $calcIconSize])
        @endif
        <span>{{ $slot ?? ($text ?? 'Tombol') }}</span>
    </button>
@endif
