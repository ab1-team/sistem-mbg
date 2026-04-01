<x-app-layout title="Daftar Purchase Order">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">Purchase Orders</h1>
            <p class="text-[13px] text-slate-500 font-medium mt-2">Daftar pesanan bahan baku ke supplier.</p>
        </div>
    </div>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th>No. PO</x-table-th>
                <x-table-th>Unit Dapur</x-table-th>
                <x-table-th>Estimasi Biaya</x-table-th>
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
                    <x-table-td class="text-right">
                        <x-btn href="{{ route('purchase-orders.show', $po) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">
                            Detail
                        </x-btn>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Belum ada Purchase Order" subtitle="Generate PO dari rencana menu yang sudah disetujui." />
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
</x-app-layout>
