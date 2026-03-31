<div>
    <x-smart-table-actions>
        <div class="flex items-center gap-3">
            <select wire:model.live="category"
                class="text-[13px] border-slate-200 rounded-xl focus:ring-green-500/20 focus:border-green-500 transition-all bg-white text-slate-600 px-3 py-2">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th>Bahan Baku</x-table-th>
                <x-table-th>Kategori</x-table-th>
                <x-table-th sort="current_stock" :active="$sortField === 'current_stock'" :asc="$sortAsc" class="text-right">Stok Saat
                    Ini</x-table-th>
                <x-table-th class="text-center">Status</x-table-th>
            </x-slot>

            @forelse($stocks as $stock)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-table-td>
                        <p class="font-bold text-slate-900 tracking-tight leading-none mb-1">
                            {{ $stock->material->name }}</p>
                        <p class="font-mono text-[11px] text-slate-400 font-bold uppercase">{{ $stock->material->code }}
                        </p>
                    </x-table-td>
                    <x-table-td>
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">{{ $stock->material->category }}</span>
                    </x-table-td>
                    <x-table-td class="text-right">
                        <span
                            class="text-[15px] font-bold text-slate-900">{{ number_format($stock->current_stock, 2) }}</span>
                        <span class="text-[10px] text-slate-400 ml-1 uppercase">{{ $stock->material->unit }}</span>
                    </x-table-td>
                    <x-table-td class="text-center">
                        @php
                            $status = 'Aman';
                            $color = 'bg-green-50 text-green-700 border-green-100';

                            if ($stock->current_stock <= 0) {
                                $status = 'Habis';
                                $color = 'bg-red-50 text-red-700 border-red-100';
                            } elseif ($stock->current_stock < 5) {
                                $status = 'Rendah';
                                $color = 'bg-orange-50 text-orange-700 border-orange-100';
                            }
                        @endphp
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $color }} uppercase tracking-widest">
                            {{ $status }}
                        </span>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <x-empty-state title="Belum ada data stok"
                            subtitle="Gunakan kolom pencarian jika Anda mencari bahan tertentu." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($stocks->hasPages())
            <div class="p-4 border-t border-slate-50">
                {{ $stocks->links() }}
            </div>
        @endif
    </x-card>
</div>
