<div>
    <x-dialog name="po-add-item" title="Tambah Barang Manual" :show="$isOpen">
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
                <x-btn @click="$wire.set('isOpen', false)" type="button" variant="secondary" class="flex-1">Batal</x-btn>
                <x-btn wire:click="addItem" type="button" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold"
                    wire:loading.attr="disabled" :disabled="!$selectedMaterialId">
                    <span wire:loading.remove>Tambahkan ke PO</span>
                    <span wire:loading>Menyimpan...</span>
                </x-btn>
            </div>
        </div>
    </x-dialog>
</div>
