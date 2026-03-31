<x-app-layout title="Stok Dapur">
    <x-page-header 
        title="Inventaris Unit Dapur" 
        subtitle="{{ $dapur->name }} — Real-time Stock Monitoring"
        :back="route('kitchen.index')"
        backLabel="Dashboard Masak"
    />

    <livewire:kitchen-inventory-table />
</x-app-layout>
