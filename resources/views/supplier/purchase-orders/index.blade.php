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

                @forelse($purchaseOrders as $po)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <x-table-td>
                            <p class="font-black text-slate-900 tracking-tight leading-none">{{ $po->po_number }}</p>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $po->dapur->name }}</p>
                        </x-table-td>
                        <x-table-td>
                            <div class="flex flex-wrap gap-1">
                                @foreach($po->items as $item)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11px] font-bold text-slate-700">
                                        {{ $item->material->name }}
                                    </span>
                                @endforeach
                            </div>
                        </x-table-td>
                        <x-table-td class="text-right font-mono font-black text-slate-900">
                            <div class="flex flex-col items-end">
                                @foreach($po->items as $item)
                                    <div class="text-[13px]">
                                        {{ number_format($item->assignments->sum('quantity_assigned'), 2) }} 
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $item->unit }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </x-table-td>
                        <x-table-td class="text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $po->status->color() }} uppercase whitespace-nowrap">
                                {{ $po->status->label() }}
                            </span>
                        </x-table-td>
                        <x-table-td class="text-right">
                            <x-btn href="{{ route('supplier.purchase-orders.show', $po) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">
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
            
            @if($purchaseOrders->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $purchaseOrders->links() }}
                </div>
            @endif
        </x-card>
    </x-container>
</x-app-layout>
