<div>
    <x-dialog name="po-verification-modal" title="Verifikasi Penerimaan Dapur" :show="$isOpen">
        @if($purchaseOrder)
            <div class="space-y-6">
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <p class="text-[12px] font-bold text-slate-500 uppercase mb-3">Ringkasan Penerimaan Barang</p>
                    <div class="space-y-3">
                        @foreach($purchaseOrder->items as $item)
                            <div class="flex items-center justify-between py-2 border-b border-slate-200 last:border-0">
                                <div>
                                    <p class="text-[13px] font-bold text-slate-800">{{ ucwords($item->material->name) }}</p>
                                    <div class="flex gap-2 mt-1">
                                        @foreach($item->assignments as $assign)
                                            <span class="text-[10px] text-slate-400">
                                                {{ $assign->subSupplier ? $assign->subSupplier->name : $assign->supplier->name }}: 
                                                <b class="text-slate-600">{{ number_format($assign->quantity_received, 1) }}</b>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[13px] font-black {{ $item->quantity_received >= $item->quantity_to_order ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ number_format($item->quantity_received, 1) }} / {{ number_format($item->quantity_to_order, 1) }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $item->unit }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <x-form-textarea 
                    label="Catatan Verifikasi (Opsional)" 
                    wire:model="notes" 
                    placeholder="Masukan catatan tambahan untuk finance jika ada..." 
                />

                <div class="flex flex-col gap-3 pt-4">
                    <x-btn wire:click="verify" class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-900/20 py-3!">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Verifikasi & Selesaikan (Terbitkan Invoice)
                    </x-btn>
                    
                    <button wire:click="markDeficit" class="text-[12px] font-bold text-rose-600 hover:text-rose-700 transition-colors py-2 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Laporkan Defisit (Butuh Belanja Lagi)
                    </button>

                    <x-btn @click="$dispatch('close-modal', 'po-verification-modal')" variant="secondary" class="w-full justify-center">
                        Tutup
                    </x-btn>
                </div>
            </div>
        @endif
    </x-dialog>
</div>
