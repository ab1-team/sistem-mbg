<x-app-layout title="Tambah Menu">

    <x-page-header 
        title="Buat Menu Baru" 
        subtitle="Susun resep masakan baru dan komposisi bahan bakunya."
        :back="route('menu-items.index')"
        back-label="Daftar Menu"
    />

    <div class="max-w-5xl">
        @livewire('menu-item-form')
    </div>

</x-app-layout>
