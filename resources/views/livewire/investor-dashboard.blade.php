<x-container class="space-y-8 pb-20">
    @if(isset($error))
        <x-alert variant="danger" title="Akses Ditolak">{{ $error }}</x-alert>
    @else
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <x-page-header title="Dashboard Investasi" subtitle="Pantau performa unit dan bagi hasil Anda secara real-time." />
            
            <div class="flex items-center gap-3 mb-2">
                <x-btn href="{{ route('investor.withdrawals.create') }}" class="shadow-lg shadow-green-900/10">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tarik Dana (Withdrawal)
                </x-btn>
            </div>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Wallet Balance --}}
            <div class="bg-emerald-900 rounded-[32px] p-7 text-white relative overflow-hidden group shadow-2xl shadow-emerald-900/20">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full group-hover:scale-125 transition-transform duration-500"></div>
                
                <div class="relative z-10 flex flex-col h-full">
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-300 opacity-80 mb-1">Saldo Tersedia</p>
                    <h3 class="text-[32px] font-black leading-tight mb-6 flex items-baseline gap-1">
                        <span class="text-emerald-400 text-[16px] font-bold">Rp</span>
                        {{ number_format($wallet->balance ?? 0, 0, ',', '.') }}
                    </h3>
                    
                    <div class="mt-auto pt-4 border-t border-white/10 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-200">Share: {{ number_format($investor->share_percentage, 2) }}%</span>
                        <div class="px-2 py-0.5 bg-emerald-800 rounded-lg text-[10px] font-black uppercase tracking-widest text-emerald-300">Aktif</div>
                    </div>
                </div>
            </div>

            {{-- Total Earned --}}
            <x-card class="group">
                <div class="flex flex-col">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Total Akumulasi</p>
                            <span class="text-[20px] font-black text-slate-900 leading-none">Rp {{ number_format($totalEarned, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Seluruh keuntungan yang pernah didistribusikan ke akun Anda sejak bergabung.</p>
                </div>
            </x-card>

            {{-- Investment Growth --}}
            <x-card class="group">
                <div class="flex flex-col">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Bergabung Sejak</p>
                            <span class="text-[20px] font-black text-slate-900 leading-none">{{ $investor->join_date->format('M Y') }}</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed italic">"Investasi yang berkelanjutan adalah investasi yang bermanfaat bagi umat."</p>
                </div>
            </x-card>
        </div>

        {{-- TABLES SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- RECENT DISTRIBUTIONS --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-[16px] font-black text-slate-900 tracking-tight">Riwayat Bagi Hasil</h3>
                    <span class="text-[11px] font-bold text-slate-400">10 Transaksi Terakhir</span>
                </div>
                
                <x-card :padding="false" class="overflow-hidden">
                    <x-table>
                        <x-slot name="thead">
                            <x-table-th>Periode</x-table-th>
                            <x-table-th>Dapur</x-table-th>
                            <x-table-th class="text-right">Dividen</x-table-th>
                        </x-slot>

                        @forelse($recentDistributions as $dist)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <x-table-td>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $dist->profitCalculation->period->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $dist->created_at->format('d M H:i') }}</span>
                                    </div>
                                </x-table-td>
                                <x-table-td>
                                    <span class="text-slate-600 font-medium">{{ $dist->profitCalculation->dapur->nama }}</span>
                                </x-table-td>
                                <x-table-td class="text-right">
                                    <span class="font-black text-green-600">+ {{ number_format($dist->amount, 0, ',', '.') }}</span>
                                </x-table-td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <x-empty-state title="Belum ada pembagian" subtitle="Bagi hasil akan muncul setelah periode keuangan dikunci." />
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-card>
            </div>

            {{-- RECENT WITHDRAWALS --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-[16px] font-black text-slate-900 tracking-tight">Status Penarikan</h3>
                    <span class="text-[11px] font-bold text-slate-400">Update Terakhir</span>
                </div>

                <x-card :padding="false" class="overflow-hidden">
                    <x-table>
                        <x-slot name="thead">
                            <x-table-th>Tanggal</x-table-th>
                            <x-table-th class="text-right">Jumlah</x-table-th>
                            <x-table-th class="text-center">Status</x-table-th>
                        </x-slot>

                        @forelse($recentWithdrawals as $wd)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <x-table-td>
                                    <span class="text-slate-500 font-medium">{{ $wd->created_at->format('d/m/Y') }}</span>
                                </x-table-td>
                                <x-table-td class="text-right">
                                    <span class="font-black text-slate-900">{{ number_format($wd->amount, 0, ',', '.') }}</span>
                                </x-table-td>
                                <x-table-td class="text-center">
                                    <x-badge :variant="$wd->status === 'processed' ? 'success' : ($wd->status === 'pending' ? 'warning' : 'danger')">
                                        {{ strtoupper($wd->status) }}
                                    </x-badge>
                                </x-table-td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <x-empty-state title="Belum ada penarikan" subtitle="Klik tombol Tarik Dana untuk mencairkan saldo Anda." />
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-card>
            </div>
        </div>
    @endif
</x-container>
