@props(['expense'])
<x-app-layout title="Edit Pengeluaran">
    <x-container>
        <x-page-header title="Edit Pengeluaran" 
            subtitle="Perbarui catatan beban operasional dapur." />

        <livewire:finance.expense-form :expenseId="$expense->id" />
    </x-container>
</x-app-layout>
