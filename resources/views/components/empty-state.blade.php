@props([
    'title'    => 'Belum ada data',
    'subtitle' => 'Tambahkan data pertama Anda untuk memulai.',
    'icon'     => 'inbox',         // inbox | users | building | document
    'action'   => null,
    'href'     => null,
    'label'    => 'Tambah Baru',
])

@php
    $icons = [
        'inbox'    => '<path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
        'users'    => '<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'building' => '<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'document' => '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    ];
@endphp

<div class="flex flex-col items-center justify-center py-20 text-center">
    <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mb-5">
        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            {!! $icons[$icon] ?? $icons['inbox'] !!}
        </svg>
    </div>
    <p class="text-[14px] font-semibold text-slate-700">{{ $title }}</p>
    <p class="text-[12px] text-slate-400 mt-1 max-w-xs">{{ $subtitle }}</p>
    @if($href)
        <a href="{{ $href }}" class="mt-5 inline-flex items-center gap-1.5 bg-green-900 text-white text-[12px] font-semibold px-4 py-2 rounded-xl hover:bg-green-800 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ $label }}
        </a>
    @endif
</div>
