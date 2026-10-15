@props([
    'placeholder' => 'Cari data...',
    'target'      => 'table',
    'id'          => '',
])

<div class="relative w-full max-w-sm">
    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
        @include('components.icon', ['name' => 'search', 'size' => 16])
    </div>
    <input type="text" 
           id="{{ $id }}"
           data-search="{{ $target }}" 
           placeholder="{{ $placeholder }}" 
           class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
</div>
