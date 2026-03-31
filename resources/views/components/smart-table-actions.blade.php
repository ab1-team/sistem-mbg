<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between mb-6 gap-4 px-1">
    <div class="flex-1 w-full lg:max-w-sm relative group">
        <x-form-input wire:model.live.debounce.300ms="search" placeholder="Cari data..." class="shadow-sm">
            <x-slot name="icon">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
            </x-slot>
        </x-form-input>

        <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
            <div class="w-3.5 h-3.5 border-2 border-slate-200 border-t-green-900 rounded-full animate-spin"></div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2.5 group">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-none">Tampilkan:</span>
            <x-form-select wire:model.live="perPage"
                class="w-auto! py-1.5! pr-10! text-[12px]! font-bold! rounded-xl! bg-slate-50! focus:bg-white! shadow-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </x-form-select>
        </div>
        {{ $slot }}
    </div>
</div>
