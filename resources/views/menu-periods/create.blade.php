<x-app-layout title="Buat Rencana Menu">
    <x-container>
        <x-page-header 
            title="Buat Rencana Menu Baru" 
            subtitle="Pilih unit dapur dan periode untuk mulai menjadwalkan menu harian."
            :back="route('menu-periods.index')"
        />

        <livewire:menu-period-form />
    </x-container>
</x-app-layout>
