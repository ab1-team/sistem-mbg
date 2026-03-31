@props(['label' => '', 'value' => '', 'icon' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col space-y-1']) }}>
    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest leading-none">
        {{ $label }}
    </span>
    <div class="flex items-center gap-2">
        @if($icon)
            <div class="text-slate-400">
                {{ $icon }}
            </div>
        @endif
        <span class="text-[14px] font-semibold text-slate-800 tracking-tight leading-relaxed">
            {{ $value ?: ($slot->isEmpty() ? '-' : $slot) }}
        </span>
    </div>
</div>
