<x-app-layout title="Daftar Invoice Dapur (Konsolidasi)">
    <x-page-header title="Invoice Dapur (Konsolidasi)"
        subtitle="Rekapitulasi seluruh item per Dapur untuk rekonsiliasi internal." />

    <x-card :padding="false" class="overflow-hidden mt-6">
        <x-table>
            <x-slot name="thead">
                <x-table-th>No. PO</x-table-th>
                <x-table-th>Unit Dapur</x-table-th>
                <x-table-th>Periode</x-table-th>
                <x-table-th>Total Nilai</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($invoices as $po)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-table-td>
                        <span
                            class="font-bold text-slate-900 border-b border-dotted border-slate-300">{{ $po->po_number }}</span>
                    </x-table-td>
                    <x-table-td>{{ $po->dapur->name }}</x-table-td>
                    <x-table-td>{{ $po->menuPeriod->name ?? '-' }}</x-table-td>
                    <x-table-td>
                        <span class="font-mono font-bold">Rp
                            {{ number_format($po->total_estimated_cost, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td class="text-right">
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
                            subtitle="Invoice otomatis tersedia setelah PO diajukan." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
