<x-app-layout title="Edit Menu">

    <x-page-header 
        title="Edit Masakan" 
        subtitle="Perbarui resep atau ganti komposisi bahan baku (BOM)."
        :back="route('menu-items.index')"
        back-label="Daftar Menu"
    />

    <div class="max-w-5xl">
        @livewire('menu-item-form', ['menuItem' => $menuItem])
    </div>

</x-app-layout>
