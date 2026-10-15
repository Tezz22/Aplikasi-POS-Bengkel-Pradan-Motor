@props([
    'label' => '',
    'value' => '0',
    'unit'  => null,
    'sub'   => null,
    'icon'  => 'box',
    'type'  => 'info',
])

@php
    $label = $label ?? '';
    $value = $value ?? '0';
    $icon  = $icon ?? 'box';
    $type  = $type ?? 'info';

    $typeConfig = [
        'danger'  => ['label' => 'text-rose-500', 'val' => 'text-rose-600', 'bg' => 'bg-rose-50 text-rose-500 border border-rose-100', 'card' => 'border-rose-100/60'],
        'warning' => ['label' => 'text-amber-500', 'val' => 'text-amber-600', 'bg' => 'bg-amber-50 text-amber-500 border border-amber-100', 'card' => 'border-amber-100/60'],
        'success' => ['label' => 'text-emerald-500', 'val' => 'text-gray-900', 'bg' => 'bg-emerald-50 text-emerald-600 border border-emerald-100', 'card' => 'border-gray-100'],
        'info'    => ['label' => 'text-blue-500', 'val' => 'text-gray-900', 'bg' => 'bg-blue-50 text-blue-600 border border-blue-100', 'card' => 'border-gray-100'],
    ];
    $cfg = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div class="bg-white rounded-2xl p-5 border {{ $cfg['card'] }} shadow-xs flex items-center justify-between hover:shadow-sm transition-all duration-200">
    <div>
        <div class="flex items-center gap-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $cfg['label'] }}">
                {{ $label }}
            </span>
        </div>
        <div class="flex items-baseline gap-2 mt-2">
            <span class="text-3xl font-extrabold {{ $cfg['val'] }} tracking-tight leading-none">
                {{ $value }}
            </span>
            @if(isset($unit) && $unit)
                <span class="text-xs text-gray-400 font-normal">
                    {{ $unit }}
                </span>
            @endif
        </div>
        @if(isset($sub) && $sub)
            <p class="text-[11px] text-gray-400 mt-1">
                {{ $sub }}
            </p>
        @endif
    </div>

    <!-- Soft Icon Box -->
    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 {{ $cfg['bg'] }}">
        @include('components.icon', ['name' => $icon, 'size' => 20])
    </div>
</div>
