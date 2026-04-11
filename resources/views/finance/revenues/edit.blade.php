@props(['revenue'])
<x-app-layout title="Edit Pendapatan">
    <x-container>
        <x-page-header title="Edit Pendapatan" 
            subtitle="Perbarui catatan penerimaan dana dari sumber eksternal." />

        <livewire:finance.revenue-form :revenueId="$revenue->id" />
    </x-container>
</x-app-layout>
