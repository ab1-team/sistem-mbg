<div class="overflow-hidden">
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
                        @if (auth()->user()->hasRole(['admin', 'superadmin']) &&
                                ($purchaseOrder->status->value === 'dikirim_ke_yayasan' ||
                                    $purchaseOrder->status->value === 'direview_yayasan'))
                            <x-btn @click.stop="$dispatch('open-assignment', { itemId: {{ $item->id }} })"
                                variant="secondary" class="py-1.5! px-3! text-[11px]! font-bold">
                                Kelola Supplier
                            </x-btn>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
