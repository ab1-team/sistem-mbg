<x-app-layout title="Detail Penagihan">
    <x-page-header 
        title="{{ $invoice->invoice_number }}" 
        subtitle="Dibuat otomatis pada {{ $invoice->created_at->translatedFormat('d F Y') }} — Status: {{ str_replace('_', ' ', $invoice->status) }}"
        :back="route('invoices.index')"
        backLabel="Daftar Invoices"
    >
        <x-slot:actions>
            <a href="{{ route('invoices.download', $invoice) }}">
                <x-btn variant="secondary">Cetak PDF</x-btn>
            </a>

            @if($invoice->status === 'generated')
                <form action="{{ route('invoices.verify', $invoice) }}" method="POST">
                    @csrf
                    <x-btn type="submit">Verifikasi Tagihan</x-btn>
                </form>
            @endif

            @if($invoice->status === 'diverifikasi')
                <x-btn @click="$dispatch('open-modal', 'modal-pay')" class="bg-green-600 hover:bg-green-700 text-white">Bayar Tagihan</x-btn>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kiri: Detail Items --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card :padding="false" title="Rincian Tagihan Aktual" subtitle="Dihitung berdasarkan kuantitas barang yang benar-benar diterima (GR).">
                <x-table>
                    <x-slot name="thead">
                        <x-table-th>Item</x-table-th>
                        <x-table-th class="text-right">Qty</x-table-th>
                        <x-table-th class="text-right">Harga Satuan</x-table-th>
                        <x-table-th class="text-right">Total</x-table-th>
                    </x-slot>

                    @foreach($invoice->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <x-table-td>
                                <p class="font-semibold text-slate-900 tracking-tight leading-none mb-1">{{ $item->material->name }}</p>
                                <p class="font-mono text-[10px] text-slate-400 font-medium uppercase tracking-tighter">{{ $item->poItem->unit }}</p>
                            </x-table-td>
                            <x-table-td class="text-right font-bold text-slate-700">
                                {{ number_format($item->quantity, 2) }}
                            </x-table-td>
                            <x-table-td class="text-right text-slate-500">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </x-table-td>
                            <x-table-td class="text-right font-bold text-slate-900">
                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                            </x-table-td>
                        </tr>
                    @endforeach
                    
                    <x-slot name="tfoot">
                        <tr class="bg-slate-50/50 border-t border-slate-100">
                            <td colspan="3" class="px-6 py-4 text-right text-[11px] font-medium text-slate-400 uppercase tracking-widest">Total Bayar</td>
                            <td class="px-6 py-4 text-right text-[18px] font-semibold text-slate-900 tracking-tight">
                                Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </x-slot>
                </x-table>
            </x-card>
        </div>

        {{-- Kanan: Metadata & Finance --}}
        <div class="lg:col-span-1 space-y-6">
            <x-card title="Informasi Supplier" subtitle="Pihak yang harus dibayarkan tagihannya.">
                <div class="space-y-4">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-widest mb-1">Nama Supplier</p>
                        <p class="text-[14px] font-medium text-slate-900">{{ $invoice->supplier->name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-widest mb-1">Dapur Pemesan</p>
                        <p class="text-[14px] font-medium text-slate-900">{{ $invoice->dapur->name }}</p>
                    </div>
                </div>
            </x-card>

            @if($invoice->status === 'dibayar')
                <x-card title="Bukti Pembayaran" subtitle="Dokumen verifikasi transaksi finansial.">
                    <img src="{{ Storage::url($invoice->payment_proof) }}" class="rounded-2xl w-full border border-slate-100 shadow-sm" alt="Bukti Transfer">
                    <p class="mt-4 text-[11px] text-center text-slate-400 font-medium uppercase tracking-tighter">Dibayar pada {{ $invoice->paid_at->translatedFormat('d M Y, H:i') }}</p>
                </x-card>
            @endif
        </div>
    </div>

    {{-- MODAL PEMBAYARAN (Phase 4.3) --}}
    <x-dialog name="modal-pay" title="Catat Pembayaran">
        <form action="{{ route('invoices.pay', $invoice) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <x-form-file 
                    name="payment_proof" 
                    label="Unggah Bukti Transfer / Kwitansi" 
                    required 
                    accept="image/*,application/pdf"
                    hint="Unggah bukti pembayaran fisik (foto/scan) untuk catatan audit finansial."
                />
            </div>
            
            <div class="flex gap-3 mt-8">
                <x-btn @click="$dispatch('close-modal', 'modal-pay')" type="button" variant="secondary" class="flex-1">Tutup</x-btn>
                <x-btn type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white">Konfirmasi Lunas</x-btn>
            </div>
        </form>
    </x-dialog>
</x-app-layout>
