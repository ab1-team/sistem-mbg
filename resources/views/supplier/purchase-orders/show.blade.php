<x-app-layout title="Detail Pesanan">
    <x-container>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('supplier.purchase-orders.index') }}" class="inline-flex items-center text-[13px] font-bold text-slate-400 hover:text-emerald-700 transition-colors mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">
                        Pesanan: {{ $assignment->item->purchaseOrder->po_number }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $assignment->item->purchaseOrder->status->color() }} uppercase whitespace-nowrap">
                        {{ $assignment->item->purchaseOrder->status->label() }}
                    </span>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-center gap-2">
                {{-- Tombol Terima/Tolak: Muncul jika penugasan masih status 'diteruskan' dan PO tidak batal/selesai --}}
                @if($assignment->status === 'diteruskan' && !in_array($assignment->item->purchaseOrder->status->value, ['dibatalkan', 'selesai']))
                    <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="action" value="accept">
                        <x-btn type="submit" class="bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-900/20">TERIMA PESANAN</x-btn>
                    </form>
                    <div x-data="{ open: false }">
                        <x-btn @click="open = true" variant="secondary" class="bg-red-50 text-red-600 border-red-100 hover:bg-red-100">TOLAK</x-btn>
                        
                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
                            <div @click.away="open = false" class="bg-white rounded-[32px] p-8 max-w-md w-full shadow-2xl border border-slate-100">
                                <h3 class="text-[20px] font-black text-slate-900 tracking-tight mb-2">Alasan Penolakan</h3>
                                <p class="text-[13px] text-slate-500 font-medium mb-6 leading-relaxed">Berikan alasan mengapa Anda tidak dapat memenuhi pesanan ini.</p>
                                
                                <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <textarea name="rejection_reason" rows="4" required minlength="5"
                                        class="w-full text-[13px] border-slate-200 rounded-[24px] focus:ring-red-500/20 focus:border-red-500 transition-all bg-slate-50 p-4 mb-6"
                                        placeholder="Contoh: Stok sedang kosong..."></textarea>
                                    
                                    <div class="flex flex-col gap-2">
                                        <x-btn type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-900/20">KIRIM PENOLAKAN</x-btn>
                                        <x-btn @click="open = false" variant="secondary" class="w-full">Batal</x-btn>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($assignment->status === 'diterima')
                    <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="action" value="process">
                        <x-btn type="submit" class="bg-amber-600 hover:bg-amber-700 text-white shadow-lg shadow-amber-900/20">MULAI DIPROSES</x-btn>
                    </form>
                @endif

                @if($assignment->status === 'diproses')
                    <form action="{{ route('supplier.purchase-orders.respond', $assignment) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="action" value="ship">
                        <x-btn type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-900/20">KIRIM BARANG</x-btn>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-2xl text-green-700 text-[13px] font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- LEFT: ITEM DETAILS --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Rincian Pesanan Bahan" subtitle="Data bahan baku yang ditugaskan kepada Anda.">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 p-6 bg-slate-50 rounded-[32px] border border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-[22px] bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-900/10">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[12px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Nama Bahan</p>
                                <p class="text-[20px] font-black text-slate-900 leading-tight">{{ $assignment->item->material->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[12px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Kuantitas Pesanan</p>
                            <p class="text-[24px] font-black text-emerald-600 leading-tight">
                                {{ number_format($assignment->quantity_assigned, 2) }} <span class="text-[14px]">{{ $assignment->item->unit }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="p-6 bg-white border border-slate-100 rounded-[28px] shadow-sm">
                            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4">Informasi Tambahan</p>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-500 font-medium">Harga Kesepakatan:</span>
                                    <span class="text-slate-900 font-bold">Rp {{ number_format($assignment->unit_price_agreed, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-500 font-medium">Total Nilai:</span>
                                    <span class="text-slate-900 font-black">Rp {{ number_format($assignment->quantity_assigned * $assignment->unit_price_agreed, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6 bg-white border border-slate-100 rounded-[28px] shadow-sm">
                            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4">Tujuan Pengiriman</p>
                            <p class="text-[13px] font-black text-slate-900">{{ $assignment->item->purchaseOrder->dapur->name }}</p>
                            <p class="text-[12px] text-slate-500 font-medium mt-2 leading-relaxed italic">{{ $assignment->item->purchaseOrder->dapur->address }}</p>
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- RIGHT: STATUS LOG / HISTORY --}}
            <div class="space-y-6">
                <x-card title="Riwayat Respon" subtitle="Jejak digital pemrosesan pesanan.">
                    <div class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-1 w-[24px] h-[24px] rounded-full bg-green-500 flex items-center justify-center text-white ring-4 ring-white">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-[12px] font-black text-slate-900">Pesanan Diterima</p>
                            <p class="text-[11px] text-slate-400 font-medium mt-1">{{ $assignment->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>

                        @if($assignment->responded_at)
                            <div class="relative pl-8">
                                <div class="absolute left-0 top-1 w-[24px] h-[24px] rounded-full {{ $assignment->status === 'ditolak' ? 'bg-red-500' : 'bg-green-500' }} flex items-center justify-center text-white ring-4 ring-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <p class="text-[12px] font-black text-slate-900">{{ ucfirst($assignment->status) }} oleh Anda</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-1">{{ $assignment->responded_at->translatedFormat('d M Y, H:i') }}</p>
                                @if($assignment->rejection_reason)
                                    <p class="text-[11px] text-red-600 bg-red-50 p-2 rounded-lg mt-2 font-medium italic border border-red-100">"{{ $assignment->rejection_reason }}"</p>
                                @endif
                            </div>
                        @endif

                        @if($assignment->shipped_at)
                            <div class="relative pl-8">
                                <div class="absolute left-0 top-1 w-[24px] h-[24px] rounded-full bg-emerald-500 flex items-center justify-center text-white ring-4 ring-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <p class="text-[12px] font-black text-slate-900">Barang Dikirim</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-1">{{ $assignment->shipped_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </x-container>
</x-app-layout>
