@props(['revenue'])
<x-app-layout title="Edit Pendapatan">
    <livewire:finance.revenue-form :revenueId="$revenue->id" />
</x-app-layout>
