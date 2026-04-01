<div>
    <x-smart-table-actions>
        <x-btn href="{{ route('finance.revenues.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Entry Pendapatan
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="created_at" :active="$sortField === 'created_at'" :asc="$sortAsc">Tanggal</x-table-th>
                <x-table-th>Dapur</x-table-th>
                <x-table-th>Periode</x-table-th>
                <x-table-th sort="amount" :active="$sortField === 'amount'" :asc="$sortAsc" class="text-right">Jumlah</x-table-th>
                <x-table-th>Keterangan</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($revenues as $revenue)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <span class="text-slate-500 font-medium">{{ $revenue->created_at->format('d/m/Y') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-bold text-slate-900 group-hover:text-green-700 transition-colors">{{ $revenue->dapur->nama }}</span>
                    </x-table-td>
                    <x-table-td>
                        <x-badge variant="info">{{ $revenue->period->name }}</x-badge>
                    </x-table-td>
                    <x-table-td class="text-right">
                        <span class="font-bold text-slate-900">Rp {{ number_format($revenue->amount, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-400 text-[11px] font-medium leading-relaxed block max-w-xs truncate" title="{{ $revenue->notes }}">
                            {{ $revenue->notes ?: '-' }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                             <x-btn href="{{ route('finance.revenues.edit', $revenue) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                             <x-dialog title="Hapus Pendapatan" 
                                description="Apakah Anda yakin ingin menghapus data pendapatan sebesar Rp {{ number_format($revenue->amount, 0, ',', '.') }} dari {{ $revenue->dapur->nama }}?"
                                action-label="Ya, Hapus" wire:click="deleteRevenue({{ $revenue->id }})">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Tidak ada data pendapatan"
                            subtitle="Catat pendapatan baru untuk periode ini." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-auto">
            {{ $revenues->links() }}
        </div>
    </x-card>
</div>
