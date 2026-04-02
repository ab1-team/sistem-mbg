<div class="max-w-2xl mx-auto space-y-6 pb-24">
    <x-page-header title="{{ $expenseId ? 'Edit' : 'Catat' }} Pengeluaran" 
        subtitle="Input beban operasional dapur seperti gaji, listrik, dan biaya lainnya." />

    <x-card title="Rincian Pengeluaran" subtitle="Lampirkan bukti jika tersedia untuk keperluan audit.">
        <form wire:submit="save" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    @if(count($dapurs) > 1)
                        <x-form-searchable-select 
                            label="Dapur Penanggung" 
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
                        label="Periode" 
                        name="period_id" 
                        wire:model="period_id" 
                        :selected="$period_id"
                        :options="$periods->map(fn($p) => ['value' => (string)$p->id, 'label' => $p->name])"
                        placeholder="Pilih Periode"
                        required 
                    />
                </div>

                <div>
                    <x-form-select 
                        label="Kategori Beban" 
                        name="category" 
                        wire:model="category" 
                        :options="$categories"
                        placeholder="Pilih Kategori"
                        required 
                    />
                </div>

                <div>
                    <x-form-input 
                        label="Jumlah Beban (Rp)" 
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
                        label="Keterangan / Catatan" 
                        wire:model="notes" 
                        id="notes" 
                        name="notes" 
                        placeholder="Gaji koki bulan Januari atau Pembayaran PLN" 
                        rows="3" 
                    />
                </div>

                <div class="md:col-span-2">
                    <x-form-file 
                        label="Lampiran Bukti (Opsional)" 
                        wire:model="attachment" 
                        id="attachment" 
                        accept="image/*" 
                    />
                    @if($existingAttachment)
                        <div class="mt-2 text-[11px] font-bold text-slate-400">
                            Terlampir: <a href="{{ asset('storage/' . $existingAttachment) }}" target="_blank" class="text-blue-500 hover:underline">Lihat Bukti Saat Ini</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <x-btn href="{{ route('finance.expenses.index') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit" loading="true" loading-target="save" loading-text="Menyimpan...">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $expenseId ? 'Update' : 'Simpan' }} Pengeluaran
                </x-btn>
            </div>
        </form>
    </x-card>
</div>
