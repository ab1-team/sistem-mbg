<div class="overflow-x-auto relative">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        <thead class="bg-slate-50/80 border-b border-slate-100 backdrop-blur-sm">
            <tr>
                {{ $thead }}
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            {{ $slot }}
        </tbody>
    </table>

    {{-- Footer Slot (e.g. for Add Row buttons) --}}
    @if(isset($footer))
        <div class="border-t border-slate-50 bg-slate-50/20">
            {{ $footer }}
        </div>
    @endif

    {{-- Loading state - subtle blur and center indicator --}}
    <div wire:loading.delay.longest 
        @if($attributes->has('loading-target')) 
            wire:target="{{ $attributes->get('loading-target') }}" 
        @endif
        class="absolute inset-0 bg-white/20 backdrop-blur-[1px] flex items-center justify-center z-10 transition-all duration-500">
        <div class="flex items-center gap-3 px-4 py-2 bg-white/90 backdrop-blur shadow-2xl rounded-2xl border border-slate-100 animate-in fade-in zoom-in duration-300">
             <div class="w-4 h-4 border-2 border-green-900/10 border-t-green-900 rounded-full animate-spin"></div>
             <span class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Sinkronisasi...</span>
        </div>
    </div>
</div>
