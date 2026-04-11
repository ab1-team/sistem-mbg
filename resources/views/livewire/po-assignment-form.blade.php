<div x-data="{ show: @entangle('isOpen') }" 
     @keydown.escape.window="show = false"
     x-show="show" 
     class="fixed inset-0 z-100 overflow-hidden" 
     style="display: none;">
    
    {{-- BACKDROP --}}
     <div x-show="show" 
          x-transition:enter="ease-in-out duration-500" 
          x-transition:enter-start="opacity-0" 
          x-transition:enter-end="opacity-100" 
          x-transition:leave="ease-in-out duration-500" 
          x-transition:leave-start="opacity-100" 
          x-transition:leave-end="opacity-0" 
          class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
          @click="show = false"></div>

    <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
        {{-- PANEL --}}
        <div x-show="show" 
             x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
             x-transition:enter-start="translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="translate-x-full" 
             class="relative w-screen max-w-md">
                       <div class="h-full flex flex-col bg-white shadow-2xl overflow-y-scroll scroll-smooth scrollbar-hide">
                {{-- HEADER --}}
                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50 sticky top-0 z-10">
                    <div>
                        <h3 class="text-[18px] font-black text-slate-900 leading-none">Kelola Alokasi</h3>
                        <p class="text-[12px] text-slate-500 font-medium mt-1.5 whitespace-nowrap">Distribusi kuantitas pesanan ke supplier.</p>
                    </div>
                    <button @click="show = false" class="p-2 -mr-2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-8 pb-32">
                    @if($item)
                        <div class="flex items-center gap-4 p-5 bg-emerald-50 rounded-[28px] border border-emerald-100 mb-10">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[15px] font-black text-slate-900 leading-none mb-1 truncate">{{ ucwords($item->material->name ?? 'Bahan Tidak Diketahui') }}</p>
                                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest">{{ number_format($item->quantity_to_order, 2) }} {{ $item->unit }}</p>
                            </div>
                        </div>
                    @endif

                @if($item)
                    {{-- ASSIGNMENT LIST --}}
                    <div class="space-y-6 mb-12">
                        @if($item->assignments->count() > 0)
                            <div class="space-y-3">
                                <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Penugasan Saat Ini</h5>
                                @foreach($item->assignments as $assignment)
                                    <div class="group flex items-center justify-between p-4 bg-white border border-slate-100 rounded-3xl shadow-sm hover:shadow-md transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-brand-soft border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-[13px] font-black text-slate-900 leading-none">{{ ucwords($assignment->supplier->name) }}</p>
                                                <p class="text-[11px] text-slate-400 mt-1 font-bold tracking-tight">Rp {{ number_format($assignment->unit_price_agreed, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-right">
                                                <p class="text-[14px] font-bold text-emerald-600 leading-none">{{ number_format($assignment->quantity_assigned, 1) }}</p>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $item->unit }}</p>
                                            </div>
                                            <button wire:click="removeAssignment({{ $assignment->id }})" class="p-2 text-slate-200 hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center bg-slate-50 border border-dashed border-slate-200 rounded-[32px]">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <p class="text-[13px] text-slate-400 font-medium italic">Belum ada supplier terpilih</p>
                            </div>
                        @endif
                    </div>

                    {{-- ADD NEW FORM --}}
                    @if($this->remainingQuantity > 0 || $item->assignments->count() === 0)
                        <div class="relative pt-10 border-t border-slate-100 space-y-6">
                            @if($item->quantity_to_order <= 0)
                                <div class="p-5 bg-amber-50 border border-amber-100 rounded-[32px] flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-amber-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-amber-900/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-black text-amber-900 leading-none mb-1">Target Pesanan 0</p>
                                        <p class="text-[11px] text-amber-700/80 leading-relaxed font-medium">Anda bisa memasukkan kuantitas belanja secara manual di bawah.</p>
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-5">
                                <x-form-searchable-select 
                                    label="Pilih Supplier" 
                                    id="side_supplier_{{ $item->id }}" 
                                    name="supplier_id" 
                                    wire:model="supplier_id" 
                                    :options="$suppliers->map(fn($s) => ['value' => (string)$s->id, 'label' => ucwords($s->name)])"
                                    placeholder="Cari supplier..."
                                    required 
                                />

                                <div class="grid grid-cols-2 gap-4">
                                    <x-form-input type="number" step="0.01" label="Jumlah ({{ $item->unit }})" id="side_qty_{{ $item->id }}" name="quantity" wire:model="quantity" placeholder="0.00" required />
                                    <x-form-input type="number" label="Harga Net" id="side_price_{{ $item->id }}" name="unit_price" wire:model="unit_price" placeholder="Rp 0" required />
                                </div>
                            </div>

                            <x-btn wire:click="addAssignment" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white shadow-xl shadow-emerald-900/20">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambahkan Penugasan
                            </x-btn>
                            
                            @error('supplier_id') <p class="text-[11px] text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                            @error('quantity') <p class="text-[11px] text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
