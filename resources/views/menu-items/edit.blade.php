<x-app-layout title="Edit Menu">
    <x-container>
        <x-page-header 
            title="Edit Masakan" 
            subtitle="Perbarui resep atau ganti komposisi bahan baku (BOM)."
            :back="route('menu-items.index')"
            back-label="Daftar Menu"
        />

        @livewire('menu-item-form', ['menuItem' => $menuItem])
    </x-container>
</x-app-layout>
