@props([
    'variant' => 'success',  // success | warning | danger | info | gray
])

@php
    $variants = [
        'success' => 'bg-green-50 text-green-700 border border-green-100',
        'warning' => 'bg-amber-50 text-amber-700 border border-amber-100',
        'danger'  => 'bg-red-50 text-red-600 border border-red-100',
        'info'    => 'bg-blue-50 text-blue-700 border border-blue-100',
        'gray'    => 'bg-slate-100 text-slate-500 border border-slate-200',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wide ' . ($variants[$variant] ?? $variants['gray'])]) }}>
    {{ $slot }}
</span>
