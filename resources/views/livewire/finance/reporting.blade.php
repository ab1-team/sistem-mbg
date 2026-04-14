<x-container class="max-w-4xl" x-data="{
    jenis_laporan: @entangle('jenis_laporan'),
    sub_laporan: @entangle('sub_laporan')
}">
    {{-- Header Section --}}
    <x-page-header title="Pusat Pelaporan Keuangan"
        subtitle="Pilih parameter laporan yang ingin Anda tinjau dalam format PDF profesional.">
        <x-slot name="actions">
            <x-btn wire:click="openReport" class="shadow-emerald-200/50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Preview PDF
            </x-btn>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Right Side: Selection Form --}}
        <div class="md:col-span-8 space-y-6">
            <x-card title="Parameter Laporan" class="overflow-visible">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Dapur --}}
                    @if($showDapurSelector)
                        <div class="md:col-span-3 border-b border-slate-50 pb-2">
                            <x-form-searchable-select wire:key="select-dapur" label="Filter Dapur (Unit)" name="dapur_id"
                                wire:model.live="dapur_id" :options="$opsiDapur" />
                        </div>
                    @endif

                    {{-- Tahun --}}
                    <div class="md:col-span-1">
                        <x-form-searchable-select wire:key="select-tahun" label="Tahun" name="tahun"
                            wire:model.live="tahun" :options="$opsiTahun" />
                    </div>

                    {{-- Bulan --}}
                    <div class="md:col-span-1 text-slate-800">
                        <x-form-searchable-select wire:key="select-bulan" label="Bulan" name="bulan"
                            wire:model.live="bulan" :options="$opsiBulan" />
                    </div>

                    {{-- Hari --}}
                    <div class="md:col-span-1" x-transition>
                        <x-form-searchable-select wire:key="select-periode" label="Tanggal" name="periode"
                            wire:model.live="periode" :options="$opsiHari" />
                    </div>

                    {{-- Laporan --}}
                    <div class="md:col-span-3 border-t border-slate-50">
                        <x-form-searchable-select wire:key="select-laporan" label="Nama Laporan" name="jenis_laporan"
                            wire:model.live="jenis_laporan" placeholder="-- Pilih Jenis Laporan --" :options="$opsiLaporan" />
                    </div>

                    {{-- Sub Laporan (Dynamic) --}}
                    @if ($jenis_laporan === 'bukuBesar')
                        <div class="md:col-span-3" x-transition>
                            <x-form-searchable-select wire:key="select-sub-laporan"
                                label="Pilih Rekening / Akun Sub Laporan" name="sub_laporan"
                                wire:model.live="sub_laporan" placeholder="Cari kode atau nama akun..."
                                :options="collect($daftarAkun)
                                    ->map(fn($a) => [
                                        'value' => $a->id, 
                                        'label' => $a->kode . ' — ' . $a->nama . ($dapur_id === 'all' && $a->dapur ? ' (' . $a->dapur->name . ')' : '')
                                    ])
                                    ->toArray()" />
                            <p class="mt-2 text-[11px] text-slate-400 leading-relaxed italic">
                                *Pilih akun spesifik untuk melihat rincian mutasi debit dan kredit secara mendetail.
                            </p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

        {{-- Left Side: Tips/Info --}}
        <div class="md:col-span-4 space-y-6">
            <x-card class="bg-slate-900 text-white border-none">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-[14px] font-black uppercase tracking-tight mb-2">Informasi Cetak</h3>
                <p class="text-[12px] text-slate-400 leading-relaxed font-medium">
                    Semua laporan digenerate dalam format <b>A4 (Portrait)</b>. Pastikan popup browser diizinkan untuk
                    melihat preview PDF secara otomatis.
                </p>
            </x-card>

            <div class="p-5 border-2 border-dashed border-slate-200 rounded-2xl">
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3">Tips Cepat</p>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5"></div>
                        <p class="text-[12px] text-slate-600 font-medium">Gunakan <b>Laba-Rugi</b> untuk melihat
                            keuntungan
                            bersih.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5"></div>
                        <p class="text-[12px] text-slate-600 font-medium">Gunakan <b>Neraca</b> untuk melihat posisi kas
                            dan aset.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- New Tab Opener --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('open-new-tab', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
</x-container>
