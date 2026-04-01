<x-app-layout title="Penerimaan Barang (QC)">
    <x-page-header 
        title="Penerimaan: {{ $purchaseOrder->po_number }}" 
        subtitle="Unit Dapur: {{ $purchaseOrder->dapur->name }} — Konfirmasi jumlah barang datang dan hasil pengecekan (QC)."
        :back="route('purchase-orders.show', $purchaseOrder)"
        backLabel="Kembali ke PO"
    >
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
                        <x-form-input 
                            type="datetime-local" 
                            label="Tanggal Terima" 
                            name="received_at" 
                            :value="now()->format('Y-m-d\TH:i')" 
                            required 
                        />
                        
                        <x-form-textarea 
                            label="Catatan Umum" 
                            name="notes" 
                            rows="3" 
                            placeholder="Contoh: Barang datang via kurir internal..." 
                        />
                    </div>
                </x-card>
            </div>

            {{-- Form Kanan: Items QC --}}
            <div class="lg:col-span-2">
                <x-card title="Daftar Barang & Control Kualitas" subtitle="Input jumlah aktual dan hasil pengecekan fisik per item.">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <x-table-th>Item & Info</x-table-th>
                                    <x-table-th>Input Penerimaan</x-table-th>
                                    <x-table-th>Catatan Kondisi</x-table-th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($purchaseOrder->load('items.material', 'items.assignments.supplier')->items as $index => $item)
                                    <tr class="group hover:bg-slate-50/30 transition-colors">
                                        <td class="px-6 py-5 align-top">
                                            <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                            <p class="font-bold text-slate-900 leading-none mb-2">{{ ucwords($item->material->name) }}</p>
                                            <div class="space-y-1.5">
                                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-400">
                                                    <span>Dipesan:</span>
                                                    <span class="text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">{{ number_format($item->quantity_to_order, 2) }} {{ $item->unit ?? 'Satuan' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-400">
                                                    <span>Sudah Ada:</span>
                                                    <span class="text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">{{ number_format($item->quantity_received, 2) }} {{ $item->unit ?? 'Satuan' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top w-56">
                                            <div class="space-y-4">
                                                <x-form-input 
                                                    type="number" 
                                                    step="0.01" 
                                                    label="Jumlah Datang ({{ $item->unit }})" 
                                                    name="items[{{ $index }}][quantity_received]" 
                                                    :value="$item->quantity_to_order - $item->quantity_received" 
                                                    required 
                                                />

                                                <x-form-searchable-select 
                                                    label="Hasil Pemeriksaan" 
                                                    name="items[{{ $index }}][qc_status]" 
                                                    :options="[
                                                        ['value' => 'sesuai', 'label' => 'Sesuai / Bagus'],
                                                        ['value' => 'kurang', 'label' => 'Kurang (Qty Tidak Pas)'],
                                                        ['value' => 'rusak', 'label' => 'Rusak / Layu'],
                                                        ['value' => 'retur', 'label' => 'Retur (Kembali)'],
                                                    ]"
                                                    selected="sesuai"
                                                    required
                                                />
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top">
                                            <div class="space-y-3">
                                                <x-form-textarea 
                                                    label="Catatan Kondisi" 
                                                    name="items[{{ $index }}][qc_notes]" 
                                                    rows="2" 
                                                    placeholder="Catatan kondisi fisik..." 
                                                />
                                                
                                                <div class="space-y-1.5">
                                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Foto Bukti (Opsional)</label>
                                                    <input type="file" name="items[{{ $index }}][qc_photo]" 
                                                           class="block w-full text-[11px] text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</x-app-layout>
