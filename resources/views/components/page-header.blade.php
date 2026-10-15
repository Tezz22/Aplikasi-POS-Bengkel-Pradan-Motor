@props([
    'title'    => '',
    'subtitle' => null,
    'icon'     => 'dashboard',
    'actions'  => null,
])

@php
    $title = $title ?? '';
    $icon = $icon ?? 'dashboard';
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0 shadow-xs">
            @include('components.icon', ['name' => $icon, 'size' => 24])
        </div>
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                {{ $title }}
            </h1>
            @if(isset($subtitle) && $subtitle)
                <p class="text-xs text-gray-500 mt-1">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>

    @if(isset($actions) && $actions)
        <div class="flex items-center gap-3 shrink-0">
            {!! $actions !!}
        </div>
    @endif
</div>
