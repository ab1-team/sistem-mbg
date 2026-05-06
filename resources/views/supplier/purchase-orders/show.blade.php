<x-app-layout title="Detail Pesanan">
    <x-container>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('supplier.purchase-orders.index') }}"
                    class="inline-flex items-center text-[13px] font-bold text-slate-400 hover:text-emerald-700 transition-colors mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">
                        Pesanan: {{ $purchaseOrder->po_number }}
                    </h1>
                    <span
                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $purchaseOrder->status->color() }} uppercase whitespace-nowrap">
                        {{ $purchaseOrder->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <x-card :padding="false" class="overflow-hidden">
                <x-table>
                    <x-slot name="thead">
                        <x-table-th>Bahan Baku</x-table-th>
                        <x-table-th class="text-right">Kuantitas</x-table-th>
                        <x-table-th class="text-right">Harga</x-table-th>
                        <x-table-th class="text-right">Total</x-table-th>
                        <x-table-th class="text-center">Status Item</x-table-th>
                        <x-table-th class="text-right">Aksi</x-table-th>
                    </x-slot>

                    @foreach ($assignments as $assignment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <x-table-td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 leading-none">{{ $assignment->item->material->name }}</p>
                                        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider font-black">ID: #{{ $assignment->id }}</p>
                                    </div>
                                </div>
                            </x-table-td>
                            <x-table-td class="text-right font-mono font-bold text-slate-700">
                                {{ number_format($assignment->quantity_assigned, 2) }} <span class="text-[10px] text-slate-400">{{ $assignment->item->unit }}</span>
                            </x-table-td>
                            <x-table-td class="text-right font-mono text-slate-600 text-[13px]">
                                Rp {{ number_format($assignment->unit_price_agreed, 0, ',', '.') }}
                            </x-table-td>
                            <x-table-td class="text-right font-mono font-black text-slate-900 text-[13px]">
                                Rp {{ number_format($assignment->quantity_assigned * $assignment->unit_price_agreed, 0, ',', '.') }}
                            </x-table-td>
                            <x-table-td class="text-center">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black border uppercase whitespace-nowrap
                                    {{ $assignment->status === 'ditolak' ? 'bg-red-50 text-red-600 border-red-100' : 
                                       ($assignment->status === 'diteruskan' ? 'bg-blue-50 text-blue-600 border-blue-100' : 
                                       ($assignment->status === 'diterima' ? 'bg-green-50 text-green-600 border-green-100' : 
                                       'bg-slate-50 text-slate-600 border-slate-200')) }}">
                                    {{ $assignment->status }}
                                </span>
                            </x-table-td>
                            <x-table-td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Tombol Terima/Tolak --}}
                                    @if ($assignment->status === 'diteruskan' && !in_array($purchaseOrder->status->value, ['dibatalkan', 'selesai']))
                                        <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="accept">
                                            <x-btn type="submit" variant="primary" class="py-1! px-2! text-[10px]! bg-emerald-600!">TERIMA</x-btn>
                                        </form>
                                        
                                        <div x-data="{ open: false }" class="inline">
                                            <x-btn @click="open = true" variant="secondary" class="py-1! px-2! text-[10px]! text-red-600! border-red-100! bg-red-50!">TOLAK</x-btn>
                                            
                                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
                                                <div @click.away="open = false" class="bg-white rounded-[32px] p-8 max-w-md w-full shadow-2xl border border-slate-100 text-left">
                                                    <h3 class="text-[20px] font-black text-slate-900 tracking-tight mb-2">Alasan Penolakan</h3>
                                                    <p class="text-[13px] text-slate-500 font-medium mb-6 leading-relaxed">Alasan tidak dapat memenuhi {{ $assignment->item->material->name }}:</p>
                                                    <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <textarea name="rejection_reason" rows="4" required minlength="5"
                                                            class="w-full text-[13px] border-slate-200 rounded-[24px] focus:ring-red-500/20 focus:border-red-500 transition-all bg-slate-50 p-4 mb-6"></textarea>
                                                        <div class="flex gap-2">
                                                            <x-btn type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white">KIRIM</x-btn>
                                                            <x-btn @click="open = false" variant="secondary" class="flex-1">BATAL</x-btn>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($assignment->status === 'diterima')
                                        <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="process">
                                            <x-btn type="submit" class="py-1! px-2! text-[10px]! bg-amber-600! text-white!">PROSES</x-btn>
                                        </form>
                                    @endif

                                    @if ($assignment->status === 'diproses')
                                        <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="ship">
                                            <x-btn type="submit" class="py-1! px-2! text-[10px]! bg-emerald-600! text-white!">KIRIM</x-btn>
                                        </form>
                                    @endif

                                    {{-- Link detail riwayat atau tooltip jika perlu --}}
                                </div>
                            </x-table-td>
                        </tr>
                    @endforeach
                </x-table>
            </x-card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-card title="Tujuan Pengiriman" subtitle="Lokasi dapur penerima bahan.">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-900">{{ $purchaseOrder->dapur->name }}</p>
                            <p class="text-[13px] text-slate-500 mt-1 leading-relaxed">{{ $purchaseOrder->dapur->address }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card title="Catatan PO" subtitle="Informasi tambahan dari Yayasan.">
                    <p class="text-[13px] text-slate-600 italic leading-relaxed">
                        {{ $purchaseOrder->notes ?: 'Tidak ada catatan khusus.' }}
                    </p>
                </x-card>
            </div>
        </div>
    </x-container>
</x-app-layout>
