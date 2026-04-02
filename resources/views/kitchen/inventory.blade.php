<x-app-layout title="Stok Dapur">
    <x-page-header title="Inventaris Unit Dapur" subtitle="{{ $dapur->name }} — Real-time Stock Monitoring"
        :back="route('kitchen.index')" backLabel="Dashboard Masak">
        <x-slot name="actions">
            @if (auth()->user()->hasRole('superadmin') && $allDapurs->count() > 1)
                <div class="w-[240px]">
                    <x-form-searchable-select name="dapur_id" :options="$allDapurs->map(fn($d) => ['value' => $d->id, 'label' => $d->name])->toArray()" :selected="$dapur->id"
                        placeholder="Cari Unit Dapur..."
                        class="border-none shadow-none bg-slate-50/50 hover:bg-white transition-all font-black text-slate-900"
                        onSelected="window.location.href = '?dapur_id=' + opt.value" />
                </div>
            @endif
        </x-slot>
    </x-page-header>

    <livewire:kitchen-inventory-table :dapur_id="$dapur->id" />
</x-app-layout>
