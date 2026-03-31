<x-app-layout title="Tambah Bahan Baku">

    <x-page-header 
        title="Tambah Bahan Baku" 
        subtitle="Masukkan detail bahan baku baru untuk kebutuhan dapur."
        :back="route('materials.index')"
        back-label="Daftar Bahan Baku"
    />

    <x-card class="max-w-3xl">
        <form action="{{ route('materials.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-5 pb-6 border-b border-slate-50">
                <x-form-input 
                    label="Kode Bahan" 
                    name="code" 
                    :value="old('code')" 
                    placeholder="Contoh: BB-BER-001" 
                    required 
                    hint="Gunakan kode unik untuk setiap bahan." 
                />
                <x-form-input 
                    label="Nama Bahan" 
                    name="name" 
                    :value="old('name')" 
                    placeholder="Contoh: Beras Ramos" 
                    required 
                />
            </div>

            <div class="grid grid-cols-2 gap-5 pb-6 border-b border-slate-50">
                <x-form-searchable-select 
                    label="Kategori" 
                    name="category" 
                    :selected="old('category')"
                    :options="collect($categories)->map(fn($cat) => ['value' => $cat, 'label' => ucfirst($cat)])->toArray()"
                    required 
                />

                <x-form-input 
                    label="Satuan" 
                    name="unit" 
                    :value="old('unit')" 
                    placeholder="Contoh: Kg, Liter, Butir" 
                    required 
                    hint="Satuan ukur terkecil."
                />
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 pb-6 border-b border-slate-50">
                <x-form-input label="Kalori (kcal)" name="calories" type="number" step="0.01" :value="old('calories', 0)" />
                <x-form-input label="Protein (g)" name="protein" type="number" step="0.01" :value="old('protein', 0)" />
                <x-form-input label="Karbo (g)" name="carbs" type="number" step="0.01" :value="old('carbs', 0)" />
                <x-form-input label="Lemak (g)" name="fat" type="number" step="0.01" :value="old('fat', 0)" />
                <x-form-input label="Serat (g)" name="fiber" type="number" step="0.01" :value="old('fiber', 0)" />
            </div>

            <div class="grid grid-cols-2 gap-5">
                <x-form-input 
                    label="Estimasi Harga" 
                    name="price_estimate" 
                    type="number" 
                    :value="old('price_estimate', 0)" 
                    required 
                    hint="Estimasi harga per satuan."
                />

                <x-form-input 
                    label="Batas Minimum Stok" 
                    name="min_stock_threshold" 
                    type="number" 
                    step="0.001"
                    :value="old('min_stock_threshold', 0)" 
                    required 
                    hint="Alert akan muncul jika stok di bawah ini."
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50">
                <x-btn href="{{ route('materials.index') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Simpan Bahan
                </x-btn>
            </div>
        </form>
    </x-card>

</x-app-layout>
