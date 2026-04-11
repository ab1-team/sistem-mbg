<x-app-layout title="Detail Menu">

    <x-container>
        <x-page-header 
            title="{{ $menuItem->name }}" 
            subtitle="Detail resep, komposisi bahan (BOM), dan estimasi biaya per porsi."
            :back="route('menu-items.index')"
            back-label="Daftar Menu"
        >
            <div class="flex items-center gap-2">
                <x-btn href="{{ route('menu-items.edit', $menuItem) }}" variant="secondary">
                    Edit Resep
                </x-btn>
            </div>
        </x-page-header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Nutrition Cards --}}
            <div class="md:col-span-2 space-y-6">
                <x-card title="Informasi Nilai Gizi (Per Porsi)">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        <div class="p-4 rounded-2xl bg-orange-50 border border-orange-100 text-center">
                            <p class="text-[10px] font-bold text-orange-400 uppercase tracking-wider mb-1">Kalori</p>
                            <p class="text-xl font-bold text-orange-700">{{ number_format($menuItem->calories, 1) }}</p>
                            <p class="text-[9px] font-medium text-orange-400">Kcal</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 text-center">
                            <p class="text-[10px] font-bold text-blue-400 uppercase tracking-wider mb-1">Protein</p>
                            <p class="text-xl font-bold text-blue-700">{{ number_format($menuItem->protein, 1) }}</p>
                            <p class="text-[9px] font-medium text-blue-400">Gram</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-green-50 border border-green-100 text-center">
                            <p class="text-[10px] font-bold text-green-400 uppercase tracking-wider mb-1">Karbo</p>
                            <p class="text-xl font-bold text-green-700">{{ number_format($menuItem->carbs, 1) }}</p>
                            <p class="text-[9px] font-medium text-green-400">Gram</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-yellow-50 border border-yellow-100 text-center">
                            <p class="text-[10px] font-bold text-yellow-500 uppercase tracking-wider mb-1">Lemak</p>
                            <p class="text-xl font-bold text-yellow-700">{{ number_format($menuItem->fat, 1) }}</p>
                            <p class="text-[9px] font-medium text-yellow-500">Gram</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Serat</p>
                            <p class="text-xl font-bold text-slate-700">{{ number_format($menuItem->fiber, 1) }}</p>
                            <p class="text-[9px] font-medium text-slate-400">Gram</p>
                        </div>
                    </div>
                </x-card>

                <x-card title="Daftar Bahan Baku (BOM)">
                    <x-table>
                        <x-slot name="thead">
                            <x-table-th>Nama Bahan</x-table-th>
                            <x-table-th class="text-center">Kuantitas</x-table-th>
                            <x-table-th class="text-right">Harga Satuan</x-table-th>
                            <x-table-th class="text-right">Subtotal</x-table-th>
                        </x-slot>

                        @php $totalCost = 0; @endphp
                        @foreach($menuItem->boms as $bom)
                            @php 
                                $subtotal = $bom->quantity_per_portion * $bom->material->price_estimate; 
                                $totalCost += $subtotal;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <x-table-td>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 leading-none">{{ $bom->material->name }}</span>
                                        <span class="text-[10px] text-slate-400 mt-1 uppercase">{{ $bom->material->category }}</span>
                                    </div>
                                </x-table-td>
                                <x-table-td class="text-center">
                                    <span class="font-mono text-slate-600 tracking-tighter">{{ $bom->quantity_per_portion }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase ml-1">{{ $bom->unit }}</span>
                                </x-table-td>
                                <x-table-td class="text-right text-slate-500 text-[12px]">
                                    Rp {{ number_format($bom->material->price_estimate, 0, ',', '.') }}
                                </x-table-td>
                                <x-table-td class="text-right font-bold text-slate-700">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </x-table-td>
                            </tr>
                        @endforeach

                        <x-slot name="footer">
                            <div class="px-6 py-4 bg-slate-50/80 flex items-center justify-between border-t border-slate-100">
                                <span class="text-[13px] font-bold text-slate-900 uppercase tracking-wide">Total Estimasi Biaya Per Porsi</span>
                                <span class="text-lg font-black text-green-700">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                            </div>
                        </x-slot>
                    </x-table>
                </x-card>
            </div>

            {{-- Sidebar Info --}}
            <div class="space-y-6">
                <x-card title="Detail Masakan">
                    <div class="space-y-5">
                        <x-show-field label="Tipe Makan" value="{{ strtoupper($menuItem->meal_type) }}" />
                        <x-show-field label="Porsi Standard" value="{{ $menuItem->portion_size }} Porsi" />
                        <x-show-field label="Dibuat Oleh" value="{{ $menuItem->creator->name ?? 'System' }}" />
                        <x-show-field label="Terakhir Diupdate" value="{{ $menuItem->updated_at->diffForHumans() }}" />
                    </div>
                </x-card>

                @if($menuItem->description)
                    <x-card title="Deskripsi">
                        <p class="text-[13px] text-slate-600 leading-relaxed">{{ $menuItem->description }}</p>
                    </x-card>
                @endif
            </div>
        </div>
    </x-container>

</x-app-layout>
