<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800">Tambah Barang Manual</h3>
                    <button wire:click="$set('isOpen', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cari Bahan Baku</label>
                        <select wire:model.live="selectedMaterialId" class="w-full border rounded-xl px-4 py-2.5 bg-white text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="">-- Pilih Bahan Baku --</option>
                            @foreach($materialOptions as $opt)
                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($selectedMaterialId)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kuantitas ({{ $unit }})</label>
                                <input type="number" step="0.01" wire:model="quantity" class="w-full border rounded-xl px-4 py-2 bg-white text-sm outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Harga Satuan (Rp)</label>
                                <input type="number" wire:model="unit_price" class="w-full border rounded-xl px-4 py-2 bg-white text-sm outline-none">
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t flex gap-3">
                    <button type="button" wire:click="$set('isOpen', false)" class="flex-1 py-2 px-4 rounded-xl border font-bold text-slate-600 hover:bg-white">Batal</button>
                    <button type="button" wire:click="addItem" class="flex-1 py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold disabled:opacity-50" @disabled(!$selectedMaterialId)>
                        <span wire:loading.remove>Tambahkan ke PO</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
