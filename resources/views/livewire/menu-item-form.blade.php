<div>
    @php
        $materialOptions = collect($allMaterials)
            ->map(function ($mat) {
                return [
                    'value' => (string) $mat->id,
                    'label' => $mat->name . ' (' . $mat->code . ')',
                    'unit' => (string) $mat->unit,
                ];
            })
            ->toArray();
    @endphp
    <form wire:submit="save">

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex flex-col gap-2 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <span
                        class="font-bold text-sm">{{ session('error') ?? 'Mohon periksa kembali inputan Anda:' }}</span>
                </div>
                @if ($errors->any())
                    <ul class="text-xs ml-8 list-disc list-inside opacity-80">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        {{-- General Information --}}
        <x-card title="Informasi Dasar">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1">
                        <x-form-input label="Nama Masakan" wire:model="name" placeholder="Contoh: Ayam Goreng Mentega"
                            required />
                        @error('name')
                            <span class="text-xs text-red-500 font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                    <x-form-searchable-select label="Tipe Makan" wire:model="meal_type" required :options="[
                        ['value' => 'anak_anak', 'label' => 'Anak-anak'],
                        ['value' => 'dewasa', 'label' => 'Dewasa'],
                    ]" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @if (count($dapurs) > 1)
                        <x-form-searchable-select label="Dapur (Opsional)" wire:model.live="dapur_id" :options="collect($dapurs)
                            ->map(fn($d) => ['value' => (string) $d->id, 'label' => $d->name])
                            ->prepend(['value' => '', 'label' => 'Global (Semua Dapur)'])
                            ->toArray()"
                            hint="Pilih jika masakan ini hanya untuk dapur tertentu. Bahan baku akan difilter sesuai dapur ini." />
                    @else
                        <input type="hidden" wire:model="dapur_id">
                    @endif
                </div>

                <x-form-textarea label="Deskripsi masakan" wire:model="description"
                    placeholder="Ceritakan singkat tentang masakan ini..." />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <x-form-input label="Porsi Standard" type="number" wire:model="portion_size" required
                        hint="Basis porsi resep ini." />
                </div>
            </div>
        </x-card>

        {{-- BOM Details --}}
        <x-card title="Bahan Baku (BOM) per 1 Porsi" class="mt-4">
            @php
                $materialOptions = collect($allMaterials)
                    ->map(function ($mat) {
                        return [
                            'value' => (string) $mat->id,
                            'label' => $mat->name . ' (' . $mat->code . ')',
                            'unit' => (string) $mat->unit,
                        ];
                    })
                    ->toArray();
            @endphp
            <x-table loading-target="save">
                <x-slot name="thead">
                    <x-table-th>Bahan Baku</x-table-th>
                    <x-table-th class="w-32">Kuantitas</x-table-th>
                    <x-table-th class="w-24 text-center">Satuan</x-table-th>
                    <x-table-th class="w-20"></x-table-th>
                </x-slot>

                @foreach ($rows as $index => $row)
                    @php
                        $rowUnit = $row['unit'] ?? '-';
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors" wire:key="bom-row-{{ $index }}">
                        <x-table-td>
                            <x-form-searchable-select wire:model.live="rows.{{ $index }}.material_id"
                                :options="$materialOptions" placeholder="Pilih Bahan..." />
                            @error('rows.' . $index . '.material_id')
                                <span class="text-[11px] text-red-500 font-bold">{{ $message }}</span>
                            @enderror
                        </x-table-td>
                        <x-table-td>
                            <input type="number" step="0.0001"
                                wire:model.live.debounce.500ms="rows.{{ $index }}.quantity"
                                class="block w-full bg-slate-50 border border-slate-200 text-slate-900 text-[13px] rounded-xl px-4 py-2 focus:bg-white focus:border-green-900 focus:ring-4 focus:ring-green-900/5 transition-all outline-none">
                            @error('rows.' . $index . '.quantity')
                                <span class="text-[11px] text-red-500 font-bold">{{ $message }}</span>
                            @enderror
                        </x-table-td>
                        <x-table-td class="text-center font-bold text-slate-400 text-[11px] uppercase tracking-widest">
                            <span>{{ $rowUnit }}</span>
                        </x-table-td>
                        <x-table-td class="text-right">
                            @if (count($rows) > 1)
                                <button type="button" wire:click="removeRow({{ $index }})"
                                    class="text-red-400 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </x-table-td>
                    </tr>
                @endforeach
            </x-table>

            <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/20">
                <button type="button" wire:click.prevent="addRow" wire:loading.attr="disabled" wire:target="addRow"
                    wire:key="btn-add-row"
                    class="flex items-center gap-2 text-green-700 font-bold text-[13px] hover:text-green-800 transition-colors disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    <span>Tambah Bahan Baku</span>
                    <div wire:loading wire:target="addRow"
                        class="w-3 h-3 border-2 border-green-700/20 border-t-green-700 rounded-full animate-spin"></div>
                </button>
            </div>

            @error('rows')
                <div class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</div>
            @enderror
        </x-card>

        {{-- Nutritional Info --}}
        <x-card title="Informasi Gizi per 1 Porsi" class="mt-4">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-5">
                <x-form-input label="Kalori (Kcal)" type="number" step="0.01" wire:model="calories" readonly
                    class="opacity-75 bg-slate-100 cursor-not-allowed" />
                <x-form-input label="Protein (g)" type="number" step="0.01" wire:model="protein" readonly
                    class="opacity-75 bg-slate-100 cursor-not-allowed" />
                <x-form-input label="Karbo (g)" type="number" step="0.01" wire:model="carbs" readonly
                    class="opacity-75 bg-slate-100 cursor-not-allowed" />
                <x-form-input label="Lemak (g)" type="number" step="0.01" wire:model="fat" readonly
                    class="opacity-75 bg-slate-100 cursor-not-allowed" />
                <x-form-input label="Serat (g)" type="number" step="0.01" wire:model="fiber" readonly
                    class="opacity-75 bg-slate-100 cursor-not-allowed" />
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 pb-20">
            <x-btn href="{{ route('menu-items.index') }}" variant="secondary">Batal</x-btn>
            <x-btn type="submit" loading="true" loading-target="save" loading-text="Menyimpan..."
                class="px-6 py-2.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                    viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" />
                </svg>
                Simpan Masakan
            </x-btn>
        </div>
    </form>
</div>
