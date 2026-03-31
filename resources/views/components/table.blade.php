<div class="overflow-x-auto relative">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                {{ $thead }}
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            {{ $slot }}
        </tbody>
    </table>

    {{-- Loading state --}}
    <div wire:loading.delay class="absolute inset-0 bg-white/50 backdrop-blur-[1px] flex items-center justify-center z-10 transition-all duration-300">
        <div class="flex items-center gap-3 px-4 py-2 bg-white shadow-xl rounded-2xl border border-slate-100 animate-in fade-in zoom-in duration-300">
             <div class="w-5 h-5 border-2 border-green-900/20 border-t-green-900 rounded-full animate-spin"></div>
             <span class="text-[12px] font-bold text-slate-600 uppercase tracking-widest">Memproses...</span>
        </div>
    </div>
</div>
