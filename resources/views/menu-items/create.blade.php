<x-app-layout title="Tambah Menu">
    <x-container>
        <x-page-header 
            title="Buat Menu Baru" 
            subtitle="Susun resep masakan baru dan komposisi bahan bakunya."
            :back="route('menu-items.index')"
            back-label="Daftar Menu"
        />

        @livewire('menu-item-form')
    </x-container>
</x-app-layout>
