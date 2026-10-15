@props([
    'label'       => null,
    'name'        => '',
    'type'        => 'text',
    'value'       => null,
    'placeholder' => '',
    'required'    => false,
    'readonly'    => false,
    'error'       => null,
    'id'          => null,
])

@php
    $inputId = $id ?? ($name ?: 'input-' . uniqid());
@endphp

<div class="w-full">
    @if(isset($label) && $label)
        <label for="{{ $inputId }}" class="block text-xs font-semibold text-gray-700 mb-1.5">
            {{ $label }}
            @if(isset($required) && $required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input type="{{ $type ?? 'text' }}"
               name="{{ $name ?? '' }}"
               id="{{ $inputId }}"
               value="{{ old($name ?? '', $value ?? '') }}"
               placeholder="{{ $placeholder ?? '' }}"
               @if(isset($required) && $required) required @endif
               @if(isset($readonly) && $readonly) readonly @endif
               class="w-full px-3.5 py-2 bg-white border {{ isset($error) && $error ? 'border-rose-400 focus:ring-rose-400/20 focus:border-rose-500' : 'border-gray-200 focus:ring-blue-500/20 focus:border-blue-500' }} rounded-xl text-xs text-gray-800 placeholder-gray-400 shadow-2xs focus:outline-none focus:ring-2 transition-all disabled:bg-gray-50 disabled:cursor-not-allowed">
    </div>

    @if(isset($error) && $error)
        <p class="text-[11px] text-rose-500 mt-1">{{ $error }}</p>
    @endif
</div>
