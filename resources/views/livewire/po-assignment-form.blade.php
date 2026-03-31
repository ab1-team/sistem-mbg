<div class="space-y-6">
    {{-- ASSIGNMENT LIST --}}
    @if($item->assignments->count() > 0)
        <div class="space-y-3">
            <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Penugasan Supplier</h5>
            @foreach($item->assignments as $assignment)
                <div class="group flex items-center justify-between p-4 bg-white border border-slate-100 rounded-[24px] shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[13px] font-black text-slate-900 leading-none">{{ $assignment->supplier->name }}</p>
                            <p class="text-[11px] text-slate-400 mt-1 font-mono">Rp {{ number_format($assignment->unit_price_agreed, 0, ',', '.') }} / {{ $item->unit }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p class="text-[14px] font-black text-indigo-600 leading-none">{{ number_format($assignment->quantity_assigned, 2) }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $item->unit }}</p>
                        </div>
                        @if($item->purchaseOrder->status === 'dikirim_ke_yayasan' || $item->purchaseOrder->status === 'direview_yayasan')
                            <button wire:click="removeAssignment({{ $assignment->id }})" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ADD FORM --}}
    @if($this->remainingQuantity > 0)
        <div class="p-6 bg-slate-50 rounded-[32px] border border-slate-100 ring-4 ring-slate-100/50">
            <h5 class="text-[13px] font-black text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Input Penugasan Baru
            </h5>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-form-searchable-select 
                        label="Pilih Supplier" 
                        id="supplier_{{ $item->id }}" 
                        name="supplier_id" 
                        wire:model="supplier_id" 
                        :options="$suppliers->map(fn($s) => ['value' => (string)$s->id, 'label' => $s->name . ' (' . $s->category . ')'])"
                        placeholder="-- Pilih Supplier --"
                        required 
                    />
                </div>
                
                <div>
                    <x-form-input type="number" step="0.01" label="Kuantitas (Max: {{ $this->remainingQuantity }})" id="qty_{{ $item->id }}" name="quantity" wire:model="quantity" placeholder="0.00" required />
                </div>

                <div>
                    <x-form-input type="number" label="Harga Kesepakatan (Net)" id="price_{{ $item->id }}" name="unit_price" wire:model="unit_price" placeholder="Rp 0" required />
                </div>
            </div>


            <x-btn wire:click="addAssignment" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-900/10">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4"/>
                </svg>
                Tambahkan Penugasan
            </x-btn>
            @error('supplier_id') <p class="text-[11px] text-red-500 mt-2 font-bold px-4">{{ $message }}</p> @enderror
            @error('quantity') <p class="text-[11px] text-red-500 mt-2 font-bold px-4">{{ $message }}</p> @enderror
        </div>
    @else
        <div class="p-6 bg-green-50 rounded-[32px] border border-green-100 flex items-center gap-4">
            <div class="w-10 h-10 rounded-2xl bg-green-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-[13px] font-black text-green-900 leading-none">Alokasi Selesai</p>
                <p class="text-[11px] text-green-700/70 mt-1 font-medium">100% kuantitas sudah ditugaskan ke supplier.</p>
            </div>
        </div>
    @endif
</div>
