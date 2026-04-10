<x-app-layout title="Buat Purchase Order Manual">
    <x-page-header title="Buat PO Manual" subtitle="Buat pesanan bahan baku tanpa melalui rencana menu." back="{{ route('purchase-orders.index') }}" />

    <div class="max-w-3xl">
        <x-card title="Informasi Dasar PO">
            <form action="{{ route('purchase-orders.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    @if ($dapurs->count() === 1)
                        @php $dapur = $dapurs->first(); @endphp
                        <input type="hidden" name="dapur_id" value="{{ $dapur->id }}">
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Unit Dapur</p>
                                <p class="text-[15px] font-black text-slate-900">{{ $dapur->name }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    @else
                        <x-form-searchable-select label="Unit Dapur" name="dapur_id" required
                            :options="$dapurs->map(fn($d) => ['value' => $d->id, 'label' => $d->name])->toArray()" />
                    @endif

                    <x-form-textarea label="Catatan Tambahan (Opsional)" name="notes" placeholder="Contoh: Pesanan mendadak untuk acara gathering..." />

                    <div class="pt-4 flex justify-end gap-3">
                        <x-btn href="{{ route('purchase-orders.index') }}" variant="secondary">Batal</x-btn>
                        <x-btn type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold">
                            Simpan & Tambah Barang
                        </x-btn>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
