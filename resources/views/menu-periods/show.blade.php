<x-app-layout title="Detail Rencana Menu">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('menu-periods.index') }}" class="inline-flex items-center text-[13px] font-bold text-slate-400 hover:text-green-700 transition-colors mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar
            </a>
            <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">{{ $menuPeriod->title }}</h1>
            <div class="flex items-center gap-3 mt-2">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">{{ $menuPeriod->dapur->name }}</span>
                <span class="text-slate-300">•</span>
                <span class="text-[13px] text-slate-500 font-medium">{{ $menuPeriod->period->name }} ({{ $menuPeriod->period->start_date->format('d M') }} - {{ $menuPeriod->period->end_date->format('d M') }})</span>
            </div>
        </div>
        <livewire:menu-period-actions :menuPeriod="$menuPeriod" />
    </div>

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
                                        <p class="text-[13px] font-bold text-slate-900 leading-tight">{{ $s->menuItem->name }}</p>
                                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                                            <span class="text-[11px] font-bold text-slate-500">{{ $s->target_portions }} <span class="text-[9px] text-slate-400">PORSI</span></span>
                                            <span class="text-[11px] font-mono font-bold text-green-700">{{ number_format($s->menuItem->calories * $s->target_portions / 1000, 1) }} <span class="text-[9px]">Mcal</span></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

            @if($menuPeriod->status === \App\Models\MenuPeriod::STATUS_APPROVED)
                <div class="mt-6 flex flex-col gap-4">
                    @php
                        $hasPo = \App\Models\PurchaseOrder::where('menu_period_id', $menuPeriod->id)->first();
                    @endphp

                    @if(!$hasPo)
                        <div class="p-6 rounded-[28px] bg-slate-900 text-white border border-slate-800 shadow-xl relative overflow-hidden group">
                            <div class="absolute -right-12 -top-12 w-32 h-32 bg-white/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="relative z-10">
                                <h4 class="text-[16px] font-bold mb-2 tracking-tight">Kirim ke Logistik</h4>
                                <p class="text-[12px] text-slate-400 mb-6 leading-relaxed">Rencana ini siap diproses menjadi Purchase Order (PO). Tim logistik akan menerima draf belanja otomatis.</p>
                                <form action="{{ route('menu-periods.generate-po', $menuPeriod) }}" method="POST">
                                    @csrf
                                    <x-btn type="submit" class="w-full bg-green-500 hover:bg-green-600 text-slate-900 font-black shadow-lg shadow-green-500/20 py-4! text-[13px]!">
                                        BUAT PURCHASE ORDER (PO)
                                    </x-btn>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="p-6 rounded-[28px] bg-green-50 border border-green-100 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-black text-green-700 uppercase tracking-widest mb-1">Status PO</p>
                                <p class="text-[14px] font-bold text-slate-900 leading-tight">PO: {{ $hasPo->po_number }}</p>
                            </div>
                            <x-btn href="{{ route('purchase-orders.show', $hasPo) }}" variant="secondary" class="bg-white hover:bg-green-100 border-green-200 text-green-700">
                                Lihat PO
                            </x-btn>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- RIGHT: MATERIAL SUMMARY --}}
        <div class="space-y-6">
            <x-card title="Kebutuhan Logistik" subtitle="Total agregasi bahan baku untuk menjalankan rencana ini.">
                @php
                    $requirements = [];
                    foreach($menuPeriod->schedules as $s) {
                        foreach($s->menuItem->boms as $bom) {
                            $materialId = $bom->material_id;
                            $needed = ($bom->quantity / 1) * $s->target_portions; // quantity is per 1 portion (already normalized)
                            
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
                @endphp

                <div class="space-y-3">
                    @forelse(collect($requirements)->sortBy('name') as $req)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div>
                                <p class="text-[12px] font-bold text-slate-800 leading-none">{{ $req['name'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[13px] font-black text-slate-900 tracking-tight leading-none">{{ number_format($req['total'], 2) }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ $req['unit'] }}</p>
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
                            <p class="text-xl font-black">{{ number_format($menuPeriod->schedules->sum(fn($s) => $s->menuItem->calories * $s->target_portions) / 1000, 0) }}k <span class="text-[10px]">kcal</span></p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-green-300 tracking-widest">Protein</p>
                            <p class="text-xl font-black">{{ number_format($menuPeriod->schedules->sum(fn($s) => $s->menuItem->protein * $s->target_portions) / 1000, 1) }}k <span class="text-[10px]">g</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
