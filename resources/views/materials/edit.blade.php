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

                {{-- Konfigurasi Supplier --}}
                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Konfigurasi Supplier</p>
                        <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-bold">Dapat disuplai oleh:</span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        @foreach($suppliers as $supplier)
                            <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-xl hover:border-green-600 hover:bg-green-50/30 transition-all cursor-pointer group">
                                <div class="relative flex items-center pt-0.5">
                                    <input type="checkbox" name="suppliers[]" value="{{ $supplier->id }}" 
                                        {{ in_array($supplier->id, old('suppliers', $material->suppliers->pluck('id')->toArray())) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-slate-300 text-green-700 focus:ring-green-900/20 transition-all cursor-pointer">
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-bold text-slate-700 group-hover:text-green-900 transition-colors">{{ $supplier->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">{{ $supplier->category }}</span>
                                </div>
                            </label>
                        @endforeach

                        @if($suppliers->isEmpty())
                            <div class="col-span-full py-6 text-center">
                                <p class="text-[12px] text-slate-400 font-bold italic">Belum ada data supplier yang tersedia.</p>
                                <a href="{{ route('suppliers.create') }}" class="text-green-700 hover:underline text-[11px] font-bold mt-1 inline-block">Tambah Supplier Baru</a>
                            </div>
                        @endif
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium italic">Centang supplier yang dapat menyediakan bahan baku ini.</p>
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
