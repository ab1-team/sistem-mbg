@props([
    'variant' => 'primary',  // primary | secondary | danger | ghost
    'size'    => 'md',        // sm | md | lg
    'type'    => 'button',
    'href'    => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-2xl border transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-95 duration-300';

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
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
