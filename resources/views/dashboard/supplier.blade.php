<x-app-layout title="Supplier Dashboard">
    <x-container>
        <div class="mb-8">
            <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">Dashboard Supplier</h1>
            <p class="text-[13px] text-slate-400 mt-2">Ringkasan aktivitas pesanan dan pengiriman Anda.</p>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/>
                        </svg>
                    </div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pesanan Berjalan</p>
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['pending_orders'] }}</span>
                </div>
            </div>

            <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Penugasan</p>
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['total_orders'] }}</span>
                </div>
            </div>

            <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nilai Transaksi</p>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- RECENT ORDERS --}}
        <x-card title="Pesanan Terbaru" subtitle="Daftar PO terbaru yang membutuhkan perhatian Anda." :padding="false">
            <x-table>
                <x-slot name="thead">
                    <x-table-th>No. PO</x-table-th>
                    <x-table-th>Bahan Baku</x-table-th>
                    <x-table-th class="text-center">Status PO</x-table-th>
                    <x-table-th class="text-right">Aksi</x-table-th>
                </x-slot>

                @forelse($recentOrders as $po)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <x-table-td>
                            <p class="font-bold text-slate-900">{{ $po->po_number }}</p>
                            <p class="text-[11px] text-slate-400">{{ $po->dapur->name }}</p>
                        </x-table-td>
                        <x-table-td>
                            <div class="flex flex-wrap gap-1">
                                @foreach($po->items as $item)
                                    @foreach($item->assignments->where('supplier_id', auth()->user()->supplier_id) as $assignment)
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold">
                                            {{ $item->material->name }}
                                        </span>
                                    @endforeach
                                @endforeach
                            </div>
                        </x-table-td>
                        <x-table-td class="text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $po->status->color() }} uppercase">
                                {{ $po->status->label() }}
                            </span>
                        </x-table-td>
                        <x-table-td class="text-right">
                            <x-btn href="{{ route('supplier.purchase-orders.show', $po) }}" variant="secondary" class="py-1 px-3 text-[11px]">
                                Detail
                            </x-btn>
                        </x-table-td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-empty-state title="Belum ada pesanan" subtitle="Pesanan baru akan muncul di sini setelah diteruskan oleh Yayasan." />
                        </td>
                    </tr>
                @endforelse
            </x-table>
            @if($recentOrders->count() > 0)
                <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/10">
                    <a href="{{ route('supplier.purchase-orders.index') }}" class="text-[12px] font-bold text-slate-500 hover:text-emerald-700 flex items-center justify-center gap-2 transition-colors">
                        Lihat Semua Pesanan
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            @endif
        </x-card>
    </x-container>
</x-app-layout>
