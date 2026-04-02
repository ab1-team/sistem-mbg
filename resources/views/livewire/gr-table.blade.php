<div>
    <x-smart-table-actions>
        <div class="flex flex-wrap items-center gap-3">
            @if(count($dapurs) > 1)
                <x-form-searchable-select wire:model.live="dapurId" class="w-48 text-[13px]" placeholder="Semua Dapur"
                    :selected="$dapurId" :options="collect($dapurs)
                        ->map(fn($d) => ['value' => $d->id, 'label' => $d->name])
                        ->prepend(['value' => '', 'label' => 'Semua Dapur'])
                        ->toArray()" />
            @endif
        </div>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden shadow-none border-none">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="po_number" :active="$sortField === 'po_number'" :asc="$sortAsc">Nomor PO</x-table-th>
                <x-table-th>Dapur Asal</x-table-th>
                <x-table-th sort="status" :active="$sortField === 'status'" :asc="$sortAsc" class="text-center">Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($purchaseOrders as $po)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <p class="font-bold text-slate-900 tracking-tight leading-none mb-1">{{ $po->po_number }}</p>
                        <p class="font-mono text-[11px] text-slate-400 font-bold tracking-tighter">{{ $po->created_at->format('d M Y') }}</p>
                    </x-table-td>
                    <x-table-td>
                        <p class="font-bold text-slate-700 text-[13px] tracking-tight">{{ $po->dapur->name }}</p>
                        <p class="text-[11px] text-slate-400 italic">Target: {{ $po->menuPeriod->title ?? '-' }}</p>
                    </x-table-td>
                    <x-table-td class="text-center">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $po->status->color() }} uppercase whitespace-nowrap">
                            {{ $po->status->label() }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <x-btn href="{{ route('gr.create', $po) }}" class="py-1.5! px-3! text-[11px]!">
                            Mulai QC
                        </x-btn>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <x-empty-state title="Belum ada pesanan aktif" subtitle="Gunakan kolom pencarian di atas jika Anda mencari PO tertentu." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if($purchaseOrders->hasPages())
            <div class="p-4 border-t border-slate-50">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </x-card>
</div>
