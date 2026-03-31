<div>
    <x-smart-table-actions>
        <x-btn href="{{ route('dapurs.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Dapur
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="code" :active="$sortField === 'code'" :asc="$sortAsc">Kode</x-table-th>
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Dapur</x-table-th>
                <x-table-th>Penanggung Jawab</x-table-th>
                <x-table-th sort="created_at" :active="$sortField === 'created_at'" :asc="$sortAsc">Terdaftar</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($dapurs as $dapur)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <div
                            class="px-2 py-1 rounded-lg bg-slate-100 text-[11px] font-bold text-slate-600 border border-slate-200 inline-block uppercase ring-4 ring-slate-50">
                            {{ $dapur->code }}
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <a href="{{ route('dapurs.show', $dapur) }}" class="font-bold text-slate-900 uppercase tracking-tight hover:text-green-700 transition-colors">
                            {{ $dapur->name }}
                        </a>
                    </x-table-td>
                    <x-table-td>
                        <div class="flex items-center gap-2">
                            <div
                                class="w-6 h-6 rounded-full bg-green-50 text-green-700 flex items-center justify-center text-[9px] font-bold border border-green-100">
                                SA
                            </div>
                            <span class="text-slate-600 font-medium tracking-tight">Super Admin</span>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-400 font-medium">{{ $dapur->created_at->diffForHumans() }}</span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('dapurs.edit', $dapur) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                            <x-dialog title="Hapus Dapur" 
                                description="Apakah Anda yakin ingin menghapus Unit Dapur {{ $dapur->name }}? Data operasional terkait dapur ini mungkin akan terpengaruh."
                                action-label="Ya, Hapus" :action-url="route('dapurs.destroy', $dapur)" method="DELETE">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Tidak ada dapur ditemukan"
                            subtitle="Coba sesuaikan kata kunci pencarian Anda." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination --}}
        @if ($dapurs->hasPages())
            <div class="mt-auto">
                {{ $dapurs->links() }}
            </div>
        @endif
    </x-card>
</div>
