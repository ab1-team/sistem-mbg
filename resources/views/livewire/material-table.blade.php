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

            <x-form-searchable-select wire:model.live="category" class="w-48 text-[13px]" placeholder="Semua Kategori"
                :selected="$category" :options="collect($categories)
                    ->map(fn($t) => ['value' => $t, 'label' => ucfirst($t)])
                    ->prepend(['value' => '', 'label' => 'Semua Kategori'])
                    ->toArray()" />

            <x-dialog title="Import Bahan Baku" name="import-modal">
                <x-slot name="trigger">
                    <x-btn variant="secondary" x-on:click="$dispatch('open-modal', 'import-modal')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import
                    </x-btn>
                </x-slot>

                <form action="{{ route('materials.import') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    <div
                        class="p-4 border-2 border-dashed border-slate-100 rounded-2xl bg-slate-50/50 flex flex-col items-center gap-3 text-center">
                        <div
                            class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[13px] font-bold text-slate-700">Pilih File Excel/CSV</p>
                            <p class="text-[11px] text-slate-500">Maksimal ukuran file 5MB</p>
                        </div>
                        <input type="file" name="file"
                            class="text-[12px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all"
                            required>
                    </div>

                    <div class="bg-green-50 border border-green-100 rounded-xl p-3 flex gap-3">
                        <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-[11px] text-green-800 leading-relaxed">
                            Gunakan template kami untuk memastikan format data benar.
                            <a href="{{ route('materials.download-template') }}"
                                class="font-bold underline hover:text-green-900 transition-colors">Unduh Template CSV</a>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-btn type="button" variant="secondary"
                            x-on:click="$dispatch('close-modal', 'import-modal')">Batal</x-btn>
                        <x-btn type="submit">Mulai Import</x-btn>
                    </div>
                </form>
            </x-dialog>
        </div>
        <x-btn href="{{ route('materials.create') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Bahan
        </x-btn>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="code" :active="$sortField === 'code'" :asc="$sortAsc">Kode</x-table-th>
                <x-table-th sort="name" :active="$sortField === 'name'" :asc="$sortAsc">Nama Bahan</x-table-th>
                <x-table-th sort="category" :active="$sortField === 'category'" :asc="$sortAsc">Kategori</x-table-th>
                <x-table-th class="text-center">Sisa Stok</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($materials as $material)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <span
                            class="font-mono text-[12px] font-bold text-slate-400 tracking-tighter">{{ $material->code }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-bold text-slate-900 tracking-tight">{{ $material->name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <x-badge variant="gray">{{ $material->category }}</x-badge>
                    </x-table-td>
                    <x-table-td class="text-center font-medium text-slate-500">
                        {{ $material->min_stock_threshold }} <span
                            class="text-[10px] uppercase">{{ $material->unit }}</span>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div
                            class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-200">
                            <x-btn href="{{ route('materials.edit', $material) }}" variant="secondary"
                                class="py-1.5! px-3! text-[11px]!">Edit</x-btn>
                            <x-dialog title="Hapus Bahan"
                                description="Apakah Anda yakin ingin menghapus {{ $material->name }}? Data yang terkait dengan bahan ini mungkin akan terpengaruh."
                                action-label="Ya, Hapus" :action-url="route('materials.destroy', $material)" method="DELETE">
                                <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Hapus</x-btn>
                            </x-dialog>
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Belum ada bahan baku"
                            subtitle="Impor dari Excel atau tambah secara manual." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($materials->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $materials->links() }}
            </div>
        @endif
    </x-card>
</div>
