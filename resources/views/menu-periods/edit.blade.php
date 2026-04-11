<x-app-layout title="Edit Rencana Menu">
    <x-container>
        <x-page-header 
            title="Edit Rencana Menu" 
            subtitle="Perbarui jadwal menu harian untuk periode ini."
            :back="route('menu-periods.index')"
        />

        <livewire:menu-period-form :menu-period-id="$menuPeriod->id" />
    </x-container>
</x-app-layout>
