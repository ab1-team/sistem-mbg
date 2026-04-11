<x-app-layout title="Daftar Purchase Order">
    <x-container>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">Purchase Orders</h1>
                <p class="text-[13px] text-slate-500 font-medium mt-2">Daftar pesanan bahan baku ke supplier.</p>
            </div>

            <div class="flex items-center gap-3">
                @if (count($dapurs) > 1)
                    <form action="{{ route('purchase-orders.index') }}" method="GET" class="flex items-center gap-2">
                        <select name="dapur_id" onchange="this.form.submit()"
                            class="text-[13px] border-slate-200 rounded-xl focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-white text-slate-700 px-4 py-2 font-bold shadow-sm min-w-[200px]">
                            <option value="">Semua Dapur</option>
                            @foreach ($dapurs as $d)
                                <option value="{{ $d->id }}" {{ request('dapur_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if (auth()->user()->hasRole(['admin', 'superadmin', 'logistik']))
                    <x-btn href="{{ route('purchase-orders.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                        Buat PO Manual
                    </x-btn>
                @endif
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
                            <p class="text-[11px] text-slate-400 mt-1">{{ $po->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                        </x-table-td>
                        <x-table-td>
                            <span class="font-bold text-slate-700">{{ $po->dapur->name }}</span>
                        </x-table-td>
                        <x-table-td>
                            <span class="font-mono font-bold text-slate-900">Rp
                                {{ number_format($po->total_estimated_cost, 0, ',', '.') }}</span>
                        </x-table-td>
                        <x-table-td class="text-center">
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $po->status->color() }} uppercase whitespace-nowrap">
                                {{ $po->status->label() }}
                            </span>
                        </x-table-td>
                        <x-table-td class="text-right whitespace-nowrap">
                            <x-btn href="{{ route('finance.kitchen-invoices.download', $po) }}" target="_blank"
                                variant="ghost"
                                class="py-1.5! px-3! text-[11px]! mr-1 underline text-emerald-600 hover:text-emerald-700">
                                Invoice Dapur
                            </x-btn>
                            <x-btn href="{{ route('purchase-orders.show', $po) }}" variant="secondary"
                                class="py-1.5! px-3! text-[11px]!">
                                Detail
                            </x-btn>
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
    </x-container>
</x-app-layout>
