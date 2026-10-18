@props([
    'id'         => 'modal-dialog',
    'title'      => '',
    'subtitle'   => null,
    'size'       => 'md',
    'formAction' => null,
    'formMethod' => 'POST',
    'submitText' => 'Simpan',
    'footer'     => null,
])

@php
    $id = $id ?? 'modal-dialog';
    $title = $title ?? '';
    $size = $size ?? 'md';

    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-2xl',
    ];
    $sz = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div id="{{ $id }}" 
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition-opacity duration-200"
     aria-modal="true" 
     role="dialog">
    
    <!-- Modal Dialog Card -->
    <div class="relative w-full {{ $sz }} bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform transition-all">
        @if(isset($formAction) && $formAction)
            <form action="{{ $formAction }}" method="{{ strtoupper($formMethod ?? 'POST') === 'GET' ? 'GET' : 'POST' }}">
                @if(strtoupper($formMethod ?? 'POST') !== 'GET')
                    @csrf
                @endif
                @if(!in_array(strtoupper($formMethod ?? 'POST'), ['GET', 'POST']))
                    @method($formMethod)
                @endif
        @endif

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-base font-bold text-gray-900 leading-snug">
                    {{ $title }}
                </h3>
                @if(isset($subtitle) && $subtitle)
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            <button type="button" 
                    onclick="closeModal('{{ $id }}')" 
                    class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
                <x-icon name="close" :size="16" />
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto text-xs text-gray-600">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="px-6 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-end gap-2.5">
            @if(isset($footer) && $footer)
                {!! $footer !!}
            @else
                <x-button variant="secondary" size="sm" :onclick="'closeModal(\'' . $id . '\')'">
                    Batal
                </x-button>
                <x-button variant="primary" size="sm" :type="isset($formAction) && $formAction ? 'submit' : 'button'">
                    {{ $submitText ?? 'Simpan' }}
                </x-button>
            @endif
        </div>

        @if(isset($formAction) && $formAction)
            </form>
        @endif
    </div>
</div>
