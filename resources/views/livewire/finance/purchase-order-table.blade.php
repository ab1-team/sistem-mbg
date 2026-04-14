<div>
    <x-smart-table-actions>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Dapur Filter (Admin Only) --}}
            @if(count($dapurs) > 0)
                <x-form-searchable-select wire:model.live="dapurId" class="w-48! text-[13px]!" placeholder="Semua Dapur"
                    :selected="$dapurId" :options="collect($dapurs)
                        ->map(fn($d) => ['value' => $d->id, 'label' => $d->name])
                        ->prepend(['value' => '', 'label' => 'Semua Dapur'])
                        ->toArray()" />
            @endif

            {{-- Status Filter --}}
            <x-form-searchable-select wire:model.live="status" class="w-48! text-[13px]!" placeholder="Semua Status"
                :selected="$status" :options="collect(App\Enums\PoStatus::cases())
                    ->map(fn($s) => ['value' => $s->value, 'label' => $s->label()])
                    ->prepend(['value' => '', 'label' => 'Semua Status'])
                    ->toArray()" />
        </div>

        @if (auth()->user()->hasRole(['admin', 'superadmin', 'logistik']))
            <x-btn href="{{ route('purchase-orders.create') }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Tambah PO
            </x-btn>
        @endif
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="po_number" :active="$sortField === 'po_number'" :asc="$sortAsc">No. PO</x-table-th>
                <x-table-th sort="dapur_id" :active="$sortField === 'dapur_id'" :asc="$sortAsc">Unit Dapur</x-table-th>
                <x-table-th sort="total_estimated_cost" :active="$sortField === 'total_estimated_cost'" :asc="$sortAsc">Estimasi Biaya</x-table-th>
                <x-table-th class="text-center">Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($purchaseOrders as $po)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <p class="font-black text-slate-900 tracking-tight leading-none">{{ $po->po_number }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $po->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-bold text-slate-700">{{ $po->dapur->name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($po->total_estimated_cost, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td class="text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $po->status->color() }} uppercase whitespace-nowrap">
                            {{ $po->status->label() }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('finance.kitchen-invoices.download', $po) }}" target="_blank"
                                variant="ghost" class="py-1.5! px-3! text-[11px]! text-emerald-600 hover:text-emerald-700">
                                Invoice
                            </x-btn>
                            <x-btn href="{{ route('purchase-orders.show', $po) }}" variant="secondary"
                                class="py-1.5! px-3! text-[11px]!">Detail</x-btn>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Belum ada Purchase Order"
                            subtitle="Generate PO dari rencana menu yang sudah disetujui." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($purchaseOrders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </x-card>
</div>
