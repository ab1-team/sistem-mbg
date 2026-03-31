<x-app-layout title="Edit Rencana Menu">
    <div class="mb-6">
        <a href="{{ route('menu-periods.index') }}" class="inline-flex items-center text-[13px] font-bold text-slate-400 hover:text-green-700 transition-colors mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">Edit Rencana Menu</h1>
        <p class="text-[13px] text-slate-400 mt-2">Perbarui jadwal menu harian untuk periode ini.</p>
    </div>

    <livewire:menu-period-form :menu-period-id="$menuPeriod->id" />
</x-app-layout>
