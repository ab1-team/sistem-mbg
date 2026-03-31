<x-app-layout title="Penerimaan Barang (QC)">
    <x-page-header 
        title="Penerimaan: {{ $purchaseOrder->po_number }}" 
        subtitle="Supplier: {{ $purchaseOrder->supplier->name ?? 'Beberapa Supplier' }} — Input hasil pengecekan fisik per item."
        :back="route('purchase-orders.show', $purchaseOrder)"
        backLabel="Detail PO"
    >
        <x-slot:actions>
            <x-btn type="submit" form="gr-form">Simpan Penerimaan</x-btn>
        </x-slot:actions>
    </x-page-header>

    <form id="gr-form" action="{{ route('gr.store', $purchaseOrder) }}" method="POST" enctype="experimental" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form Kiri: Metadata --}}
            <div class="lg:col-span-1 space-y-6">
                <x-card title="Informasi Penerimaan" subtitle="Data dasar waktu dan catatan logistik.">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Terima</label>
                            <input type="datetime-local" name="received_at" value="{{ now()->format('Y-m-d\TH:i') }}" required
                                   class="w-full rounded-2xl border-slate-200 text-[14px] focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Catatan Umum</label>
                            <textarea name="notes" rows="3" placeholder="Contoh: Barang datang via kurir internal..."
                                      class="w-full rounded-2xl border-slate-200 text-[14px] focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
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
                                    <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">Item & Target</th>
                                    <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">Input QC</th>
                                    <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">Kondisi / Foto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($purchaseOrder->items as $index => $item)
                                    <tr class="group hover:bg-slate-50/30 transition-colors">
                                        <td class="px-6 py-5 align-top">
                                            <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                            <p class="font-black text-slate-900 tracking-tight leading-none mb-1">{{ $item->material->name }}</p>
                                            <div class="space-y-1 mt-2">
                                                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">
                                                    Dipesan: <span class="text-slate-900">{{ number_format($item->quantity_to_order, 2) }} {{ $item->unit }}</span>
                                                </p>
                                                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">
                                                    Telah Diterima: <span class="text-indigo-600">{{ number_format($item->quantity_received, 2) }} {{ $item->unit }}</span>
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top w-48">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 mb-1">JUMLAH DATANG ({{ $item->unit }})</label>
                                                <input type="number" step="0.01" name="items[{{ $index }}][quantity_received]" 
                                                       value="{{ $item->quantity_to_order - $item->quantity_received }}"
                                                       class="w-full rounded-xl border-slate-200 text-[13px] font-black focus:ring-indigo-500">
                                            </div>
                                            <div class="mt-3">
                                                <label class="block text-[10px] font-bold text-slate-400 mb-1">STATUS QC</label>
                                                <select name="items[{{ $index }}][qc_status]" 
                                                        class="w-full rounded-xl border-slate-200 text-[12px] font-bold focus:ring-indigo-500">
                                                    <option value="sesuai">Sesuai</option>
                                                    <option value="kurang">Kurang</option>
                                                    <option value="rusak">Rusak</option>
                                                    <option value="retur">Retur</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top">
                                            <div>
                                                <textarea name="items[{{ $index }}][qc_notes]" rows="2" placeholder="Catatan kondisi fisik..."
                                                          class="w-full rounded-xl border-slate-200 text-[12px] focus:ring-indigo-500 mb-2"></textarea>
                                                <input type="file" name="items[{{ $index }}][qc_photo]" 
                                                       class="text-[11px] text-slate-500 file:mr-4 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[11px] file:font-black file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
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
