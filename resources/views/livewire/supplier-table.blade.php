<div>
    <x-smart-table-actions>
        <x-btn href="{{ route('suppliers.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Supplier
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="code" :active="$sortField === 'code'" :asc="$sortAsc">Kode</x-table-th>
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Supplier</x-table-th>
                <x-table-th sort="contact_name" :active="$sortField === 'contact_name'" :asc="$sortAsc">Kontak Person</x-table-th>
                <x-table-th>No. Telepon</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($suppliers as $supplier)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <div
                            class="px-2 py-1 rounded-lg bg-green-50 text-[11px] font-bold text-green-700 border border-green-100 inline-block uppercase ring-4 ring-green-900/5">
                            {{ $supplier->code }}
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <div class="flex flex-col">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="font-bold text-slate-900 uppercase tracking-tight hover:text-green-700 transition-colors">
                                {{ $supplier->name }}
                            </a>
                            <span
                                class="text-[11px] text-slate-400 font-medium">{{ $supplier->address ? Str::limit($supplier->address, 40) : 'Alamat tidak tersedia' }}</span>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-700 font-bold tracking-tight">{{ $supplier->contact_name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <div class="flex items-center gap-2">
                            <div class="p-1 rounded-md bg-slate-100 text-slate-400 border border-slate-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <span class="text-slate-500 font-medium tracking-tight">{{ $supplier->phone }}</span>
                        </div>
                    </x-table-td>
                    <x-table-td class="text-right">
                        <div
                            class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <x-btn href="{{ route('suppliers.edit', $supplier) }}" variant="secondary"
                                class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                            <x-dialog title="Hapus Supplier"
                                description="Apakah Anda yakin ingin menghapus supplier {{ $supplier->name }}? Tindakan ini tidak dapat dibatalkan."
                                action-label="Ya, Hapus" :action-url="route('suppliers.destroy', $supplier)" method="DELETE">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Tidak ada supplier ditemukan"
                            subtitle="Coba sesuaikan kata kunci pencarian Anda." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination --}}
        @if ($suppliers->hasPages())
            <div class="mt-auto">
                {{ $suppliers->links() }}
            </div>
        @endif
    </x-card>
</div>
