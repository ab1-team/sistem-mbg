<x-container>
    <x-page-header title="Input Jurnal Umum"
        subtitle="Catat transaksi keuangan (Aset Masuk, Keluar, atau Pemindahan Saldo) untuk laporan buku besar." />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Main Form Section --}}
        <div class="lg:col-span-3">
            <x-card padding="false" class="overflow-visible">
                <form wire:submit.prevent="save" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                        {{-- Smart Dapur Selector --}}
                        @if ($showDapurSelector)
                            <div class="md:col-span-2 pb-2">
                                <x-form-searchable-select label="Dapur" name="dapur_id" wire:model.live="dapur_id"
                                    :options="$dapurOptions" required />
                                <div class="mt-4 border-b border-slate-100"></div>
                            </div>
                        @endif

                        {{-- Row 1 --}}
                        <x-datepicker label="Tanggal Transaksi" name="tanggal_transaksi"
                            wire:model.live="tanggal_transaksi" :value="$tanggal_transaksi" required />

                        <x-form-searchable-select label="Jenis Transaksi" name="jenis_transaksi"
                            wire:model.live="jenis_transaksi" :options="$jenisTransaksiOptions" required />

                        {{-- Row 2 --}}
                        <div wire:key="sumber-dana-select-{{ $jenis_transaksi }}">
                            <x-form-searchable-select
                                label="{{ $jenis_transaksi === 'aset_masuk' ? 'Sumber Pemasukan' : ($jenis_transaksi === 'aset_keluar' ? 'Dari Rekening' : 'Rekening Sumber') }}"
                                name="sumber_dana" wire:model.live="sumber_dana" :options="$sourceOptions" required />
                        </div>

                        <div wire:key="disimpan-ke-select-{{ $jenis_transaksi }}">
                            <x-form-searchable-select
                                label="{{ $jenis_transaksi === 'aset_masuk' ? 'Simpan Ke Rekening' : ($jenis_transaksi === 'aset_keluar' ? 'Untuk Beban' : 'Rekening Tujuan') }}"
                                name="disimpan_ke" wire:model.live="disimpan_ke" :options="$targetOptions" required />
                        </div>

                        {{-- Row 3 --}}
                        @if ($showRelasi)
                            <x-form-input label="Relasi" name="relasi" wire:model="relasi"
                                placeholder="e.g. Nama Supplier, No Invoice, dll" />
                        @endif

                        <div class="{{ !$showRelasi ? 'md:col-span-2' : '' }}">
                            <x-form-textarea label="Keterangan" name="keterangan" wire:model="keterangan" rows="1"
                                placeholder="Detail transaksi..." required />
                        </div>

                        {{-- Alert Form Inventaris --}}
                        @if ($showInventaris)
                            <div
                                class="md:col-span-2 bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-center gap-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <span class="text-[12px] font-medium text-blue-800 italic">Informasi: Anda memilih akun
                                    Aset Tetap. Form Inventaris akan terbuka otomatis setelah jurnal ini
                                    disimpan.</span>
                            </div>
                        @endif

                        {{-- Row 4: Nominal --}}
                        <div class="md:col-span-2 mt-2">
                            <x-form-currency label="Nominal Rp." name="nominal" wire:model="nominal" required />
                        </div>


                    </div>

                    {{-- Footer Actions --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end">
                        <x-btn type="submit" variant="primary" loading="true" loadingTarget="save"
                            loadingText="Menyimpan...">
                            Simpan Transaksi
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Sidebar Section --}}
        <div class="lg:col-span-1">
            <x-card>
                <div class="flex items-center justify-between mb-1" wire:loading.class="opacity-50">
                    <span class="text-[13px] text-slate-500 font-medium">Saldo</span>
                    <div class="flex flex-col items-end">
                        <span class="text-[15px] font-semibold text-slate-900" wire:key="saldo-display">
                            Rp. {{ number_format($saldo, 0, ',', '.') }}
                        </span>
                        <div wire:loading wire:target="calculateSaldo"
                            class="text-[10px] text-emerald-600 font-bold italic">Menghitung...</div>
                    </div>
                </div>
            </x-card>

            @if (session()->has('success'))
                <div class="mt-4 bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-start gap-3">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-[12px] font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mt-4 bg-red-50 border border-red-100 p-4 rounded-xl flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <p class="text-[12px] font-medium text-red-800">{{ session('error') }}</p>
                </div>
            @endif
        </div>

    </div>
</x-container>
