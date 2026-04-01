@props([
    'variant'       => 'primary',  // primary | secondary | danger | ghost
    'size'          => 'md',       // sm | md | lg
    'type'          => 'button',
    'href'          => null,
    'loading'       => false,
    'loadingTarget' => null,
    'loadingText'   => 'Mohon Tunggu...',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-2xl border transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-95 duration-300 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary'   => 'bg-green-900 text-white border-transparent hover:bg-green-950 focus:ring-green-900 shadow-sm',
        'secondary' => 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 focus:ring-slate-200',
        'danger'    => 'bg-red-600 text-white border-transparent hover:bg-red-700 focus:ring-red-500',
        'ghost'     => 'bg-transparent text-slate-500 border-transparent hover:bg-slate-50 hover:text-slate-900',
    ];

    $sizes = [
        'sm' => 'text-[11px] px-3 py-1.5',
        'md' => 'text-[12px] px-4 py-2.5',
        'lg' => 'text-[13px] px-6 py-3',
        'xl' => 'text-[14px] px-8 py-4',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" 
        @if($loading) 
            wire:loading.attr="disabled" 
            @if($loadingTarget) wire:target="{{ $loadingTarget }}" @endif
        @endif
        {{ $attributes->merge(['class' => $classes]) }}>
        
        @if($loading)
            <span class="flex items-center gap-2" wire:loading.remove @if($loadingTarget) wire:target="{{ $loadingTarget }}" @endif>
                {{ $slot }}
            </span>
            <span class="items-center gap-2" wire:loading.flex @if($loadingTarget) wire:target="{{ $loadingTarget }}" @endif style="display: none;">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ $loadingText }}</span>
            </span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
