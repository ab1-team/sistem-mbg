<div class="animate-reveal">
    <x-page-header title="Manajemen Bagi Hasil"
        subtitle="Pantau rincian laba, pembagian yayasan, dan distribusi dividen investor secara transparan.">
    </x-page-header>

    {{-- Dashboard Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card Laba Bersih --}}
        <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Laba Bersih</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-[14px] font-bold text-slate-400">Rp</span>
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ number_format($calculations->sum('net_profit'), 0, ',', '.') }}</span>
                </div>
                <p class="text-[10px] text-emerald-600 font-bold mt-2 flex items-center gap-1 uppercase tracking-tighter">
                    Terakumulasi dari semua periode
                </p>
            </div>
        </div>

        {{-- Card Yayasan Share --}}
        <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Bagian Yayasan ({{ \App\Models\Setting::get('profit_share_yayasan', 20) }}%)</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-[14px] font-bold text-blue-400">Rp</span>
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ number_format($calculations->sum('yayasan_share'), 0, ',', '.') }}</span>
                </div>
                <div class="mt-4">
                    <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 w-[{{ \App\Models\Setting::get('profit_share_yayasan', 20) }}%]"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Investor Share --}}
        <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pool Investor ({{ \App\Models\Setting::get('profit_share_investor', 80) }}%)</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-[14px] font-bold text-orange-400">Rp</span>
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ number_format($calculations->sum('investor_total_share'), 0, ',', '.') }}</span>
                </div>
                <div class="mt-4">
                    <div class="h-1.5 bg-slate-50 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500 w-[{{ \App\Models\Setting::get('profit_share_investor', 80) }}%]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <x-card :padding="false" class="overflow-hidden mb-20">
        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
            <h4 class="text-[14px] font-bold text-slate-900 tracking-tight">Riwayat Kalkulasi Bagi Hasil</h4>
            <div class="w-72">
                <x-form-input wire:model.live="search" placeholder="Cari periode atau dapur...">
                    <x-slot name="icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </x-slot>
                </x-form-input>
            </div>
        </div>

        <x-table>
            <x-slot name="thead">
                <x-table-th>Periode</x-table-th>
                <x-table-th>Dapur</x-table-th>
                <x-table-th>Laba Bersih</x-table-th>
                <x-table-th>Bagi Yayasan</x-table-th>
                <x-table-th>Pool Investor</x-table-th>
                <x-table-th class="text-center">Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($calculations as $calc)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <span class="font-bold text-[13px] text-slate-900">{{ $calc->period->name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-500 font-medium text-[12px]">{{ $calc->dapur->name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-900 font-bold text-[13px]">Rp{{ number_format($calc->net_profit, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-emerald-700 font-bold text-[13px]">Rp{{ number_format($calc->yayasan_share, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-900 font-bold text-[13px]">Rp{{ number_format($calc->investor_total_share, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td class="text-center">
                        <x-badge variant="success">{{ $calc->status }}</x-badge>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-6">
                        <x-btn wire:click="showDetail({{ $calc->id }})" variant="secondary" class="py-1.5! px-3! text-[11px]!">Detail Distribusi</x-btn>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state title="Belum ada data bagi hasil" subtitle="Data muncul setelah periode dikunci oleh tim Finance." />
                    </td>
                </tr>
            @endforelse
        </x-table>
        
        <div class="px-6 py-4 border-t border-slate-50">
            {{ $calculations->links() }}
        </div>
    </x-card>

    {{-- Detail Modal --}}
    <x-dialog name="calculation-detail" title="Rincian Distribusi Bagi Hasil" maxWidth="lg">
        @if($selectedCalculation)
            <div class="space-y-6">
                {{-- Meta Info --}}
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <div>
                        <h4 class="text-[15px] font-bold text-slate-900 tracking-tight">{{ $selectedCalculation->period->name }}</h4>
                        <p class="text-[12px] text-slate-500 font-medium">Dapur: {{ $selectedCalculation->dapur->name }}</p>
                    </div>
                </div>

                {{-- Distribution List --}}
                <div class="overflow-hidden border border-slate-100 rounded-2xl shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Investor</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Saham</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Dividen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($selectedCalculation->distributions as $dist)
                                <tr>
                                    <td class="px-4 py-3.5 text-[13px] font-bold text-slate-900">{{ $dist->investor->name }}</td>
                                    <td class="px-4 py-3.5 text-[12px] font-semibold text-slate-500">{{ number_format($dist->share_percentage, 1) }}%</td>
                                    <td class="px-4 py-3.5 text-right text-[13px] font-bold text-emerald-700">Rp{{ number_format($dist->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="p-5 bg-emerald-600 rounded-2xl text-white flex items-center justify-between shadow-lg">
                    <div>
                        <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest mb-1 opacity-80">Total Distribusi Investor</p>
                        <p class="text-[20px] font-black tracking-tight">Rp{{ number_format($selectedCalculation->investor_total_share, 0, ',', '.') }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" /></svg>
                </div>
            </div>
        @endif
    </x-dialog>
</div>
