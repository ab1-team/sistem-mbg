<x-app-layout title="Pesanan Saya">
    <x-container>
        <x-page-header title="Pesanan Saya" 
            subtitle="Daftar pesanan bahan baku dari Yayasan MBG." />

        <x-card :padding="false" class="overflow-hidden">
            <x-table>
                <x-slot name="thead">
                    <x-table-th>No. PO</x-table-th>
                    <x-table-th>Bahan Baku</x-table-th>
                    <x-table-th class="text-right">Kuantitas</x-table-th>
                    <x-table-th class="text-center">Status</x-table-th>
                    <x-table-th class="text-right">Aksi</x-table-th>
                </x-slot>

                @forelse($assignments as $assignment)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <x-table-td>
                            <p class="font-black text-slate-900 tracking-tight leading-none">{{ $assignment->item->purchaseOrder->po_number }}</p>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $assignment->item->purchaseOrder->dapur->name }}</p>
                        </x-table-td>
                        <x-table-td>
                            <span class="font-bold text-slate-700">{{ $assignment->item->material->name }}</span>
                        </x-table-td>
                        <x-table-td class="text-right font-mono font-black text-slate-900">
                            {{ number_format($assignment->quantity_assigned, 2) }} <span class="text-[10px] text-slate-400">{{ $assignment->item->unit }}</span>
                        </x-table-td>
                        <x-table-td class="text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $assignment->item->purchaseOrder->status->color() }} uppercase whitespace-nowrap">
                                {{ $assignment->item->purchaseOrder->status->label() }}
                            </span>
                        </x-table-td>
                        <x-table-td class="text-right">
                            <x-btn href="{{ route('supplier.purchase-orders.show', $assignment) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">
                                Buka Detail
                            </x-btn>
                        </x-table-td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-empty-state title="Belum ada pesanan masuk" subtitle="Tunggu penugasan dari Admin Yayasan." />
                        </td>
                    </tr>
                @endforelse
            </x-table>
            
            @if($assignments->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $assignments->links() }}
                </div>
            @endif
        </x-card>
    </x-container>
</x-app-layout>
