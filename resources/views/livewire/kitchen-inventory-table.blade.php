<div>
    <x-smart-table-actions>
        <div class="flex items-center gap-3">
            <x-slot name="actions">
                <select wire:model.live="category"
                    class="text-[13px] border-slate-200 rounded-xl focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-white text-slate-700 px-4 py-2.5 font-bold shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">{{ Str::headline($cat) }}</option>
                    @endforeach
                </select>
            </x-slot>
        </div>
    </x-smart-table-actions>

    <x-card :padding="false" class="overflow-hidden border-slate-100 shadow-sm">
        <x-table>
            <x-slot name="thead">
                <x-table-th>Bahan Baku</x-table-th>
                <x-table-th>Kategori</x-table-th>
                <x-table-th sort="current_stock" :active="$sortField === 'current_stock'" :asc="$sortAsc" class="text-right">Stok Gudang</x-table-th>
                <x-table-th class="text-center">Status</x-table-th>
            </x-slot>

            @forelse($stocks as $stock)
                <tr class="hover:bg-slate-50/50 transition-colors border-b border-slate-50 last:border-0">
                    <x-table-td>
                        <p class="font-bold text-slate-900 tracking-tight leading-none mb-1">
                            {{ $stock->material->name }}</p>
                        <p class="font-mono text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $stock->material->code }}
                        </p>
                    </x-table-td>
                    <x-table-td>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 uppercase border border-slate-200">
                            {{ Str::headline($stock->material->category) }}
                        </span>
                    </x-table-td>
                    <x-table-td class="text-right">
                        <span class="text-[14px] font-black text-slate-900">{{ number_format($stock->current_stock, 1) }}</span>
                        <span class="text-[10px] font-bold text-slate-400 ml-1 uppercase">{{ $stock->material->unit }}</span>
                    </x-table-td>
                    <x-table-td class="text-center">
                        @php
                            $threshold = $stock->material->min_stock_threshold ?? 5;
                            $status = 'Aman';
                            $color = 'bg-emerald-50 text-emerald-700 border-emerald-100';

                            if ($stock->current_stock <= 0) {
                                $status = 'Habis';
                                $color = 'bg-rose-50 text-rose-700 border-rose-100';
                            } elseif ($stock->current_stock <= $threshold) {
                                $status = 'Rendah';
                                $color = 'bg-amber-50 text-amber-700 border-amber-100';
                            }
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $color }} whitespace-nowrap">
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
