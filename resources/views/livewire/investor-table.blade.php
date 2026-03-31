<div>
    <x-smart-table-actions>
        <x-btn href="{{ route('investors.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Investor
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="code" :active="$sortField === 'code'" :asc="$sortAsc">Kode</x-table-th>
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Investor</x-table-th>
                <x-table-th sort="share_percentage" :active="$sortField === 'share_percentage'" :asc="$sortAsc">Saham (%)</x-table-th>
                <x-table-th sort="join_date" :active="$sortField === 'join_date'" :asc="$sortAsc">Bergabung</x-table-th>
                <x-table-th>Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($investors as $investor)
                <tr class="hover:bg-slate-50/50 transition-colors group {{ !$investor->is_active ? 'opacity-60 grayscale-[0.5]' : '' }}">
                    <x-table-td>
                        <div class="px-2 py-1 rounded-lg bg-blue-50 text-[11px] font-bold text-blue-700 border border-blue-100 inline-block uppercase">
                            {{ $investor->code }}
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <div class="flex flex-col">
                            <a href="{{ route('investors.show', $investor) }}" class="font-bold text-slate-900 uppercase tracking-tight hover:text-green-700 transition-colors">
                                {{ $investor->name }}
                            </a>
                            <span class="text-[11px] text-slate-400 font-medium tracking-tighter">{{ $investor->identity_number ?: 'Identitas belum diisi' }}</span>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <div class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 rounded-lg border border-slate-200">
                             <span class="text-[14px] font-black text-slate-900">{{ number_format($investor->share_percentage, 2) }}</span>
                             <span class="text-[10px] text-slate-400 font-bold">%</span>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <div class="flex flex-col leading-none">
                             <span class="text-[13px] font-bold text-slate-700">{{ $investor->join_date->format('d M Y') }}</span>
                             <span class="text-[10px] text-slate-400 mt-0.5 tracking-tight">{{ $investor->join_date->diffForHumans() }}</span>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        @if($investor->is_active)
                            <x-badge variant="success">Aktif / Dividen</x-badge>
                        @else
                            <x-badge variant="error">Non-Aktif</x-badge>
                        @endif
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('investors.edit', $investor) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                            <x-dialog title="Hapus Investor" 
                                description="Apakah Anda yakin ingin menghapus {{ $investor->name }}? Saldo investasi yang tersisa mungkin akan terhapus."
                                action-label="Ya, Hapus" :action-url="route('investors.destroy', $investor)" method="DELETE">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state
                            title="Tidak ada investor ditemukan"
                            subtitle="Coba sesuaikan kata kunci pencarian Anda."
                        />
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination --}}
        @if ($investors->hasPages())
            <div class="mt-auto">
                {{ $investors->links() }}
            </div>
        @endif
    </x-card>
</div>
