<x-app-layout title="Edit Bahan Baku">
    <x-container>
        <x-page-header 
            title="Edit Bahan Baku" 
            subtitle="Perbarui data bahan baku untuk kebutuhan operasional."
            :back="route('materials.index')"
            back-label="Daftar Bahan Baku"
        />

        <x-card title="Edit Informasi" subtitle="Pembaruan detail bahan baku.">
            <form action="{{ route('materials.update', $material) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pb-6 border-b border-slate-100">
                    <x-form-input label="Kode Bahan" name="code" :value="old('code', $material->code)" placeholder="Contoh: BB-BER-001" required hint="Gunakan kode unik untuk setiap bahan." />
                    <x-form-input label="Nama Bahan" name="name" :value="old('name', $material->name)" placeholder="Contoh: Beras Ramos" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pb-6 border-b border-slate-100">
                    <x-form-searchable-select label="Kategori" name="category" :selected="old('category', $material->category)" :options="collect($categories)->map(fn($cat) => ['value' => $cat, 'label' => ucfirst($cat)])->toArray()" required />
                    @if(count($dapurs) > 1)
                        <x-form-searchable-select label="Dapur (Opsional)" name="dapur_id" :selected="old('dapur_id', $material->dapur_id)" :options="collect($dapurs)->map(fn($d) => ['value' => $d->id, 'label' => $d->name])->prepend(['value' => '', 'label' => 'Global (Semua Dapur)'])->toArray()" hint="Pilih jika bahan ini hanya ada di dapur tertentu." />
                    @else
                        <input type="hidden" name="dapur_id" value="{{ $dapurs->first()?->id }}">
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pb-6 border-b border-slate-100">
                    <x-form-input label="Satuan" name="unit" :value="old('unit', $material->unit)" placeholder="Contoh: Kg, Liter, Butir" required hint="Satuan ukur terkecil." />
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 pb-6 border-b border-slate-100">
                    <x-form-input label="Kalori (kcal)" name="calories" type="number" step="0.01" :value="old('calories', $material->calories)" />
                    <x-form-input label="Protein (g)" name="protein" type="number" step="0.01" :value="old('protein', $material->protein)" />
                    <x-form-input label="Karbo (g)" name="carbs" type="number" step="0.01" :value="old('carbs', $material->carbs)" />
                    <x-form-input label="Lemak (g)" name="fat" type="number" step="0.01" :value="old('fat', $material->fat)" />
                    <x-form-input label="Serat (g)" name="fiber" type="number" step="0.01" :value="old('fiber', $material->fiber)" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-form-input label="Estimasi Harga" name="price_estimate" type="number" :value="old('price_estimate', $material->price_estimate)" required hint="Estimasi harga per satuan." />
                    <x-form-input label="Batas Minimum Stok" name="min_stock_threshold" type="number" step="0.001" :value="old('min_stock_threshold', $material->min_stock_threshold)" required hint="Alert akan muncul jika stok di bawah ini." />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <x-btn href="{{ route('materials.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Perbarui Bahan
                    </x-btn>
                </div>
            </form>
        </x-card>
    </x-container>
</x-app-layout>
