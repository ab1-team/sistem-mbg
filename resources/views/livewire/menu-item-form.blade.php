<form wire:submit="save" class="space-y-8" x-data="{
    materials: {{ collect($allMaterials)->keyBy('id')->toJson() }},
    rows: @entangle('rows'),
    recalc() {
        let total = { cal: 0, pro: 0, car: 0, fat: 0, fib: 0 };
        (Array.isArray(this.rows) ? this.rows : Object.values(this.rows)).forEach(row => {
            if (row && row.material_id && row.quantity) {
                let m = this.materials[row.material_id];
                if (m) {
                    let q = parseFloat(row.quantity) || 0;
                    total.cal += (parseFloat(m.calories) || 0) * q;
                    total.pro += (parseFloat(m.protein) || 0) * q;
                    total.car += (parseFloat(m.carbs) || 0) * q;
                    total.fat += (parseFloat(m.fat) || 0) * q;
                    total.fib += (parseFloat(m.fiber) || 0) * q;
                }
            }
        });
        this.$wire.calories = total.cal.toFixed(2);
        this.$wire.protein = total.pro.toFixed(2);
        this.$wire.carbs = total.car.toFixed(2);
        this.$wire.fat = total.fat.toFixed(2);
        this.$wire.fiber = total.fib.toFixed(2);
    },
    addRow() {
        this.rows.push({ material_id: '', quantity: 1, unit: '-' });
        this.$nextTick(() => this.recalc());
    },
    removeRow(index) {
        if (this.rows.length > 1) {
            this.rows.splice(index, 1);
            this.$nextTick(() => this.recalc());
        }
    }
}" x-init="recalc();
$watch('rows', () => recalc())">

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
                <span class="font-bold text-sm">{{ session('error') ?? 'Mohon periksa kembali inputan Anda:' }}</span>
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
                    ['value' => 'sarapan', 'label' => 'Sarapan'],
                    ['value' => 'makan_siang', 'label' => 'Makan Siang'],
                    ['value' => 'makan_malam', 'label' => 'Makan Malam'],
                    ['value' => 'snack', 'label' => 'Snack'],
                ]" />
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
    <x-card title="Bahan Baku (BOM) per 1 Porsi">
        <div class="overflow-x-auto relative">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <x-table-th>Bahan Baku</x-table-th>
                        <x-table-th class="w-32">Kuantitas</x-table-th>
                        <x-table-th class="w-24 text-center">Satuan</x-table-th>
                        <x-table-th class="w-20"></x-table-th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="(row, index) in rows" :key="index">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <x-table-td>
                                {{-- Faithful Re-implementation of YOUR Searchable Select Component --}}
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    pos: { top: 0, left: 0, width: 0 },
                                    options: Object.values(materials),
                                
                                    get filteredOptions() {
                                        if (!this.search) return this.options;
                                        let s = this.search.toLowerCase();
                                        return this.options.filter(o =>
                                            (o.name || '').toLowerCase().includes(s) ||
                                            (o.code || '').toLowerCase().includes(s)
                                        );
                                    },
                                    get selectedLabel() {
                                        let m = materials[row.material_id];
                                        return m ? (m.name + ' (' + m.code + ')') : '';
                                    },
                                    updatePos() {
                                        if (!this.$refs.trigger) return;
                                        let rect = this.$refs.trigger.getBoundingClientRect();
                                        this.pos.top = rect.bottom + window.scrollY;
                                        this.pos.left = rect.left + window.scrollX;
                                        this.pos.width = rect.width;
                                    },
                                    toggle() {
                                        this.open = !this.open;
                                        if (this.open) {
                                            this.updatePos();
                                            setTimeout(() => { if (this.$refs.searchInput) this.$refs.searchInput.focus(); }, 100);
                                        }
                                    },
                                    select(m) {
                                        row.material_id = m.id;
                                        row.unit = m.unit;
                                        this.open = false;
                                        this.search = '';
                                        recalc();
                                    }
                                }" @scroll.window="if(open) updatePos()"
                                    @resize.window="if(open) updatePos()">

                                    <div class="relative" x-ref="trigger">
                                        <input type="text" x-model="search" x-ref="searchInput"
                                            @click="if(!open) toggle()" @focus="if(!open) toggle()"
                                            @keydown.escape="open = false"
                                            :placeholder="row.material_id ? selectedLabel : 'Pilih Bahan...'"
                                            class="block w-full bg-slate-50 border border-slate-200 text-slate-900 text-[13px] rounded-xl px-4 py-2.5 
                                                  focus:bg-white focus:border-green-900 focus:ring-4 focus:ring-green-900/5 
                                                  transition-all outline-none placeholder:text-slate-900 placeholder:font-bold"
                                            :class="open ? 'ring-4 ring-green-900/5 border-green-900 bg-white' : ''">

                                        <div @click="toggle()"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2 cursor-pointer text-slate-400 hover:text-green-600 transition-colors">
                                            <svg x-show="!open" class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <svg x-show="open" class="w-4 h-4 animate-in fade-in zoom-in duration-200"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>

                                    <template x-teleport="body">
                                        <div x-show="open" @click.away="open = false"
                                            class="fixed z-[99999] bg-white border border-slate-200 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] overflow-hidden"
                                            :style="`top: ${pos.top}px; left: ${pos.left}px; width: ${pos.width}px;`"
                                            style="display: none;">
                                            <div class="max-h-60 overflow-y-auto p-1.5 custom-scrollbar">
                                                <template x-for="m in filteredOptions" :key="m.id">
                                                    <button type="button" @click="select(m)"
                                                        class="flex items-center w-full px-4 py-2.5 text-left text-[13px] rounded-xl transition-all hover:bg-green-50 group"
                                                        :class="row.material_id == m.id ?
                                                            'bg-green-50 text-green-700 font-extrabold' :
                                                            'text-slate-600 hover:text-slate-900'">
                                                        <div class="flex flex-col flex-1">
                                                            <span x-text="m.name" class="font-bold"></span>
                                                            <span
                                                                class="text-[10px] text-slate-400 uppercase tracking-tighter"
                                                                x-text="m.code"></span>
                                                        </div>
                                                        <template x-if="row.material_id == m.id">
                                                            <svg class="w-4 h-4 text-green-700" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </template>
                                                    </button>
                                                </template>
                                                <div x-show="filteredOptions.length === 0"
                                                    class="p-8 text-center bg-slate-50/50">
                                                    <p class="text-[12px] text-slate-400 font-bold italic">Bahan tidak
                                                        ditemukan...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </x-table-td>

                            <x-table-td>
                                <input type="number" step="0.0001" x-model="row.quantity" @input="recalc()"
                                    class="block w-full bg-slate-50 border border-slate-200 text-slate-900 text-[13px] rounded-xl px-4 py-2.5 focus:bg-white focus:border-green-900 focus:ring-4 focus:ring-green-900/5 transition-all outline-none">
                            </x-table-td>

                            <x-table-td
                                class="text-center font-bold text-slate-400 text-[11px] uppercase tracking-widest">
                                <span x-text="row.unit || '-'"></span>
                            </x-table-td>

                            <x-table-td class="text-right">
                                <button type="button" @click="removeRow(index)"
                                    class="text-red-400 hover:text-red-600 transition-colors p-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </x-table-td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Instant Add Button --}}
            <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/20">
                <button type="button" @click="addRow()"
                    class="flex items-center gap-2 text-green-700 font-bold text-[13px] hover:text-green-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Tambah Bahan Baku
                </button>
            </div>
        </div>

    </x-card>

    {{-- Nutritional Info --}}
    <x-card title="Informasi Gizi per 1 Porsi">
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

    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
        <x-btn href="{{ route('menu-items.index') }}" variant="secondary">Batal</x-btn>
        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-green-900 border border-transparent rounded-xl font-bold text-white hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-900/10 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            <svg wire:loading.remove wire:target="save" class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" />
            </svg>
            <svg wire:loading wire:target="save" class="w-3.5 h-3.5 animate-spin" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span wire:loading.remove wire:target="save">Simpan Masakan</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </div>
</form>
