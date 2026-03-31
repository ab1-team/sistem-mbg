<div class="flex items-center gap-3">
    @if($menuPeriod->status === \App\Models\MenuPeriod::STATUS_DRAF || $menuPeriod->status === \App\Models\MenuPeriod::STATUS_REJECTED)
        <x-btn wire:click="submit" class="shadow-lg shadow-green-900/20">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7"/>
            </svg>
            Ajukan Approval
        </x-btn>
    @endif

    @if($menuPeriod->status === \App\Models\MenuPeriod::STATUS_PENDING)
        @can('manage-approvals') {{-- Admin Only --}}
            <x-btn wire:click="approve" class="bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-900/20">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
                Setujui Rencana
            </x-btn>
            <x-btn @click="$wire.set('showRejectModal', true)" variant="secondary" class="border-red-100 text-red-600 hover:bg-red-50">
                Tolak
            </x-btn>
        @endcan
    @endif

    {{-- REJECT MODAL --}}
    <div x-show="$wire.showRejectModal" 
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
        x-cloak>
        <div class="bg-white rounded-[32px] p-8 max-w-md w-full shadow-2xl border border-slate-100" @click.away="$wire.set('showRejectModal', false)">
            <h3 class="text-[20px] font-black text-slate-900 tracking-tight mb-2">Tolak Perencanaan</h3>
            <p class="text-[13px] text-slate-400 mb-6 leading-relaxed">Berikan alasan penolakan agar tim dapur dapat melakukan perbaikan yang diperlukan.</p>
            
            <div class="space-y-4">
                <x-form-textarea label="Catatan Penolakan" id="rejection_note" name="rejection_note" wire:model="rejection_note" placeholder="Contoh: Menu makan siang hari Senin kurang nutrisi protein..." required />

                <div class="flex gap-3 pt-2">

                    <x-btn @click="$wire.set('showRejectModal', false)" variant="secondary" class="flex-1">Batal</x-btn>
                    <x-btn wire:click="reject" class="flex-1 bg-red-600 hover:bg-red-700 shadow-lg shadow-red-900/20">Konfirmasi Tolak</x-btn>
                </div>
            </div>
        </div>
    </div>
</div>
