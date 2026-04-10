<div class="overflow-hidden" x-data="{ confirmingDelete: null }">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <x-table-th class="normal-case!">Bahan & Supplier</x-table-th>
                <x-table-th class="text-right normal-case!">Jumlah Total</x-table-th>
                <x-table-th class="text-right normal-case!">Aksi</x-table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach ($purchaseOrder->items as $item)
                <tr class="group hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-5">
                        <div class="flex items-start gap-3">
                            {{-- STATUS DOT (Muted) --}}
                            <div class="mt-1">
                                @if ($item->assignments->count() > 0)
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                @else
                                    <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                                @endif
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <p class="text-[14px] font-bold text-slate-800 leading-none">
                                        {{ ucwords($item->material->name) }}</p>
                                    @if ($item->assignments->count() == 0)
                                        <span
                                            class="text-[9px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full uppercase tracking-wider border border-rose-100">Butuh
                                            Supplier</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($item->assignments as $assign)
                                        <span
                                            class="inline-flex items-center gap-1.5 text-[11px] font-medium text-slate-600 bg-white px-2.5 py-1 rounded-lg border border-slate-200 shadow-sm">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor"
                                                stroke-width="2.5" viewBox="0 0 24 24">
                                                <path
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ ucwords($assign->supplier->name ?? 'N/A') }} • <span
                                                class="font-bold text-slate-900">{{ number_format($assign->quantity_assigned, 1) }}</span>
                                        </span>
                                    @empty
                                        <p class="text-[11px] text-slate-400 italic">Belum ada alokasi supplier</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right whitespace-nowrap">
                        @php
                            $assigned = $item->assignments->sum('quantity_assigned');
                            $isComplete = abs($item->quantity_to_order - $assigned) < 0.001;
                            $remaining = $item->quantity_to_order - $assigned;
                        @endphp
                        <div class="inline-block text-right">
                            <p
                                class="text-[14px] font-bold {{ $isComplete ? 'text-green-700' : 'text-slate-900' }} tracking-tight leading-none">
                                {{ number_format($item->quantity_to_order, 2) }}
                            </p>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase mt-1">{{ $item->unit }}</p>
                            @if (!$isComplete && $item->quantity_to_order > 0)
                                <p class="text-[10px] font-bold text-rose-500 mt-1">Sisa:
                                    {{ number_format($remaining, 2) }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            @if (auth()->user()->hasRole(['admin', 'superadmin', 'logistik']) &&
                                    in_array($purchaseOrder->status->value, ['draf', 'dikirim_ke_yayasan', 'direview_yayasan']))
                                <button @click="confirmingDelete = {{ $item->id }}"
                                    class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif

                            @if (auth()->user()->hasRole(['admin', 'superadmin']) &&
                                    ($purchaseOrder->status->value === 'dikirim_ke_yayasan' ||
                                        $purchaseOrder->status->value === 'direview_yayasan'))
                                <x-btn @click.stop="$dispatch('open-assignment', { itemId: {{ $item->id }} })"
                                    variant="secondary" class="py-1.5! px-3! text-[11px]! font-bold">
                                    Kelola Supplier
                                </x-btn>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER: ADD ITEM --}}
    @if (auth()->user()->hasRole(['admin', 'superadmin', 'logistik']) &&
            in_array($purchaseOrder->status->value, ['draf', 'dikirim_ke_yayasan', 'direview_yayasan']))
        <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex justify-center gap-4">
            <button @click="$dispatch('open-add-item', { poId: {{ $purchaseOrder->id }} })"
                class="inline-flex items-center gap-2 text-[13px] font-bold text-emerald-700 hover:text-emerald-800 transition-colors py-2 px-4 rounded-xl hover:bg-emerald-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                Tambah Barang Baru Manual
            </button>

            <button @click="$dispatch('open-import-po', { poId: {{ $purchaseOrder->id }} })"
                class="inline-flex items-center gap-2 text-[13px] font-bold text-blue-700 hover:text-blue-800 transition-colors py-2 px-4 rounded-xl hover:bg-blue-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Barang (Excel/CSV)
            </button>
        </div>
    @endif

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div x-show="confirmingDelete !== null" 
         class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="confirmingDelete = null" 
             class="bg-white rounded-[24px] shadow-xl w-full max-w-sm overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="p-6">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 border border-rose-100 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-[16px] font-bold text-slate-900 mb-2 tracking-tight">Hapus Barang?</h3>
                <p class="text-[13px] text-slate-500 mb-6 leading-relaxed">Barang ini akan dihapus dari daftar pesanan. Tindakan ini tidak dapat dibatalkan.</p>
                
                <div class="flex gap-3">
                    <button @click="confirmingDelete = null" type="button" class="flex-1 px-4 py-2.5 rounded-xl font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-[13px]">
                        Batal
                    </button>
                    <button @click="$wire.removeItem(confirmingDelete); confirmingDelete = null" type="button" class="flex-1 px-4 py-2.5 rounded-xl font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-all text-[13px]">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
