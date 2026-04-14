<div>
    @if(auth()->user()->hasRole('superadmin'))
        <x-smart-table-actions>
            <x-btn href="{{ route('dapurs.create') }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Tambah Dapur
            </x-btn>
        </x-smart-table-actions>
    @endif

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="code" :active="$sortField === 'code'" :asc="$sortAsc">Kode</x-table-th>
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Dapur</x-table-th>
                <x-table-th>Penanggung Jawab</x-table-th>
                <x-table-th sort="created_at" :active="$sortField === 'created_at'" :asc="$sortAsc">Terdaftar</x-table-th>
                @if(auth()->user()->hasAnyRole(['superadmin', 'admin_yayasan']))
                    <x-table-th class="text-right">Aksi</x-table-th>
                @endif
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
                    @if(auth()->user()->hasAnyRole(['superadmin', 'admin_yayasan']))
                        <x-table-td class="text-right py-3 px-4">
                            <div class="flex items-center justify-end gap-2">
                                <x-btn href="{{ route('dapurs.edit', $dapur) }}" variant="secondary"
                                    class="py-1.5! px-3! text-[11px]!">Edit</x-btn>

                                @if(auth()->user()->hasRole('superadmin'))
                                    <x-dialog title="Hapus Dapur" name="delete-dapur-{{ $dapur->id }}">
                                        <x-slot name="trigger">
                                            <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!" 
                                                x-on:click="$dispatch('open-modal', 'delete-dapur-{{ $dapur->id }}')">
                                                Hapus
                                            </x-btn>
                                        </x-slot>

                                        <p class="text-[13px] text-slate-600 leading-relaxed">
                                            Apakah Anda yakin ingin menghapus Unit Dapur <span class="font-bold text-slate-900">{{ $dapur->name }}</span>? 
                                            Semua data operasional dan akun keuangan terkait dapur ini akan hilang secara permanen.
                                        </p>

                                        <x-slot name="footer">
                                            <x-btn variant="secondary" x-on:click="$dispatch('close-modal', 'delete-dapur-{{ $dapur->id }}')">Batal</x-btn>
                                            <form action="{{ route('dapurs.destroy', $dapur) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-btn type="submit" variant="danger">Ya, Hapus Permanen</x-btn>
                                            </form>
                                        </x-slot>
                                    </x-dialog>
                                @endif
                            </div>
                        </x-table-td>
                    @endif
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
