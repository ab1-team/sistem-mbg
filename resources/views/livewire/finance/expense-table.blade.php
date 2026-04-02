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

            <x-btn href="{{ route('finance.expenses.create') }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Catat Pengeluaran
            </x-btn>
        </div>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="created_at" :active="$sortField === 'created_at'" :asc="$sortAsc">Tanggal</x-table-th>
                <x-table-th>Dapur</x-table-th>
                <x-table-th sort="category" :active="$sortField === 'category'" :asc="$sortAsc">Kategori</x-table-th>
                <x-table-th sort="amount" :active="$sortField === 'amount'" :asc="$sortAsc" class="text-right">Jumlah</x-table-th>
                <x-table-th>Keterangan</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($expenses as $expense)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <span class="text-slate-500 font-medium">{{ $expense->created_at->format('d/m/Y') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-bold text-slate-900 group-hover:text-amber-700 transition-colors">{{ $expense->dapur->name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <x-badge :variant="$expense->category === 'bahan_baku' ? 'warning' : 'secondary'">
                            {{ ucwords(str_replace('_', ' ', $expense->category)) }}
                        </x-badge>
                    </x-table-td>
                    <x-table-td class="text-right">
                        <span class="font-bold text-slate-900 font-mono">Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-400 text-[11px] font-medium leading-relaxed block max-w-xs truncate" title="{{ $expense->notes }}">
                            {{ $expense->notes ?: '-' }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                             <x-btn href="{{ route('finance.expenses.edit', $expense) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                             <x-dialog title="Hapus Pengeluaran" 
                                description="Apakah Anda yakin ingin menghapus data pengeluaran sebesar Rp {{ number_format($expense->amount, 0, ',', '.') }} dari {{ $expense->dapur->nama }}?"
                                action-label="Ya, Hapus" wire:click="deleteExpense({{ $expense->id }})">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Tidak ada data pengeluaran"
                            subtitle="Catat pengeluaran baru untuk periode ini." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-auto">
            {{ $expenses->links() }}
        </div>
    </x-card>
</div>
