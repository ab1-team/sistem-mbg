<div class="overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                    Bahan & Supplier</th>
                <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">
                    Target Porsi</th>
                <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">
                    Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach ($purchaseOrder->items as $item)
                <tr class="group hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-5">
                        <div class="flex items-start gap-3">
                            {{-- STATUS BADGE --}}
                            <div class="mt-1">
                                @if($item->assignments->count() > 0)
                                    <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                                @else
                                    <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.6)]"></div>
                                @endif
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-bold text-slate-900 tracking-tight leading-none">{{ $item->material->name }}</p>
                                    @if($item->assignments->count() == 0)
                                        <span class="text-[9px] font-black text-red-600 bg-red-50 px-1.5 py-0.5 rounded-full uppercase tracking-tighter border border-red-100">Butuh Supplier</span>
                                    @else
                                        <span class="text-[9px] font-black text-green-700 bg-green-50 px-1.5 py-0.5 rounded-full uppercase tracking-tighter border border-green-100">Ditugaskan</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1.5">
                                    @forelse($item->assignments as $assign)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ $assign->supplier->name }} ({{ number_format($assign->quantity_assigned, 1) }})
                                        </span>
                                    @empty
                                        <p class="text-[11px] text-slate-300 italic font-medium">Klik tombol Kelola Supplier di kanan</p>
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
                            <p class="text-[15px] font-black {{ $isComplete ? 'text-green-600' : 'text-slate-900' }} tracking-tight leading-none">
                                {{ number_format($item->quantity_to_order, 2) }}
                            </p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1">{{ $item->unit }}</p>
                            @if(!$isComplete && $item->quantity_to_order > 0)
                                <p class="text-[10px] font-black text-rose-500 mt-1">Sisa: {{ number_format($remaining, 2) }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right whitespace-nowrap">
                        @if (auth()->user()->hasRole(['admin', 'superadmin']) &&
                                ($purchaseOrder->status->value === 'dikirim_ke_yayasan' ||
                                    $purchaseOrder->status->value === 'direview_yayasan'))
                            <x-btn @click.stop="$dispatch('open-assignment', { itemId: {{ $item->id }} })"
                                variant="secondary" class="py-1.5! px-3! text-[11px]! font-black border-slate-200! hover:bg-slate-50! group">
                                <span class="group-hover:scale-105 transition-transform inline-block">Kelola Supplier</span>
                            </x-btn>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-slate-50 border-t border-slate-100">
            <tr>
                <td colspan="3" class="px-6 py-4 text-[11px] font-bold text-slate-400 text-center uppercase tracking-widest">
                    Akhir dari daftar bahan baku
                </td>
            </tr>
        </tfoot>
    </table>
</div>
