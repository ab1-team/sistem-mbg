@props(['expense'])
<x-app-layout title="Edit Pengeluaran">
    <livewire:finance.expense-form :expenseId="$expense->id" />
</x-app-layout>
