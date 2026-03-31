<div>
    <x-smart-table-actions>
        <x-btn href="{{ route('periods.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Buka Periode Baru
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="code" :active="$sortField === 'code'" :asc="$sortAsc">Kode</x-table-th>
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Periode</x-table-th>
                <x-table-th sort="start_date" :active="$sortField === 'start_date'" :asc="$sortAsc">Rentang Tanggal</x-table-th>
                <x-table-th sort="status" :active="$sortField === 'status'" :asc="$sortAsc">Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($periods as $period)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <div class="px-2 py-1 rounded-lg bg-orange-50 text-[11px] font-bold text-orange-700 border border-orange-100 inline-block uppercase ring-4 ring-orange-900/5">
                            {{ $period->code }}
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <a href="{{ route('periods.show', $period) }}" class="font-bold text-slate-900 uppercase tracking-tight hover:text-green-700 transition-colors">
                            {{ $period->name }}
                        </a>
                    </x-table-td>
                    <x-table-td>
                        <div class="flex items-center gap-2">
                             <div class="flex flex-col text-right">
                                 <span class="text-[12px] font-bold text-slate-700">{{ $period->start_date->format('d M Y') }}</span>
                             </div>
                             <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                             <div class="flex flex-col">
                                 <span class="text-[12px] font-bold text-slate-700">{{ $period->end_date->format('d M Y') }}</span>
                             </div>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        @if($period->status == 'open')
                            <x-badge variant="success">OPEN</x-badge>
                        @elseif($period->status == 'closed')
                            <x-badge variant="error">CLOSED</x-badge>
                        @else
                            <x-badge variant="warning">LOCKED</x-badge>
                        @endif
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('periods.edit', $period) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                            <x-dialog title="Hapus Periode" 
                                description="Apakah Anda yakin ingin menghapus periode {{ $period->name }}? Seluruh data transaksi di periode ini akan terpengaruh."
                                action-label="Ya, Hapus" :action-url="route('periods.destroy', $period)" method="DELETE">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state
                            title="Tidak ada periode ditemukan"
                            subtitle="Coba sesuaikan kata kunci pencarian Anda."
                        />
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination --}}
        @if ($periods->hasPages())
            <div class="mt-auto">
                {{ $periods->links() }}
            </div>
        @endif
    </x-card>
</div>
