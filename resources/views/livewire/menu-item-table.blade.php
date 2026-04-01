<div>
    <x-smart-table-actions>
        <div class="flex flex-wrap items-center gap-3">
            <x-form-searchable-select wire:model.live="mealType" 
                class="w-48 text-[13px]"
                placeholder="Semua Tipe Makan"
                :selected="$mealType"
                :options="collect($mealTypes)->map(fn($t) => ['value' => $t, 'label' => ucfirst($t)])->prepend(['value' => '', 'label' => 'Semua Tipe Makan'])->toArray()"
            />
        </div>
        <x-btn href="{{ route('menu-items.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Menu
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Masakan</x-table-th>
                <x-table-th sort="meal_type" :active="$sortField === 'meal_type'" :asc="$sortAsc">Tipe</x-table-th>
                <x-table-th class="text-center">Porsi Standard</x-table-th>
                <x-table-th class="text-center">Jumlah Bahan (BOM)</x-table-th>
                <x-table-th sort="calories" :active="$sortField === 'calories'" :asc="$sortAsc" class="text-right">Kalori</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($menuItems as $menu)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 tracking-tight leading-tight">{{ $menu->name }}</span>
                            <span class="text-[11px] text-slate-400 truncate max-w-[200px]">{{ $menu->description ?? 'Tidak ada deskripsi' }}</span>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                            {{ $menu->meal_type }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-center font-medium text-slate-600">
                        {{ $menu->portion_size }} <span class="text-[10px] text-slate-400 uppercase">Porsi</span>
                    </x-table-td>
                    <x-table-td class="text-center">
                        <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-orange-50 text-orange-700 text-[11px] font-bold border border-orange-100">
                             <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            {{ $menu->boms_count }} Item
                        </div>
                    </x-table-td>
                    <x-table-td class="text-right font-mono text-slate-600">
                        {{ number_format($menu->calories, 1) }} <span class="text-[10px] text-slate-400 uppercase">Kcal</span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('menu-items.show', $menu) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Detail</x-btn>
                            <x-btn href="{{ route('menu-items.edit', $menu) }}" variant="secondary" class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                            <x-dialog title="Hapus Menu" 
                                description="Apakah Anda yakin ingin menghapus masakan {{ $menu->name }}? Data rencana menu yang menggunakan item ini juga akan ikut terhapus."
                                action-label="Ya, Hapus" :action-url="route('menu-items.destroy', $menu)" method="DELETE">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Menu belum terdaftar" subtitle="Mulai susun resep dan masakan baru dapur Anda." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($menuItems->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $menuItems->links() }}
            </div>
        @endif
    </x-card>
</div>
