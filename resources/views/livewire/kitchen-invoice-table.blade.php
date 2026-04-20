<div>
    <x-smart-table-actions>
        <div class="flex flex-wrap items-center gap-3">
            @if (count($dapurs) > 1)
                <x-form-searchable-select wire:model.live="dapurId" class="w-48 text-[13px]" placeholder="Semua Dapur"
                    :selected="$dapurId" :options="collect($dapurs)
                        ->map(fn($d) => ['value' => $d->id, 'label' => $d->name])
                        ->prepend(['value' => '', 'label' => 'Semua Dapur'])
                        ->toArray()" />
            @endif
        </div>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="po_number" :active="$sortField === 'po_number'" :asc="$sortAsc">Nomor PO</x-table-th>
                <x-table-th sort="dapur_id" :active="$sortField === 'dapur_id'" :asc="$sortAsc">Unit Dapur</x-table-th>
                <x-table-th sort="menu_period_id" :active="$sortField === 'menu_period_id'" :asc="$sortAsc">Periode</x-table-th>
                <x-table-th sort="total_estimated_cost" :active="$sortField === 'total_estimated_cost'" :asc="$sortAsc">Total Nilai</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($invoices as $po)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <span class="font-bold text-slate-900 border-b border-dotted border-slate-300">
                            {{ $po->po_number }}
                        </span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-bold text-slate-700 text-[13px] tracking-tight">
                            {{ $po->dapur->name }}
                        </span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-[13px] text-slate-600">
                            {{ $po->menuPeriod->title ?? '-' }}
                        </span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-mono font-bold text-[14px] text-emerald-700">Rp
                            {{ number_format($po->total_estimated_cost, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <x-btn href="{{ route('finance.kitchen-invoices.download', $po) }}" target="_blank"
                            variant="secondary" class="py-1.5! px-3! text-[11px]!">
                            Download PDF
                        </x-btn>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Belum ada invoice dapur"
                            subtitle="Invoice otomatis tersedia setelah PO diajukan. Gunakan fitur pencarian jika Anda mencari invoice tertentu." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($invoices->hasPages())
            <div class="p-4 border-t border-slate-50">
                {{ $invoices->links() }}
            </div>
        @endif
    </x-card>
</div>
