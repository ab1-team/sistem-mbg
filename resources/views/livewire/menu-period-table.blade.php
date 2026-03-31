<div>
    <x-smart-table-actions>
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="status" 
                class="text-[13px] border-slate-200 rounded-xl focus:ring-green-500/20 focus:border-green-500 transition-all bg-white text-slate-600 px-3 py-2">
                <option value="">Semua Status</option>
                <option value="draf">Draf</option>
                <option value="menunggu_approval">Menunggu Approval</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
        <x-btn href="{{ route('menu-periods.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Buat Rencana
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="title" :active="$sortField === 'title'" :asc="$sortAsc">Rencana Menu</x-table-th>
                <x-table-th>Unit Dapur</x-table-th>
                <x-table-th>Periode</x-table-th>
                <x-table-th class="text-center">Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($menuPeriods as $item)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <p class="font-bold text-slate-900 tracking-tight leading-none">{{ $item->title }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Dibuat oleh: {{ $item->creator->name }}</p>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-medium text-slate-600 truncate block max-w-[150px]">{{ $item->dapur->name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-mono text-[12px] text-slate-500">{{ $item->period->name }}</span>
                    </x-table-td>
                    <x-table-td class="text-center">
                        @php
                            $statusColors = [
                                'draf' => 'bg-slate-100 text-slate-600 border-slate-200',
                                'menunggu_approval' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'disetujui' => 'bg-green-50 text-green-700 border-green-100',
                                'ditolak' => 'bg-red-50 text-red-700 border-red-100',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColors[$item->status] }} uppercase whitespace-nowrap">
                            {{ str_replace('_', ' ', $item->status) }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('menu-periods.show', $item) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Detail</x-btn>
                            @if($item->status === 'draf' || $item->status === 'ditolak')
                                <x-btn href="{{ route('menu-periods.edit', $item) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                                <x-dialog title="Hapus Rencana" 
                                    description="Apakah Anda yakin ingin menghapus rencana {{ $item->title }}? Data jadwal harian di dalamnya akan ikut terhapus."
                                    action-label="Ya, Hapus" :action-url="route('menu-periods.destroy', $item)" method="DELETE">
                                    <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                                </x-dialog>
                            @endif
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Belum ada rencana menu" subtitle="Buat jadwal makanan harian untuk unit dapur Anda." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($menuPeriods->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $menuPeriods->links() }}
            </div>
        @endif
    </x-card>
</div>
