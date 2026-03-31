<x-app-layout title="Detail Purchase Order">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('purchase-orders.index') }}"
                class="inline-flex items-center text-[13px] font-bold text-slate-400 hover:text-green-700 transition-colors mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">
                    {{ $purchaseOrder->po_number }}
                </h1>
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $purchaseOrder->status->color() }} uppercase whitespace-nowrap">
                    {{ $purchaseOrder->status->label() }}
                </span>
            </div>
            <p class="text-[13px] text-slate-400 font-medium mt-2">Dipesan pada
                {{ $purchaseOrder->created_at->translatedFormat('l, d F Y H:i') }} oleh
                {{ $purchaseOrder->creator->name }}</p>
        </div>
        <div class="flex items-center gap-2" x-data="{ showCancelModal: false }">
            @if(in_array($purchaseOrder->status->value, ['diteruskan_ke_supplier', 'diproses_supplier', 'dalam_pengiriman', 'diterima_sebagian']) && (auth()->user()->hasRole('logistik') || auth()->user()->hasRole('admin')))
                <a href="{{ route('gr.create', $purchaseOrder) }}">
                    <x-btn class="bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-900/20">
                        Terima Barang
                    </x-btn>
                </a>
            @endif

            @if ($purchaseOrder->status->value !== 'dibatalkan' && $purchaseOrder->status->value !== 'selesai')
                <x-btn @click="showCancelModal = true" variant="secondary"
                    class="border-red-200! text-red-600! hover:bg-red-50!">
                    Batalkan PO
                </x-btn>
            @endif

            @if ($purchaseOrder->status->value === 'draf')
                <x-btn variant="secondary">Edit PO</x-btn>
                <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="dikirim_ke_yayasan">
                    <x-btn type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-900/20">Kirim ke
                        Yayasan</x-btn>
                </form>
            @endif

            @if ($purchaseOrder->status->value === 'direview_yayasan' && auth()->user()->hasRole('admin'))
                <form action="{{ route('purchase-orders.submit-to-supplier', $purchaseOrder) }}" method="POST"
                    class="inline">
                    @csrf
                    <x-btn type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-900/20">
                        TERUSKAN KE SUPPLIER
                    </x-btn>
                </form>
            @endif

            {{-- MODAL PEMBATALAN (Roadmap 3.5) --}}
            <div x-show="showCancelModal"
                class="fixed inset-0 z-100 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-cloak>
                <div @click.away="showCancelModal = false"
                    class="bg-white rounded-[32px] shadow-2xl w-full max-w-md overflow-hidden"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
                    <div class="p-8">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h3 class="text-[20px] font-black text-slate-900 mb-2">Batalkan Pesanan?</h3>
                        <p class="text-[14px] text-slate-500 mb-6">Tandai pesanan ini sebagai batal. Anda wajib
                            memberikan alasan pembatalan untuk catatan audit.</p>

                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <textarea name="reason" required rows="3"
                                class="w-full rounded-2xl border-slate-200 text-[14px] focus:ring-red-500 focus:border-red-500 placeholder:text-slate-300 mb-6"
                                placeholder="Contoh: Perubahan menu dadakan atau kesalahan input bahan baku..."></textarea>

                            <div class="flex gap-3">
                                <x-btn @click="showCancelModal = false" type="button" variant="secondary"
                                    class="flex-1">Tutup</x-btn>
                                <x-btn type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white">Konfirmasi
                                    Batal</x-btn>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-[13px] font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- LEFT: PO ITEMS --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Daftar Bahan Baku" subtitle="Tugaskan supplier untuk setiap item belanja.">
                <div class="overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                                    Bahan & Supplier</th>
                                <th
                                    class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">
                                    Target Porsi</th>
                                <th
                                    class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($purchaseOrder->items as $item)
                                <tr x-data="{ open: false }" class="group hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-green-50 group-hover:text-green-600 transition-colors shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-900 tracking-tight leading-none mb-1">
                                                    {{ $item->material->name }}</p>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @forelse($item->assignments as $assign)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-100">
                                                            {{ $assign->supplier->name }}
                                                            ({{ number_format($assign->quantity_assigned, 1) }})
                                                        </span>
                                                    @empty
                                                        <span class="text-[11px] text-slate-300 italic">Belum ada
                                                            supplier</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        @php
                                            $assigned = $item->assignments->sum('quantity_assigned');
                                            $isComplete = abs($item->quantity_to_order - $assigned) < 0.001;
                                        @endphp
                                        <div class="inline-block text-right">
                                            <p
                                                class="text-[15px] font-black {{ $isComplete ? 'text-green-600' : 'text-slate-900' }} leading-none">
                                                {{ number_format($item->quantity_to_order, 2) }}
                                            </p>
                                            <p
                                                class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1">
                                                {{ $item->unit }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        @if (auth()->user()->hasRole('admin') &&
                                                ($purchaseOrder->status->value === 'dikirim_ke_yayasan' ||
                                                    $purchaseOrder->status->value === 'direview_yayasan'))
                                            <x-btn @click="open = !open" variant="secondary"
                                                class="py-1.5! px-3! text-[11px]!">
                                                <span x-text="open ? 'Tutup' : 'Kelola Supplier'"></span>
                                            </x-btn>
                                        @endif
                                    </td>

                                    {{-- EXPANDABLE FORM --}}
                                    <template x-if="open">
                                <tr class="bg-white">
                                    <td colspan="3" class="px-8 py-8 border-t border-slate-100 shadow-inner">
                                        <div class="max-w-2xl mx-auto">
                                            <livewire:po-assignment-form :item="$item" :key="'item-' . $item->id" />
                                        </div>
                                    </td>
                                </tr>
                                </template>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-100">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-[11px] font-bold text-slate-400 text-center">
                                    Akhir dari daftar bahan baku
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="bg-slate-50 border-t border-slate-100 px-6 py-5 flex justify-end">
                    <div class="text-right">
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Estimasi
                            Biaya</p>
                        <p class="text-[24px] font-black text-slate-900 tracking-tight leading-none">
                            Rp {{ number_format($purchaseOrder->total_estimated_cost, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- RIGHT: PO INFO --}}
        <div class="space-y-6">
            <x-card title="Informasi Pesanan" subtitle="Detail administratif pesanan ini.">
                <div class="space-y-4">
                    <div>
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Unit Dapur</p>
                        <p class="text-[14px] font-bold text-slate-900">{{ $purchaseOrder->dapur->name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Sumber Rencana
                            Menu</p>
                        <a href="{{ route('menu-periods.show', $purchaseOrder->menuPeriod) }}"
                            class="text-[14px] font-bold text-green-700 underline underline-offset-4 decoration-2 decoration-green-200 hover:decoration-green-500 transition-all">
                            {{ $purchaseOrder->menuPeriod->title }}
                        </a>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $purchaseOrder->menuPeriod->period->name }}</p>
                    </div>
                    @if ($purchaseOrder->notes)
                        <div class="pt-4 border-t border-slate-100">
                            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Catatan
                                Tambahan</p>
                            <p class="text-[13px] text-slate-600 leading-relaxed italic">{{ $purchaseOrder->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- TRACKING --}}
            <div class="p-6 bg-slate-900 rounded-[32px] text-white">
                <h4 class="text-[16px] font-bold mb-4 tracking-tight">Langkah Selanjutnya</h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor"
                                stroke-width="3" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-[12px] font-bold text-slate-300 italic line-through decoration-slate-500">
                            Generate PO dari rencana menu</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center shrink-0 text-slate-500 font-black text-[12px]">
                            2</div>
                        <p class="text-[12px] font-bold text-white">Kirim ke Yayasan untuk review supplier</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center shrink-0 text-slate-500 font-black text-[12px]">
                            3</div>
                        <p class="text-[12px] font-bold text-slate-500">Meneruskan ke Supplier</p>
                    </div>
                </div>
            </div>

            {{-- AUDIT TRAIL / HISTORY (Roadmap 3.3) --}}
            <x-card title="Riwayat Status PO" subtitle="Jejak audit seluruh perubahan status pesanan ini.">
                <div
                    class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                    @foreach ($purchaseOrder->statusHistory->sortByDesc('created_at') as $history)
                        <div class="relative pl-8">
                            <div
                                class="absolute left-0 top-1 w-[24px] h-[24px] rounded-full {{ $loop->first ? 'bg-indigo-600 shadow-lg shadow-indigo-900/20' : 'bg-slate-200' }} flex items-center justify-center text-white ring-4 ring-white">
                                @if ($loop->first)
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="text-[12px] font-black text-slate-900">{{ $history->to_status->label() }}</span>
                                @if ($history->from_status)
                                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span
                                        class="text-[10px] text-slate-400 line-through">{{ $history->from_status->label() }}</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium">
                                Diubah oleh <span class="font-bold text-slate-700">{{ $history->user->name }}</span>
                                <span class="mx-1">•</span>
                                {{ $history->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                            @if ($history->reason)
                                <p
                                    class="mt-2 text-[11px] bg-slate-50 p-2 rounded-lg border border-slate-100 italic text-slate-600">
                                    "{{ $history->reason }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
