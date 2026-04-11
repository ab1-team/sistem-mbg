<x-app-layout title="Detail Rencana Menu">
    <x-container>
        <x-page-header 
            title="{{ $menuPeriod->title }}" 
            subtitle="{{ $menuPeriod->period->name }} ({{ $menuPeriod->period->start_date->format('d M') }} - {{ $menuPeriod->period->end_date->format('d M') }})"
            :back="route('menu-periods.index')"
        >
            <x-slot name="actions">
                <livewire:menu-period-actions :menuPeriod="$menuPeriod" />
            </x-slot>
            <div class="flex items-center gap-3 mt-2">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">{{ $menuPeriod->dapur->name }}</span>
            </div>
        </x-page-header>

        @if($menuPeriod->status === \App\Models\MenuPeriod::STATUS_REJECTED && $menuPeriod->rejection_note)
            <div class="mb-8 p-6 bg-red-50 border border-red-100 rounded-[32px] flex items-start gap-4 ring-4 ring-red-500/5">
                <div class="w-10 h-10 rounded-2xl bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-[15px] font-black text-red-900 tracking-tight leading-none mb-1">Rencana Ditolak</h4>
                    <p class="text-[13px] text-red-700/80 font-medium leading-relaxed italic">"{{ $menuPeriod->rejection_note }}"</p>
                    <p class="text-[11px] text-red-400 mt-2 font-bold uppercase tracking-widest">— Silakan lakukan perbaikan dan ajukan kembali.</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            {{-- LEFT: CALENDAR VIEW --}}
            <div class="xl:col-span-2 space-y-6">
                <x-card title="Jadwal Penyajian" subtitle="Daftar makanan yang dijadwalkan pada masing-masing tanggal.">
                    <div class="space-y-8">
                        @php
                            $groupedSchedules = $menuPeriod->schedules->groupBy(function($s) {
                                return $s->serve_date->format('Y-m-d');
                            });
                        @endphp

                        @foreach($groupedSchedules as $date => $daySchedules)
                            <div class="relative pl-8 border-l-2 border-slate-100 pb-2 last:pb-0">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 border-green-500 shadow-sm"></div>
                                <div class="mb-4">
                                    <h4 class="text-[15px] font-black text-slate-900 tracking-tight">{{ Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</h4>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    @foreach($daySchedules->sortBy('meal_type') as $s)
                                        <div class="bg-slate-50/50 rounded-2xl p-3 border border-slate-100/50 group hover:border-green-200 transition-colors">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">{{ str_replace('_', ' ', $s->meal_type) }}</p>
                                            <div class="space-y-0.5">
                                                @forelse($s->items as $item)
                                                    <p class="text-[13px] font-bold text-slate-900 leading-tight">{{ $item->name }}</p>
                                                @empty
                                                    <p class="text-[13px] font-bold text-slate-300 leading-tight italic">Belum dipilih</p>
                                                @endforelse
                                            </div>
                                            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                                                <span class="text-[11px] font-bold text-slate-500">{{ $s->target_portions }} <span class="text-[9px] text-slate-400">PORSI</span></span>
                                                <span class="text-[11px] font-mono font-bold text-green-700">
                                                    {{ number_format($s->items->sum('calories') * $s->target_portions / 1000, 1) }} 
                                                    <span class="text-[9px]">Mcal</span>
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            {{-- RIGHT: MATERIAL SUMMARY --}}
            <div class="xl:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <x-card title="Kebutuhan Logistik" subtitle="Total agregasi bahan baku untuk menjalankan rencana ini.">
                        @php
                            $requirements = [];
                            $menuPeriod->loadMissing('schedules.items.boms.material');

                            foreach($menuPeriod->schedules as $s) {
                                foreach($s->items as $item) {
                                    foreach($item->boms as $bom) {
                                        $materialId = $bom->material_id;
                                        $needed = (float) $bom->quantity_per_portion * $s->target_portions;
                                        
                                        if(!isset($requirements[$materialId])) {
                                            $requirements[$materialId] = [
                                                'name' => $bom->material->name,
                                                'unit' => $bom->material->unit,
                                                'total' => 0
                                            ];
                                        }
                                        $requirements[$materialId]['total'] += $needed;
                                    }
                                }
                            }
                        @endphp

                        <div class="space-y-3">
                            @forelse(collect($requirements)->sortBy('name') as $req)
                                <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 group">
                                    <div class="flex-1">
                                        <p class="text-[13px] font-bold text-slate-700 leading-none group-hover:text-green-700 transition-colors">{{ $req['name'] }}</p>
                                    </div>
                                    <div class="flex items-baseline gap-1.5 text-right">
                                        <span class="text-[16px] font-black text-slate-900 tracking-tight">{{ number_format($req['total'], 2) }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $req['unit'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-10 text-center text-slate-400 text-[13px]">Jadwalkan menu untuk melihat kebutuhan bahan.</div>
                            @endforelse
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[11px] text-slate-400 leading-relaxed italic">Catatan: Kalkulasi didasarkan pada resep (BOM) standar yang terdaftar di sistem.</p>
                        </div>
                    </x-card>

                    {{-- ENERGY SUMMARY --}}
                    <div class="bg-green-900 rounded-[28px] p-6 text-white overflow-hidden relative group">
                        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full group-hover:scale-125 transition-transform"></div>
                        <div class="relative z-10">
                            <h4 class="text-[16px] font-bold mb-4 tracking-tight">Total Gizi Periode</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-green-300 tracking-widest">Energi</p>
                                    <p class="text-xl font-black">{{ number_format($menuPeriod->schedules->sum(fn($s) => $s->items->sum('calories') * $s->target_portions) / 1000, 0) }}k <span class="text-[10px]">kcal</span></p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-green-300 tracking-widest">Protein</p>
                                    <p class="text-xl font-black">{{ number_format($menuPeriod->schedules->sum(fn($s) => $s->items->sum('protein') * $s->target_portions) / 1000, 1) }}k <span class="text-[10px]">g</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($menuPeriod->status === \App\Models\MenuPeriod::STATUS_APPROVED)
            <div class="mt-8">
                @php
                    $hasPo = \App\Models\PurchaseOrder::where('menu_period_id', $menuPeriod->id)->first();
                @endphp

                @if(!$hasPo)
                    <div class="p-8 rounded-[32px] bg-slate-900 text-white border border-slate-800 shadow-2xl relative overflow-hidden group">
                        <div class="absolute -right-24 -top-24 w-64 h-64 bg-green-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="max-w-2xl">
                                <h4 class="text-[20px] font-black mb-2 tracking-tight flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                    Kirim ke Logistik
                                </h4>
                                <p class="text-[14px] text-slate-400 leading-relaxed">Rancangan menu telah disetujui. Langkah terakhir adalah menerbitkannya sebagai **Purchase Order (PO)** untuk diproses oleh tim pengadaan barang.</p>
                            </div>
                            <div class="shrink-0">
                                <form action="{{ route('menu-periods.generate-po', $menuPeriod) }}" method="POST">
                                    @csrf
                                    <x-btn type="submit" size="xl" class="bg-green-500 hover:bg-green-600 text-slate-900 font-black shadow-lg shadow-green-500/20">
                                        BUAT PURCHASE ORDER (PO) SEKARANG
                                    </x-btn>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-6 rounded-[32px] bg-green-50 border border-green-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-green-700 uppercase tracking-widest mb-1">Logistik Sudah Diproses</p>
                                <p class="text-[16px] font-bold text-slate-900 leading-tight">PO: {{ $hasPo->po_number }}</p>
                            </div>
                        </div>
                        <x-btn href="{{ route('purchase-orders.show', $hasPo) }}" variant="secondary" class="bg-white hover:bg-green-100 border-green-200 text-green-700 px-8!">
                            Lihat Detail PO
                        </x-btn>
                    </div>
                @endif
            </div>
        @endif
    </x-container>
</x-app-layout>
