<div class="max-w-2xl mx-auto space-y-6 pb-24">
    <x-page-header title="{{ $revenueId ? 'Edit' : 'Entry' }} Pendapatan" 
        subtitle="Catat penerimaan dana dari sumber eksternal untuk operasional dapur." />

    <x-card title="Detail Transaksi" subtitle="Pastikan jumlah dan periode sudah sesuai sebelum menyimpan.">
        <form wire:submit="save" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    @if(count($dapurs) > 1)
                        <x-form-searchable-select 
                            label="Dapur Penerima" 
                            name="dapur_id" 
                            wire:model="dapur_id" 
                            :selected="$dapur_id"
                            :options="$dapurs->map(fn($d) => ['value' => (string)$d->id, 'label' => $d->name])"
                            placeholder="Pilih Dapur"
                            required 
                        />
                    @else
                        <input type="hidden" wire:model="dapur_id">
                    @endif
                </div>

                <div>
                    <x-form-searchable-select 
                        label="Periode Keuangan" 
                        name="period_id" 
                        wire:model="period_id" 
                        :selected="$period_id"
                        :options="$periods->map(fn($p) => ['value' => (string)$p->id, 'label' => $p->name])"
                        placeholder="Pilih Periode"
                        required 
                    />
                </div>

                <div>
                    <x-form-input 
                        label="Jumlah Pendapatan (Rp)" 
                        type="number" 
                        wire:model="amount" 
                        id="amount" 
                        name="amount" 
                        placeholder="0" 
                        prefix="Rp"
                        required 
                    />
                </div>

                <div class="md:col-span-2">
                    <x-form-textarea 
                        label="Keterangan / Sumber" 
                        wire:model="notes" 
                        id="notes" 
                        name="notes" 
                        placeholder="Contoh: Dana operasional dari BGN tahap 1" 
                        rows="3" 
                    />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <x-btn href="{{ route('finance.revenues.index') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit" loading="true" loading-target="save" loading-text="Menyimpan...">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $revenueId ? 'Update' : 'Simpan' }} Pendapatan
                </x-btn>
            </div>
        </form>
    </x-card>

    @if(!$revenueId)
        <x-alert variant="info" title="Informasi">
            <p class="text-[12px] leading-relaxed">
                Pendapatan yang dicatat akan otomatis masuk ke perhitungan laba rugi pada periode yang dipilih. 
                Pastikan nominal sudah termasuk seluruh dana yang diterima.
            </p>
        </x-alert>
    @endif
</div>
