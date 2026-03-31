@props([
    'title'    => '',
    'subtitle' => '',
    'back'     => null,    // URL back button
    'backLabel'=> 'Kembali',
])

<div class="flex items-start justify-between mb-6">
    <div>
        @if($back)
            <a href="{{ $back }}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 hover:text-slate-700 mb-3 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ $backLabel }}
            </a>
        @endif
        <h1 class="text-[24px] font-extrabold text-slate-900 tracking-tight leading-none">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-[13px] text-slate-400 mt-1.5">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-2.5 mt-1">
            {{ $actions }}
        </div>
    @endisset
</div>
