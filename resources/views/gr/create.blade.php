<x-app-layout title="Penerimaan Barang (QC)">
    <x-container>
        <x-page-header title="Penerimaan: {{ $purchaseOrder->po_number }}"
            subtitle="Unit Dapur: {{ $purchaseOrder->dapur->name }} — Konfirmasi jumlah barang datang dan hasil pengecekan (QC)."
            :back="route('purchase-orders.show', $purchaseOrder)" backLabel="Kembali ke PO">
            <x-slot:actions>
                <x-btn type="submit" form="gr-form">
                    Simpan Penerimaan
                </x-btn>
            </x-slot:actions>
        </x-page-header>

        <form id="gr-form" action="{{ route('gr.store', $purchaseOrder) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Form Kiri: Metadata --}}
                <div class="lg:col-span-1 space-y-6">
                    <x-card title="Informasi Penerimaan" subtitle="Data dasar waktu dan catatan logistik.">
                        <div class="space-y-5">
                            <x-datepicker label="Waktu Kedatangan" name="received_at" :value="now()->format('Y-m-d H:i')" enableTime="true"
                                required />

                            <x-form-textarea label="Catatan Umum" name="notes" rows="3"
                                placeholder="Contoh: Barang datang via kurir internal..." />
                        </div>
                    </x-card>

                    @if($purchaseOrder->goodsReceipts->count() > 0)
                        <x-card title="Riwayat Penerimaan" subtitle="Daftar barang yang sudah masuk sebelumnya.">
                            <div class="space-y-4">
                                @foreach($purchaseOrder->goodsReceipts->sortByDesc('received_at') as $prevGr)
                                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $prevGr->received_at->translatedFormat('d M, H:i') }}</span>
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">DITERIMA</span>
                                        </div>
                                        <div class="space-y-1">
                                            @foreach($prevGr->items as $grItem)
                                                <p class="text-[11px] text-slate-600 flex justify-between">
                                                    <span>{{ $grItem->material->name }}</span>
                                                    <span class="font-bold text-slate-900">{{ number_format($grItem->quantity_received, 1) }} {{ $grItem->unit }}</span>
                                                </p>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif
                </div>

                <div class="space-y-6 lg:col-span-2">
                    @foreach ($purchaseOrder->load('items.material', 'items.assignments.supplier')->items as $index => $item)
                        <x-card class="w-full">
                            <x-slot name="title">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[15px] font-black tracking-tight text-slate-800">{{ ucwords($item->material->name) }}</span>
                                    @if($item->quantity_received >= $item->quantity_to_order)
                                        <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 uppercase tracking-widest">Terpenuhi</span>
                                    @endif
                                </div>
                            </x-slot>

                            <x-slot name="action">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-end">
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Target
                                            PO</span>
                                        <span
                                            class="text-[12px] font-bold text-slate-600">{{ number_format($item->quantity_to_order, 2) }}
                                            {{ $item->unit }}</span>
                                    </div>
                                    <div class="w-px h-6 bg-slate-100"></div>
                                    <div class="flex flex-col items-end">
                                        <span
                                            class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Diterima</span>
                                        <span
                                            class="text-[12px] font-bold text-emerald-600">{{ number_format($item->quantity_received, 2) }}
                                            {{ $item->unit }}</span>
                                    </div>
                                </div>
                            </x-slot>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                @if($item->quantity_received < $item->quantity_to_order || auth()->user()->hasRole('superadmin'))
                                    {{-- LEFT: INPUTS --}}
                                    <div class="space-y-6">
                                        <input type="hidden" name="items[{{ $index }}][po_item_id]"
                                            value="{{ $item->id }}">

                                        <x-form-input type="number" step="0.01"
                                            label="Jumlah Datang ({{ $item->unit }})"
                                            name="items[{{ $index }}][quantity_received]" :value="max(0, $item->quantity_to_order - $item->quantity_received)"
                                            required />

                                        @php
                                            $assignmentOptions = $item->assignments
                                                ->map(
                                                    fn($a) => [
                                                        'value' => (string) $a->id,
                                                        'label' =>
                                                            ($a->subSupplier
                                                                ? ucwords($a->subSupplier->name)
                                                                : ucwords($a->supplier->name)) .
                                                            ' (' .
                                                            number_format(
                                                                max(0, $a->quantity_assigned - $a->quantity_received),
                                                                2,
                                                            ) .
                                                            ' sisa)',
                                                    ],
                                                )
                                                ->toArray();
                                        @endphp

                                        <x-form-searchable-select label="Dari Personel (Sub-Supplier)"
                                            name="items[{{ $index }}][po_supplier_assignment_id]" :options="$assignmentOptions"
                                            :selected="$item->assignments->first()?->id" placeholder="Cari personel..." required />

                                        <x-form-searchable-select label="Hasil Pemeriksaan QC"
                                            name="items[{{ $index }}][qc_status]" :options="[
                                                ['value' => 'sesuai', 'label' => 'Sesuai / Bagus'],
                                                ['value' => 'kurang', 'label' => 'Kurang (Qty Tidak Pas)'],
                                                ['value' => 'rusak', 'label' => 'Rusak / Layu'],
                                                ['value' => 'retur', 'label' => 'Retur (Kembali)'],
                                            ]"
                                            selected="sesuai" required />
                                    </div>

                                    {{-- RIGHT: NOTES & PHOTO --}}
                                    <div class="space-y-6">
                                        <x-form-textarea label="Catatan Kondisi Fisik"
                                            name="items[{{ $index }}][qc_notes]" rows="4"
                                            placeholder="Gambarkan kondisi fisik barang yang diterima..." />

                                        <x-form-file 
                                            label="Foto Bukti (Opsional)"
                                            name="items[{{ $index }}][qc_photo]" 
                                            accept="image/*"
                                        />
                                    </div>
                                @else
                                    <div class="md:col-span-2 p-8 bg-slate-50 border border-slate-100 rounded-[32px] flex flex-col items-center justify-center text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-emerald-600 mb-4 shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <p class="text-[14px] font-bold text-slate-900">Barang Sudah Lengkap</p>
                                        <p class="text-[12px] text-slate-500 mt-1">Item ini sudah diterima sesuai target ({{ number_format($item->quantity_to_order, 1) }} {{ $item->unit }}). Tidak ada aksi diperlukan.</p>
                                    </div>
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>
            </div>
            </div>
        </form>
    </x-container>
</x-app-layout>
