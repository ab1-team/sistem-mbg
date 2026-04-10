<div>
    <div x-data="{ open: @entangle('isOpen') }" x-show="open"
        class="fixed inset-0 z-1000 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-cloak>
        <div
            class="bg-white rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
            
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-[18px] font-black text-slate-900 leading-none">Tambah Barang Manual</h3>
                    <p class="text-[12px] text-slate-500 font-medium mt-1.5">Tambahkan item baru ke dalam daftar pesanan.</p>
                </div>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-8">
                <div class="space-y-6">
                    {{-- Search Material --}}
                    <x-form-searchable-select 
                        label="Cari Bahan Baku" 
                        name="selectedMaterialId" 
                        wire:model.live="selectedMaterialId"
                        :options="$materialOptions"
                        placeholder="Ketik nama bahan baku..."
                        required
                    />

                    @if($selectedMaterialId)
                    <div class="grid grid-cols-2 gap-4 animate-in fade-in slide-in-from-top-2 duration-300">
                        <x-form-input label="Kuantitas Pesanan" name="quantity" type="number" step="0.01" wire:model="quantity">
                            <x-slot name="icon">
                                <span class="text-[10px] font-black uppercase">{{ $unit }}</span>
                            </x-slot>
                        </x-form-input>

                        <x-form-input label="Estimasi Harga Satuan" name="unit_price" type="number" wire:model="unit_price">
                            <x-slot name="icon">
                                <span class="text-[10px] font-bold">Rp</span>
                            </x-slot>
                        </x-form-input>
                    </div>
                    @endif

                    <div class="pt-4 flex gap-3">
                        <x-btn @click="open = false" type="button" variant="secondary" class="flex-1">Batal</x-btn>
                        <x-btn wire:click="addItem" type="button" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold"
                            wire:loading.attr="disabled" :disabled="!$selectedMaterialId">
                            <span wire:loading.remove>Tambahkan ke PO</span>
                            <span wire:loading>Menyimpan...</span>
                        </x-btn>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
