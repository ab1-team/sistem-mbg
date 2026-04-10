<x-app-layout title="Detail Purchase Order">
    <x-page-header title="{{ $purchaseOrder->po_number }}"
        subtitle="Dipesan oleh {{ ucwords($purchaseOrder->creator->name) }} pada {{ $purchaseOrder->created_at->translatedFormat('d F Y, H:i') }}"
        back="{{ route('purchase-orders.index') }}">
        <x-slot name="actions">
            @if (in_array($purchaseOrder->status->value, [
                    'diteruskan_ke_supplier',
                    'diproses_supplier',
                    'dalam_pengiriman',
                    'diterima_sebagian',
                ]) &&
                    auth()->user()->hasRole(['logistik', 'admin', 'superadmin']))
                <x-btn href="{{ route('gr.create', $purchaseOrder) }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white">
                    Terima Barang
                </x-btn>
            @endif

            @if ($purchaseOrder->status->value !== 'dibatalkan' && $purchaseOrder->status->value !== 'selesai')
                <x-btn @click="$dispatch('open-cancel-modal')" variant="secondary"
                    class="border-red-200! text-red-600! hover:bg-red-50!">
                    Batalkan PO
                </x-btn>
            @endif

            @if ($purchaseOrder->status->value === 'draf')
                <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="dikirim_ke_yayasan">
                    <x-btn type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white">Kirim ke
                        Yayasan</x-btn>
                </form>
            @endif

            @if (
                $purchaseOrder->status->value === 'direview_yayasan' &&
                    auth()->user()->hasRole(['admin', 'superadmin']))
                <form action="{{ route('purchase-orders.submit-to-supplier', $purchaseOrder) }}" method="POST"
                    class="inline">
                    @csrf
                    <x-btn type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white">
                        Teruskan Ke Supplier
                    </x-btn>
                </form>
            @endif

            @if ($purchaseOrder->invoices()->exists())
                <x-btn href="{{ route('finance.invoices.show', $purchaseOrder->invoices->first()) }}"
                    variant="secondary" class="bg-white border-emerald-200 text-emerald-700 hover:bg-emerald-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Tagihan (Invoice)
                </x-btn>
            @endif

            @if (
                $purchaseOrder->status->value === 'diterima_lengkap' &&
                    auth()->user()->hasRole(['admin', 'superadmin', 'finance']))
                <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="selesai">
                    <x-btn type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white">
                        Selesaikan PO
                    </x-btn>
                </form>
            @endif
        </x-slot>
    </x-page-header>

    <div x-data="{ showCancelModal: false }" @open-cancel-modal.window="showCancelModal = true">
        {{-- MODAL PEMBATALAN --}}
        <div x-show="showCancelModal"
            class="fixed inset-0 z-1000 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
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
                    <p class="text-[14px] text-slate-500 mb-6">Tandai pesanan ini sebagai batal. Anda wajib memberikan
                        alasan pembatalan untuk catatan audit.</p>

                    <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                        @csrf
                        <textarea name="reason" required rows="3"
                            class="w-full rounded-2xl border-slate-200 text-[14px] focus:ring-red-500 focus:border-red-500 placeholder:text-slate-300 mb-6"
                            placeholder="Alasan pembatalan..."></textarea>

                        <div class="flex gap-3">
                            <x-btn @click="showCancelModal = false" type="button" variant="secondary"
                                class="flex-1">Tutup</x-btn>
                            <x-btn type="submit"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold">Konfirmasi Batal</x-btn>
                        </div>
                    </form>
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
            <x-card title="Daftar Bahan Baku" subtitle="Daftar bahan baku yang perlu dipesan dan alokasi supplier.">
                <livewire:po-items-table :purchaseOrder="$purchaseOrder" />
            </x-card>
        </div>

        {{-- RIGHT: PO INFO --}}
        <div class="space-y-6">
            <x-card title="Informasi Pesanan">
                <div class="space-y-5">
                    <x-show-field label="Unit Dapur" :value="ucwords($purchaseOrder->dapur->name)" />

                    <x-show-field label="Tujuan Rencana Menu">
                        @if ($purchaseOrder->menuPeriod)
                            <a href="{{ route('menu-periods.show', $purchaseOrder->menuPeriod) }}"
                                class="text-[13px] font-bold text-emerald-700 hover:underline">
                                {{ $purchaseOrder->menuPeriod->title }}
                            </a>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $purchaseOrder->menuPeriod->period->name }}</p>
                        @else
                            <p class="text-[13px] font-bold text-slate-500 italic">Manual (Tanpa Rencana Menu)</p>
                        @endif
                        <div class="mt-2">
                            <span
                                class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $purchaseOrder->status->color() }} whitespace-nowrap">
                                {{ $purchaseOrder->status->label() }}
                            </span>
                        </div>
                    </x-show-field>

                    @if ($purchaseOrder->notes)
                        <div class="pt-4 border-t border-slate-100">
                            <x-show-field label="Catatan Tambahan" :value="$purchaseOrder->notes" />
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- TRACKING (Standardized emerald style) --}}
            <div class="bg-brand-soft border border-emerald-100 rounded-3xl p-6">
                <h4 class="text-[13px] font-bold text-emerald-900 tracking-wider mb-4">Langkah Operasional</h4>
                <div class="space-y-4">
                    @php
                        $statusVal = $purchaseOrder->status->value;
                        $hasInvoice = $purchaseOrder->invoices()->exists();
                        $steps = [
                            ['label' => 'Generate PO', 'done' => true],
                            [
                                'label' => 'Review Yayasan',
                                'done' => in_array($statusVal, [
                                    'direview_yayasan',
                                    'diteruskan_ke_supplier',
                                    'diproses_supplier',
                                    'dalam_pengiriman',
                                    'diterima_sebagian',
                                    'diterima_lengkap',
                                    'selesai',
                                ]),
                            ],
                            [
                                'label' => 'Proses Supplier',
                                'done' => in_array($statusVal, [
                                    'diteruskan_ke_supplier',
                                    'diproses_supplier',
                                    'dalam_pengiriman',
                                    'diterima_sebagian',
                                    'diterima_lengkap',
                                    'selesai',
                                ]),
                            ],
                            [
                                'label' => 'Penerimaan (GR)',
                                'done' => in_array($statusVal, ['diterima_sebagian', 'diterima_lengkap', 'selesai']),
                            ],
                            ['label' => 'Tagihan (Invoice)', 'done' => $hasInvoice],
                            ['label' => 'Selesai', 'done' => $statusVal === 'selesai'],
                        ];
                    @endphp

                    @foreach ($steps as $index => $step)
                        <div class="flex items-center gap-3">
                            <div
                                class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $step['done'] ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-400' }}">
                                @if ($step['done'])
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <span class="text-[10px] font-bold text-emerald-600">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <p
                                class="text-[12px] font-semibold {{ $step['done'] ? 'text-emerald-900/40 line-through' : 'text-emerald-900' }}">
                                {{ $step['label'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- AUDIT TRAIL / HISTORY --}}
            <x-card title="Riwayat Status">
                <div
                    class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-px before:bg-slate-100">
                    @foreach ($purchaseOrder->statusHistory->sortByDesc('created_at') as $history)
                        <div class="relative pl-8">
                            <div
                                class="absolute left-0 top-1 w-[22px] h-[22px] rounded-full border bg-white flex items-center justify-center ring-4 ring-white {{ $loop->first ? 'border-emerald-600 text-emerald-600' : 'border-slate-200 text-slate-300' }}">
                                <div
                                    class="w-1.5 h-1.5 rounded-full {{ $loop->first ? 'bg-emerald-600' : 'bg-slate-300' }}">
                                </div>
                            </div>
                            <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5 mb-1">
                                <span
                                    class="text-[12px] font-bold text-slate-900">{{ $history->to_status->label() }}</span>
                                @if ($history->from_status)
                                    <span class="text-[10px] text-slate-400">←
                                        {{ $history->from_status->label() }}</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500">
                                {{ $history->user->name }} • {{ $history->created_at->format('d/m/y H:i') }}
                            </p>
                            @if ($history->reason)
                                <p class="mt-1.5 text-[11px] text-slate-400 italic">"{{ $history->reason }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>

    <livewire:po-assignment-form />
    <livewire:po-add-item-form />

    {{-- MODAL IMPORT PO --}}
    <div x-data="{ open: false, poId: null }" 
         @open-import-po.window="open = true; poId = $event.detail.poId"
         x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 tracking-tight">Import Bahan Baku</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="`/purchase-orders/${poId}/import`" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                {{-- Centered Upload Area --}}
                <div class="border border-dashed border-slate-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 mb-4 border border-slate-100 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    
                    <h4 class="text-[14px] font-bold text-slate-800 mb-1">Pilih File Excel/CSV</h4>
                    <p class="text-[12px] text-slate-500 mb-5">Maksimal ukuran file 5MB</p>
                    
                    <div class="relative max-w-xs mx-auto">
                        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors cursor-pointer" />
                    </div>
                </div>

                {{-- Alert Note --}}
                <div class="mt-4 p-4 bg-emerald-50/50 border border-emerald-100 rounded-xl flex gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01" />
                    </svg>
                    <div class="text-[12px] text-emerald-800 leading-snug">
                        Gunakan template kami untuk memastikan format data benar.
                        <a href="{{ route('purchase-orders.download-template') }}" class="font-bold underline hover:text-emerald-900 ml-1">Unduh Template CSV</a>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-[13px]">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-100 transition-all text-[13px]">
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
