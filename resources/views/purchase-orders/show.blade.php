<x-app-layout title="Detail Purchase Order">
    <x-container>
        <x-page-header title="{{ $purchaseOrder->po_number }}"
            subtitle="Tanggal PO: {{ $purchaseOrder->po_date?->translatedFormat('d F Y') ?? $purchaseOrder->created_at->translatedFormat('d F Y') }} • Dipesan oleh {{ ucwords($purchaseOrder->creator->name) }}"
            back="{{ route('purchase-orders.index') }}">
            <x-slot name="actions">
                {{-- CANCEL PO - ALWAYS CORNER LEFT IF APPLICABLE --}}
                @if ($purchaseOrder->status->value !== 'dibatalkan' && $purchaseOrder->status->value !== 'selesai')
                    <x-dialog name="cancel-po" title="Batalkan Pesanan?">
                        <x-slot name="trigger">
                            <x-btn @click="$dispatch('open-modal', 'cancel-po')" variant="secondary"
                                class="border-red-200! text-red-600! hover:bg-red-50!">
                                Batalkan PO
                            </x-btn>
                        </x-slot>

                        <p class="text-[13px] text-slate-500 mb-6 font-medium leading-relaxed">Tandai pesanan ini
                            sebagai batal. Anda wajib memberikan alasan pembatalan untuk catatan audit.</p>

                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <x-form-textarea label="Alasan Pembatalan" name="reason" required rows="3"
                                placeholder="Contoh: Kesalahan input atau stok supplier habis..." />

                            <div class="mt-8 flex gap-3">
                                <x-btn @click="$dispatch('close-modal', 'cancel-po')" type="button" variant="secondary"
                                    class="flex-1">Batal</x-btn>
                                <x-btn type="submit"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold">Konfirmasi
                                    Batal</x-btn>
                            </div>
                        </form>
                    </x-dialog>
                @endif

                {{-- OTHER ACTIONS --}}
                @php
                    $isFullyReceived = $purchaseOrder->items->every(
                        fn($item) => $item->quantity_received >= $item->quantity_to_order,
                    );
                    $allowedReceptionStatuses = [
                        'diteruskan_ke_supplier',
                        'diproses_supplier',
                        'dalam_pengiriman',
                        'diterima_sebagian',
                    ];
                @endphp

                @if (in_array($purchaseOrder->status->value, $allowedReceptionStatuses) && !$isFullyReceived)
                    @if (auth()->user()->hasRole(['logistik', 'admin', 'superadmin']))
                        <x-btn href="{{ route('gr.create', $purchaseOrder) }}"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white">
                            Terima Barang
                        </x-btn>
                    @endif
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
                        <x-btn type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white shadow-lg shadow-emerald-900/10">
                            Teruskan Ke Supplier
                        </x-btn>
                    </form>
                @endif

                @if (
                    (($purchaseOrder->status->value === 'menunggu_verifikasi_dapur' ||
                        (auth()->user()->hasRole('superadmin') &&
                            in_array($purchaseOrder->status->value, ['diterima_sebagian', 'diterima_lengkap']))) ||
                        ($purchaseOrder->status->value === 'selesai' && $purchaseOrder->invoices()->count() === 0)) &&
                        auth()->user()->hasRole(['kepala_dapur', 'admin', 'superadmin']))
                    <x-dialog name="po-verification-modal" title="Verifikasi Penerimaan Dapur">
                        <x-slot name="trigger">
                            <x-btn @click="$dispatch('open-modal', 'po-verification-modal')"
                                class="bg-brand hover:bg-brand-dark text-white shadow-lg shadow-brand/20">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="3"
                                    viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verifikasi Dapur
                            </x-btn>
                        </x-slot>

                        <div class="space-y-5">
                            {{-- HEADER INFO --}}
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <div>
                                    <p class="text-[12px] font-black text-slate-800">{{ $purchaseOrder->po_number }}</p>
                                    <p class="text-[11px] text-slate-400">{{ ucwords($purchaseOrder->dapur->name) }} • {{ $purchaseOrder->items->count() }} bahan baku</p>
                                </div>
                            </div>

                            {{-- ITEM BREAKDOWN --}}
                            <div class="space-y-3">
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Detail Penerimaan per Bahan</p>
                                @foreach($purchaseOrder->items as $item)
                                    @php
                                        $pct = $item->quantity_to_order > 0
                                            ? min(100, round(($item->quantity_received / $item->quantity_to_order) * 100))
                                            : 0;
                                        $isLunas = $item->quantity_received >= $item->quantity_to_order;
                                    @endphp
                                    <div class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm space-y-3">
                                        {{-- Item header --}}
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <p class="text-[13px] font-black text-slate-800">{{ ucwords($item->material->name) }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $item->unit }}</p>
                                            </div>
                                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-black {{ $isLunas ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-600' }}">
                                                {{ $isLunas ? '✓ Lunas' : '⚠ Kurang' }}
                                            </span>
                                        </div>

                                        {{-- Progress bar --}}
                                        <div>
                                            <div class="flex justify-between text-[10px] font-bold mb-1">
                                                <span class="text-slate-500">Diterima: <span class="{{ $isLunas ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($item->quantity_received, 1) }}</span> / {{ number_format($item->quantity_to_order, 1) }} {{ $item->unit }}</span>
                                                <span class="{{ $isLunas ? 'text-emerald-600' : 'text-rose-500' }}">{{ $pct }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full transition-all {{ $isLunas ? 'bg-emerald-500' : 'bg-rose-400' }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>

                                        {{-- Supplier breakdown --}}
                                        @if($item->assignments->count() > 0)
                                            <div class="space-y-1.5 pt-1 border-t border-slate-50">
                                                @foreach($item->assignments as $assign)
                                                    <div class="flex items-center justify-between text-[11px]">
                                                        <div class="flex items-center gap-1.5 text-slate-500">
                                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                            <span>{{ $assign->subSupplier ? ucwords($assign->subSupplier->name) : ucwords($assign->supplier->name) }}</span>
                                                        </div>
                                                        <div class="text-right font-bold">
                                                            <span class="text-slate-700">{{ number_format($assign->quantity_received, 1) }}</span>
                                                            <span class="text-slate-400"> / {{ number_format($assign->quantity_assigned, 1) }} {{ $item->unit }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- SUMMARY BAR --}}
                            @php
                                $totalItems = $purchaseOrder->items->count();
                                $lunas = $purchaseOrder->items->filter(fn($i) => $i->quantity_received >= $i->quantity_to_order)->count();
                            @endphp
                            <div class="flex items-center justify-between px-4 py-3 rounded-2xl {{ $lunas === $totalItems ? 'bg-emerald-50 border border-emerald-100' : 'bg-amber-50 border border-amber-100' }}">
                                <p class="text-[12px] font-bold {{ $lunas === $totalItems ? 'text-emerald-700' : 'text-amber-700' }}">
                                    {{ $lunas }}/{{ $totalItems }} bahan terpenuhi
                                </p>
                                <p class="text-[11px] font-bold {{ $lunas === $totalItems ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $lunas === $totalItems ? 'Siap diverifikasi' : 'Ada kekurangan' }}
                                </p>
                            </div>

                            <form action="{{ route('purchase-orders.verify', $purchaseOrder) }}" method="POST">
                                @csrf
                                <x-form-textarea label="Catatan untuk Finance (Opsional)" name="notes" rows="2"
                                    placeholder="Contoh: Semua barang sudah dicek dan sesuai..." />
                                <div class="mt-4 flex flex-col gap-3">
                                    <x-btn type="submit"
                                        class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                        Verifikasi & Selesaikan
                                    </x-btn>
                                </div>
                            </form>

                            <form action="{{ route('purchase-orders.deficit', $purchaseOrder) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 text-[12px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-2xl transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Laporkan Defisit (Ada barang kurang, perlu belanja lagi)
                                </button>
                            </form>
                        </div>
                    </x-dialog>
                @endif

                @if ($purchaseOrder->invoices()->exists())
                    <x-btn href="{{ route('finance.invoices.show', $purchaseOrder->invoices->first()) }}"
                        variant="secondary" class="bg-white border-emerald-200 text-emerald-700 hover:bg-emerald-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Tagihan
                    </x-btn>
                @endif
            </x-slot>
        </x-page-header>


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
                        <x-show-field label="Tanggal Purchase Order" :value="$purchaseOrder->po_date?->translatedFormat('d F Y') ?? '-'" />
                        
                        @if($purchaseOrder->delivery_date)
                            <x-show-field label="Jadwal Pengiriman">
                                <span class="text-[13px] font-bold text-slate-700">
                                    {{ $purchaseOrder->delivery_date->translatedFormat('d F Y') }}
                                    @if($purchaseOrder->delivery_time_start || $purchaseOrder->delivery_time_end)
                                        <span class="text-slate-400 font-medium ml-1">
                                            ({{ $purchaseOrder->delivery_time_start ? \Carbon\Carbon::parse($purchaseOrder->delivery_time_start)->format('H:i') : '' }} - {{ $purchaseOrder->delivery_time_end ? \Carbon\Carbon::parse($purchaseOrder->delivery_time_end)->format('H:i') : '' }})
                                        </span>
                                    @endif
                                </span>
                            </x-show-field>
                        @endif

                        <x-show-field label="Tujuan Rencana Menu">
                            @if ($purchaseOrder->menuPeriod)
                                <a href="{{ route('menu-periods.show', $purchaseOrder->menuPeriod) }}"
                                    class="text-[13px] font-bold text-emerald-700 hover:underline">
                                    {{ $purchaseOrder->menuPeriod->title }}
                                </a>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $purchaseOrder->menuPeriod->period->name }}</p>
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

                {{-- TRACKING --}}
                <div class="bg-emerald-50/50 border border-emerald-100 rounded-3xl p-6">
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
                                        'menunggu_verifikasi_dapur',
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
                                        'menunggu_verifikasi_dapur',
                                        'selesai',
                                    ]),
                                ],
                                [
                                    'label' => 'Penerimaan (GR)',
                                    'done' => in_array($statusVal, [
                                        'diterima_sebagian',
                                        'diterima_lengkap',
                                        'menunggu_verifikasi_dapur',
                                        'selesai',
                                    ]),
                                ],
                                [
                                    'label' => 'Verifikasi Dapur',
                                    'done' => in_array($statusVal, ['selesai']),
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
                                    {{ $step['label'] }}</p>
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
                                </div>
                                <p class="text-[11px] text-slate-500">{{ $history->user->name }} •
                                    {{ $history->created_at->format('d/m/y H:i') }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>
    </x-container>

    {{-- MODALS & LIVEWIRE COMPONENTS --}}
    <livewire:po-assignment-form />
</x-app-layout>
