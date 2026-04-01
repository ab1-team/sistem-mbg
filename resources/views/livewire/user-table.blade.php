<div>
    <x-smart-table-actions>
        <x-btn href="{{ route('users.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah User
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Pengguna</x-table-th>
                <x-table-th sort="email" :active="$sortField === 'email'" :asc="$sortAsc">Email</x-table-th>
                <x-table-th>Role / Akses</x-table-th>
                <x-table-th sort="created_at" :active="$sortField === 'created_at'" :asc="$sortAsc">Terdaftar</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($users as $user)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[11px] font-bold text-slate-500 border border-slate-200 uppercase">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <a href="{{ route('users.show', $user) }}"
                                class="font-bold text-slate-900 hover:text-green-700 transition-colors">{{ $user->name }}</a>
                        </div>
                    </x-table-td>
                    <x-table-td>{{ $user->email }}</x-table-td>
                    <x-table-td>
                        @foreach ($user->roles as $role)
                            <x-badge variant="success">{{ ucwords(str_replace('_', ' ', $role->name)) }}</x-badge>
                        @endforeach
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-400 font-medium">{{ $user->created_at->diffForHumans() }}</span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('users.edit', $user) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                            <x-dialog title="Hapus Pengguna" 
                                description="Apakah Anda yakin ingin menghapus hak akses {{ $user->name }}? Pengguna ini tidak akan bisa login lagi."
                                action-label="Ya, Hapus" :action-url="route('users.destroy', $user)" method="DELETE">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Tidak ada user ditemukan"
                            subtitle="Coba sesuaikan kata kunci pencarian Anda." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination --}}
        <div class="mt-auto">
            {{ $users->links() }}
        </div>
    </x-card>
</div>
