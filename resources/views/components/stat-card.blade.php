@props([
    'label' => '',
    'value' => '0',
    'icon'  => 'dollar',
    'color' => 'blue',
    'sub'   => null,
])

@php
    $label = $label ?? '';
    $value = $value ?? '0';
    $icon  = $icon ?? 'dollar';
    $color = $color ?? 'blue';

    $colorMap = [
        'emerald' => 'bg-emerald-500 text-white',
        'green'   => 'bg-emerald-500 text-white',
        'blue'    => 'bg-blue-500 text-white',
        'purple'  => 'bg-purple-600 text-white',
        'amber'   => 'bg-amber-500 text-white',
        'orange'  => 'bg-amber-500 text-white',
        'rose'    => 'bg-rose-500 text-white',
        'red'     => 'bg-rose-500 text-white',
    ];
    $iconClass = $colorMap[$color] ?? 'bg-blue-500 text-white';
@endphp

<div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex items-center gap-4 hover:shadow-sm transition-all duration-200">
    <!-- Square Colored Icon -->
    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-xs {{ $iconClass }}">
        @include('components.icon', ['name' => $icon, 'size' => 22])
    </div>

    <!-- Text & Value -->
    <div class="min-w-0 flex-1">
        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 truncate">
            {{ $label }}
        </p>
        <h3 class="text-xl font-bold text-gray-900 mt-1 tracking-tight truncate">
            {{ $value }}
        </h3>
        @if(isset($sub) && $sub)
            <p class="text-[11px] text-gray-400 mt-0.5 truncate">
                {{ $sub }}
            </p>
        @endif
    </div>
</div>
