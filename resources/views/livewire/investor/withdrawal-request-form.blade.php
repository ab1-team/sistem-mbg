<x-container class="pb-20">
    <x-page-header title="Request Penarikan Dana" subtitle="Cairkan saldo bagi hasil Anda ke rekening terdaftar." />

    <x-card title="Rincian Penarikan" subtitle="Saldo akan dikurangi setelah pengajuan disetujui admin.">
        <form wire:submit="save" class="space-y-6">
            {{-- Balance Info --}}
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Saldo Saat Ini</p>
                    <p class="text-[18px] font-black text-green-700">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="space-y-4">
                <x-form-currency 
                    label="Jumlah Penarikan" 
                    wire:model="amount" 
                    id="amount" 
                    name="amount" 
                    placeholder="0" 
                    prefix="Rp"
                    required 
                />

                <x-form-textarea 
                    label="Catatan (Opsional)" 
                    wire:model="notes" 
                    id="notes" 
                    name="notes" 
                    placeholder="Contoh: Kebutuhan darurat" 
                    rows="3" 
                />
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <x-btn href="{{ route('investor.dashboard') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit" loading="true" loading-target="save" loading-text="Mengajukan...">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                    Ajukan Penarikan
                </x-btn>
            </div>
        </form>
    </x-card>

    <x-alert variant="info" title="Penting">
        <p class="text-[11px] leading-relaxed">
            Proses verifikasi penarikan biasanya memakan waktu 1-3 hari kerja. Dana akan ditransfer ke rekening bank yang terdaftar di profil investor Anda.
        </p>
    </x-alert>
</x-container>
