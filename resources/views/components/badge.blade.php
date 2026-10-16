@props([
    'type' => 'neutral',
    'size' => 'sm',
    'text' => '',
])

@php
    $type = $type ?? 'neutral';
    $size = $size ?? 'sm';

    $badgeStyles = [
        'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
        'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        'danger'  => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
        'info'    => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
        'neutral' => 'bg-gray-100 text-gray-700 ring-1 ring-gray-600/10',
    ];
    $style = $badgeStyles[$type] ?? $badgeStyles['neutral'];
    $sz = $size === 'md' ? 'px-3 py-1 text-xs' : 'px-2.5 py-0.5 text-[11px]';
@endphp

<span class="inline-flex items-center font-semibold rounded-full {{ $style }} {{ $sz }}">
    {{ (isset($slot) && $slot->isNotEmpty()) ? $slot : ($text ?? '') }}
</span>
