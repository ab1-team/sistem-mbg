<x-app-layout title="Import Massal Purchase Order">
    <x-container>
        <x-page-header title="Import Massal Purchase Order"
            subtitle="Buat PO baru sekaligus meng-import daftar item dari file CSV/Excel."
            back="{{ route('purchase-orders.index') }}" />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <x-card title="Upload File Import" subtitle="Pilih file yang berisi daftar bahan baku.">
                    <form action="{{ route('purchase-orders.bulk-import.process') }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        @if ($dapurs->count() === 1)
                            @php $dapur = $dapurs->first(); @endphp
                            <div
                                class="bg-slate-50 border border-slate-100 rounded-2xl p-6 flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Unit
                                        Dapur Penerima</p>
                                    <p class="text-[16px] font-black text-slate-900">{{ $dapur->name }}</p>
                                    <input type="hidden" name="dapur_id" value="{{ $dapur->id }}">
                                </div>
                                <div
                                    class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                        @else
                            <x-form-searchable-select label="Unit Dapur" name="dapur_id" required :options="$dapurs->map(fn($d) => ['value' => $d->id, 'label' => $d->name])->toArray()" />
                        @endif

                        <x-datepicker label="Tanggal Purchase Order" name="po_date" required
                            value="{{ old('po_date', now()->format('Y-m-d')) }}" />

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-datepicker label="Tanggal Pengiriman" name="delivery_date"
                                value="{{ old('delivery_date') }}" placeholder="Pilih tanggal..." />
                            <x-datepicker label="Jam Mulai" name="delivery_time_start" noCalendar="true"
                                value="{{ old('delivery_time_start') }}" placeholder="08:00" />
                            <x-datepicker label="Jam Selesai" name="delivery_time_end" noCalendar="true"
                                value="{{ old('delivery_time_end') }}" placeholder="12:00" />
                        </div>

                        <x-form-textarea label="Catatan Tambahan (Opsional)" name="notes"
                            placeholder="Contoh: Import dari laporan mingguan..." value="{{ old('notes') }}" />

                        <x-form-file label="Pilih File CSV/Excel" name="file" required accept=".csv,.xlsx,.xls"
                            hint="Maksimal 10MB. Gunakan format kolom yang sesuai." />

                        <div class="pt-6 flex justify-end gap-3 border-t border-slate-100">
                            <x-btn href="{{ route('purchase-orders.index') }}" variant="secondary">Batal</x-btn>
                            <x-btn type="submit" variant="primary">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Proses Import
                            </x-btn>
                        </div>
                    </form>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Petunjuk Format" :padding="true">
                    <div class="space-y-4">
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-[13px] font-bold text-amber-900 mb-1">Penting!</p>
                                    <p class="text-[12px] text-amber-800 leading-relaxed">
                                        Jika bahan makanan belum terdaftar di sistem, sistem akan <strong>otomatis
                                            menambahkannya</strong> sebagai bahan baku baru ke katalog dapur.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-widest">Urutan Kolom:</p>
                            <ul class="space-y-2">
                                @php
                                    $cols = [
                                        'No',
                                        'Uraian Jenis Bahan Makanan',
                                        'Kuantitas',
                                        'Satuan',
                                        'Harga Satuan',
                                        'Jumlah',
                                        'Keterangan',
                                    ];
                                @endphp
                                @foreach ($cols as $i => $col)
                                    <li class="flex items-start gap-2 text-[13px] text-slate-600">
                                        <span
                                            class="w-5 h-5 rounded-full bg-slate-100 text-slate-400 text-[10px] font-black flex items-center justify-center shrink-0 mt-0.5">{{ $i + 1 }}</span>
                                        <span>{{ $col }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </x-card>

                <x-btn variant="outline" class="w-full py-4! rounded-2xl!"
                    href="{{ route('purchase-orders.download-template') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Template CSV
                </x-btn>
            </div>
        </div>
    </x-container>
    </x-admin-layout>
