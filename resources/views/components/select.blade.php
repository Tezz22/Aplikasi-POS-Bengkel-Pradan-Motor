@props([
    'label'       => null,
    'name'        => '',
    'options'     => null,
    'selected'    => null,
    'value'       => null,
    'placeholder' => '-- Pilih --',
    'required'    => false,
    'error'       => null,
    'id'          => null,
])

@php
    $selectId = $id ?? ($name ?: 'select-' . uniqid());
    $activeVal = $value ?? $selected ?? null;
    $isList = is_array($options) && array_is_list($options);
@endphp

<div class="w-full">
    @if(isset($label) && $label)
        <label for="{{ $selectId }}" class="block text-xs font-semibold text-gray-700 mb-1.5">
            {{ $label }}
            @if(isset($required) && $required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select name="{{ $name ?? '' }}"
                id="{{ $selectId }}"
                @if(isset($required) && $required) required @endif
                class="w-full px-3.5 py-2 bg-white border {{ isset($error) && $error ? 'border-rose-400 focus:ring-rose-400/20 focus:border-rose-500' : 'border-gray-200 focus:ring-blue-500/20 focus:border-blue-500' }} rounded-xl text-xs text-gray-800 shadow-2xs focus:outline-none focus:ring-2 transition-all cursor-pointer">
            @if(isset($placeholder) && $placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @if(isset($options) && is_array($options))
                @foreach($options as $val => $optLabel)
                    @php($currVal = $isList ? $optLabel : $val)
                    <option value="{{ $currVal }}" {{ (string)old($name ?? '', $activeVal ?? '') === (string)$currVal ? 'selected' : '' }}>
                        {{ $optLabel }}
                    </option>
                @endforeach
            @else
                {{ $slot ?? '' }}
            @endif
        </select>
    </div>

    @if(isset($error) && $error)
        <p class="text-[11px] text-rose-500 mt-1">{{ $error }}</p>
    @endif
</div>
