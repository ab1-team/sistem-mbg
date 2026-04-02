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

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="invoice_number" :active="$sortField === 'invoice_number'" :asc="$sortAsc">Nomor Invoice</x-table-th>
                <x-table-th>Referensi PO</x-table-th>
                <x-table-th>Supplier</x-table-th>
                <x-table-th sort="grand_total" :active="$sortField === 'grand_total'" :asc="$sortAsc">Total Tagihan</x-table-th>
                <x-table-th sort="status" :active="$sortField === 'status'" :asc="$sortAsc" class="text-center">Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($invoices as $invoice)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <p class="font-bold text-slate-900 tracking-tight leading-none mb-1">
                            {{ $invoice->invoice_number }}</p>
                        <p class="text-[11px] text-slate-400 font-bold tracking-tighter">Jatuh Tempo:
                            {{ $invoice->due_date->format('d M Y') }}</p>
                    </x-table-td>
                    <x-table-td>
                        <span
                            class="font-mono text-[12px] font-bold text-slate-400 uppercase tracking-tighter">{{ $invoice->purchaseOrder->po_number }}</span>
                    </x-table-td>
                    <x-table-td>
                        <p class="font-bold text-slate-700 text-[13px] tracking-tight">{{ $invoice->supplier->name }}
                        </p>
                        <p class="text-[11px] text-slate-400 italic">Dapur: {{ $invoice->dapur->name }}</p>
                    </x-table-td>
                    <x-table-td>
                        <p class="font-bold text-slate-900 text-[14px]">Rp
                            {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                    </x-table-td>
                    <x-table-td class="text-center">
                        @php
                            $statusColors = [
                                'generated' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                'diverifikasi' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'dibayar' => 'bg-green-50 text-green-700 border-green-100',
                                'dibatalkan' => 'bg-red-50 text-red-700 border-red-100',
                            ];
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColors[$invoice->status] ?? 'bg-slate-50' }} uppercase whitespace-nowrap">
                            {{ str_replace('_', ' ', $invoice->status) }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <x-btn href="{{ route('invoices.show', $invoice) }}" variant="secondary"
                            class="py-1.5! px-3! text-[11px]!">
                            Detail
                        </x-btn>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Belum ada tagihan"
                            subtitle="Gunakan kolom pencarian di atas jika Anda mencari invoice tertentu." />
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
