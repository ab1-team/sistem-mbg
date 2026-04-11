<x-app-layout title="Tambah Pendapatan">
    <x-container>
        <x-page-header title="{{ request()->routeIs('*.edit') ? 'Edit' : 'Entry' }} Pendapatan" 
            subtitle="Catat penerimaan dana dari sumber eksternal untuk operasional dapur." />

        <livewire:finance.revenue-form />
    </x-container>
</x-app-layout>
