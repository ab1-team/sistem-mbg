@props([
    'variant' => 'success', // success | danger | warning | info
    'title'   => null,
])

@php
    $type = $variant === 'danger' ? 'error' : $variant;
    $styles = [
        'success' => 'bg-green-50 border-green-100 text-green-700',
        'error'   => 'bg-red-50 border-red-100 text-red-700',
        'warning' => 'bg-amber-50 border-amber-100 text-amber-700',
        'info'    => 'bg-blue-50 border-blue-100 text-blue-700',
    ];
    $icons = [
        'success' => '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'error'   => '<path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'warning' => '<path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        'info'    => '<path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ];
@endphp

<div class="flex items-start gap-3 px-4 py-3 rounded-[20px] border {{ $styles[$type] ?? $styles['success'] }}">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        {!! $icons[$type] ?? $icons['success'] !!}
    </svg>
    <div>
        @if($title)
            <h5 class="font-black text-[13px] leading-tight mb-1">{{ $title }}</h5>
        @endif
        <div class="text-[12px] font-medium opacity-90 leading-relaxed">
            {{ $slot }}
        </div>
    </div>
</div>
