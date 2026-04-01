<div class="space-y-8 pb-32">
    @if($errors->any())
        <x-alert variant="danger" title="Terdapat Kesalahan Input">
            Beberapa data jadwal belum valid atau tidak lengkap. Periksa baris jadwal yang berwarna merah.
        </x-alert>
    @endif
    {{-- BASIC INFO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card title="Informasi Dasar" subtitle="Tentukan target dapur dan periode perencanaan.">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <x-form-input label="Judul Perencanaan" wire:model="title" id="title" name="title" placeholder="Contoh: Rencana Januari 2024 - Dapur A" required />
                    </div>
                    <div>
                        <x-form-searchable-select 
                            label="Unit Dapur" 
                            name="dapur_id" 
                            wire:model="dapur_id" 
                            :selected="$dapur_id"
                            :options="$dapurs->map(fn($d) => ['value' => (string)$d->id, 'label' => $d->name])"
                            placeholder="Pilih Dapur"
                            required 
                        />
                    </div>
                    <div>
                        <x-form-searchable-select 
                            label="Periode" 
                            name="period_id" 
                            wire:model.live="period_id" 
                            :selected="$period_id"
                            :options="$periods->map(fn($p) => ['value' => (string)$p->id, 'label' => $p->name . ' (' . $p->start_date->format('d M') . ' - ' . $p->end_date->format('d M') . ')'])"
                            placeholder="Pilih Periode"
                            required 
                        />
                    </div>

                </div>
            </x-card>
        </div>
        <div class="lg:col-span-1">
            <div class="bg-emerald-900 rounded-[24px] p-6 text-white relative overflow-hidden h-full flex flex-col justify-center">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full"></div>
                <div class="relative z-10">
                    <h4 class="text-[18px] font-black leading-tight mb-2 tracking-tight">Tips Perencanaan</h4>
                    <p class="text-[12px] text-emerald-100 opacity-80 leading-relaxed mb-4">Pilih periode terlebih dahulu untuk memunculkan daftar tanggal penjadwalan secara otomatis.</p>
                    <div class="flex items-center gap-2 text-[11px] font-bold text-emerald-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/>
                        </svg>
                        Simpan sebagai Draf sebelum diajukan.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCHEDULING GRID --}}
    @if(!empty($schedules))
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-[18px] font-black text-slate-900 tracking-tight">Jadwal Makanan Harian</h3>
                <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[11px] font-bold rounded-lg">{{ count($schedules) }} Hari Terdeteksi</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($schedules as $date => $day)
                    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-5 hover:border-green-200 transition-colors">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-50">
                            <span class="text-[14px] font-black text-slate-900 tracking-tight">{{ $day['display'] }}</span>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest font-mono">{{ $date }}</span>
                        </div>

                        <div class="space-y-4">
                            @foreach($day['meals'] as $mIdx => $meal)
                                <div class="space-y-1.5 p-3 rounded-2xl bg-slate-50/50">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ str_replace('_', ' ', $meal['type']) }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="flex-1 min-w-0 relative" 
                                            x-data="{ 
                                                open: false, 
                                                search: '', 
                                                selectedIds: @entangle('schedules.'.$date.'.meals.'.$mIdx.'.menu_item_ids') || [],
                                                menuLookup: @js($menuItems->keyBy('id')),
                                                toggle(id) {
                                                    const idInt = parseInt(id);
                                                    if (!Array.isArray(this.selectedIds)) this.selectedIds = [];
                                                    if (this.selectedIds.includes(idInt)) {
                                                        this.selectedIds = this.selectedIds.filter(i => i != idInt);
                                                    } else {
                                                        this.selectedIds.push(idInt);
                                                    }
                                                }
                                            }">
                                            <div @click="open = !open" 
                                                class="w-full min-h-[38px] p-1.5 border {{ $errors->has('schedules.'.$date.'.meals.'.$mIdx.'.menu_item_ids') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200' }} rounded-xl bg-white flex flex-wrap gap-1.5 cursor-pointer hover:border-green-500 transition-all group items-center">
                                                
                                                <template x-if="!selectedIds || selectedIds.length === 0">
                                                    <span class="text-[12px] text-slate-400 px-2 py-1 font-bold">Pilih Menu...</span>
                                                </template>

                                                <template x-for="id in (selectedIds || [])" :key="id">
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-black rounded-lg border border-slate-200 group-hover:bg-green-50 group-hover:text-green-700 group-hover:border-green-100 transition-colors">
                                                        <span x-text="menuLookup[id]?.name || 'Menu'"></span>
                                                        <button type="button" @click.stop="toggle(id)" class="hover:text-red-500">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </span>
                                                </template>

                                                <div class="ml-auto pr-1 flex items-center">
                                                    <svg class="w-3 h-3 text-slate-300 group-hover:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                                </div>
                                            </div>

                                            {{-- Dropdown --}}
                                            <div x-show="open" 
                                                @click.away="open = false" 
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                class="absolute z-50 left-0 right-0 mt-2 min-w-[200px] max-h-60 overflow-y-auto bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 space-y-1 focus:outline-none">
                                                <div class="sticky top-0 bg-white pb-2 px-1 pt-1">
                                                    <input x-model="search" type="text" placeholder="Cari menu..." 
                                                        class="w-full text-[11px] border-slate-100 rounded-xl focus:ring-green-500/20 focus:border-green-500 font-bold placeholder:text-slate-300 p-2" />
                                                </div>
                                                <div class="space-y-0.5">
                                                    @foreach($menuItems as $item)
                                                        <label x-show="'{{ strtolower($item->name) }}'.includes(search.toLowerCase())" 
                                                            class="flex items-center gap-2.5 p-2 hover:bg-slate-50 rounded-xl cursor-pointer transition-colors">
                                                            <input type="checkbox" 
                                                                :checked="(selectedIds || []).includes(parseInt('{{ $item->id }}'))"
                                                                @change="toggle('{{ $item->id }}')"
                                                                class="w-4 h-4 rounded-lg border-slate-300 text-green-600 focus:ring-green-500/20 transition-all">
                                                            <span class="text-[12px] font-black text-slate-700 leading-none">{{ $item->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-16">
                                            <input type="number" wire:model="schedules.{{ $date }}.meals.{{ $mIdx }}.portions" 
                                                placeholder="Qty"
                                                class="w-full text-center text-[12px] {{ $errors->has('schedules.'.$date.'.meals.'.$mIdx.'.portions') ? 'border-red-500 ring-1 ring-red-500/20' : 'border-slate-200' }} rounded-xl focus:ring-green-500/20 focus:border-green-500 transition-all bg-white text-slate-700 h-[38px] px-1 font-mono font-bold" />
                                        </div>
                                    </div>
                                    @error('schedules.'.$date.'.meals.'.$mIdx.'.menu_item_ids') <p class="text-[9px] text-red-500 font-bold mt-1">Minimal satu menu harus dipilih jika porsi diisi</p> @enderror
                                    @error('schedules.'.$date.'.meals.'.$mIdx.'.portions') <p class="text-[9px] text-red-500 font-bold mt-1">Porsi tidak boleh kosong</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="py-20 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h4 class="text-[16px] font-bold text-slate-400">Pilih periode untuk memunculkan jadwal harian</h4>
            <p class="text-[12px] text-slate-400 mt-1 max-w-[300px]">Jadwal akan digenerate otomatis berdasarkan rentang tanggal periode tersebut.</p>
        </div>
    @endif

    {{-- STICKY FOOTER --}}
    <div class="fixed bottom-0 left-0 right-0 lg:left-64 bg-white/80 backdrop-blur-lg border-t border-slate-100 p-4 z-40">
        <div class="max-w-[1200px] mx-auto flex items-center justify-between">
            <div class="hidden sm:block">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status Rencana</p>
                <p class="text-[13px] font-black text-slate-900 tracking-tight">Draf Perencanaan</p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <x-btn href="{{ route('menu-periods.index') }}" variant="secondary" class="flex-1 sm:flex-none">Batal</x-btn>
                <x-btn type="button" 
                    wire:click="save" 
                    loading="true" 
                    loading-target="save" 
                    loading-text="Menyimpan..."
                    class="flex-1 sm:flex-none shadow-lg shadow-green-900/20">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perencanaan
                </x-btn>
            </div>
        </div>
    </div>
</div>
