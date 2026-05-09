<x-container>
    <x-page-header title="Input Distribusi Bagi Hasil"
        subtitle="Catat pembagian keuntungan untuk investor dan yayasan ke dalam laporan keuangan." 
        :back="route('finance.profit-sharing.index')"
    />

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Side: Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Data Dasar Pembagian" subtitle="Tentukan periode dan dapur untuk menghitung laba.">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-datepicker label="Tanggal Pencatatan" name="tanggal_distribusi" wire:model="tanggal_distribusi" required />
                        
                        <x-form-searchable-select label="Dapur" name="dapur_id" wire:model.live="dapur_id" :options="$dapurOptions" required />
                        
                        <x-form-searchable-select label="Periode Keuangan (Opsional)" name="period_id" wire:model.live="period_id" :options="$periodOptions" 
                            hint="Pilih periode untuk otomatis mengambil data laba." />

                        <x-form-currency label="Laba Bersih (Net Profit)" name="net_profit" wire:model.live.debounce.500ms="net_profit" required 
                            hint="Nilai laba yang akan dibagikan." />
                    </div>
                </x-card>

                <x-card title="Rincian Pembagian Investor" subtitle="Tentukan nominal spesifik yang diterima setiap investor.">
                    <div class="overflow-hidden border border-slate-100 rounded-2xl">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Investor</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Saham</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Nominal Bagi Hasil</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($investors as $investor)
                                    <tr wire:key="investor-{{ $investor->id }}">
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col">
                                                <span class="text-[13px] font-bold text-slate-700">{{ $investor->name }}</span>
                                                <span class="text-[10px] text-slate-400 uppercase font-medium">{{ $investor->user?->email }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600">
                                                {{ number_format($investor->share_percentage, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex justify-end">
                                                <div class="w-48">
                                                    <x-form-currency 
                                                        name="investor_distributions.{{ $investor->id }}" 
                                                        wire:model.live="investor_distributions.{{ $investor->id }}" 
                                                        prefix="Rp" 
                                                        no-label="true"
                                                    />
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

            {{-- Right Side: Summary Cards --}}
            <div class="space-y-6">
                <x-card title="Ringkasan Alokasi" class="sticky top-6">
                    <div class="space-y-4">
                        {{-- Yayasan Pool --}}
                        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                            <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1">Bagian Yayasan ({{ $yayasan_share_percentage }}%)</p>
                            <p class="text-xl font-black text-blue-900 tracking-tighter">Rp {{ number_format($yayasan_share, 0, ',', '.') }}</p>
                        </div>

                        {{-- Investor Pool --}}
                        <div class="p-4 bg-orange-50 rounded-2xl border border-orange-100">
                            <p class="text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Pool Investor ({{ $investor_share_percentage }}%)</p>
                            <p class="text-xl font-black text-orange-900 tracking-tighter">Rp {{ number_format($investor_pool, 0, ',', '.') }}</p>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[12px] font-medium text-slate-500">Terdistribusi ke Investor:</span>
                                <span class="text-[12px] font-bold text-slate-900">Rp {{ number_format(array_sum($investor_distributions), 0, ',', '.') }}</span>
                            </div>
                            
                            @php
                                $diff = $investor_pool - array_sum($investor_distributions);
                            @endphp

                            <div class="flex justify-between items-center">
                                <span class="text-[12px] font-medium text-slate-500">Selisih:</span>
                                <span class="text-[12px] font-bold {{ abs($diff) < 1 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($diff, 0, ',', '.') }}
                                </span>
                            </div>

                            @if(abs($diff) >= 1)
                                <div class="mt-4 p-3 bg-rose-50 border border-rose-100 rounded-xl">
                                    <p class="text-[10px] text-rose-700 leading-relaxed font-medium">
                                        <svg class="w-3 h-3 inline mr-1 mb-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                        Total distribusi ke investor tidak sama dengan Pool Investor. Pastikan nominal sudah benar.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="pt-6">
                            <x-btn type="submit" class="w-full justify-center py-3" loading="true" loading-target="save">
                                Simpan & Catat Jurnal
                            </x-btn>
                        </div>
                    </div>
                </x-card>

                <x-alert variant="info" title="Informasi Jurnal Otomatis">
                    <p class="text-[11px] leading-relaxed">
                        Sistem akan otomatis mencatatkan pengeluaran bagi hasil pada akun <strong>Ikhtisar Laba Rugi Berjalan (3.2.01.02)</strong> dan mengkreditkan ke akun <strong>Hutang Dividen (2.1.03.03)</strong> serta <strong>Hutang Yayasan (2.1.04.01)</strong>.
                    </p>
                </x-alert>
            </div>
        </div>
    </form>
</x-container>
