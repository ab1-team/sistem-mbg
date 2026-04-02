@props([
    'title' => 'Konfirmasi',
    'name' => 'modal', // Used for x-data binding
    'danger' => false,
    'maxWidth' => 'md', // sm | md | lg | xl
    'show' => false,
])

@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
    ];
@endphp

{{-- Trigger slot: wrap any button with x-on:click="$dispatch('open-modal', '{{ $name }}')" --}}

<div x-data="{ open: @js($show) }" x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false">
    {{-- Trigger --}}
    {{ $trigger ?? '' }}

    {{-- Overlay --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak>
        {{-- Dialog --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="if(!$event.target.closest('.searchable-select-menu')) open = false"
            class="bg-white rounded-2xl shadow-2xl w-full {{ $widths[$maxWidth] ?? $widths['md'] }}">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <h3 class="text-[15px] font-bold text-slate-900">{{ $title }}</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div
                    class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-slate-50 bg-slate-50/50 rounded-b-2xl">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
