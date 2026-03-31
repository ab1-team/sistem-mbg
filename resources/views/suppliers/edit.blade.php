<x-app-layout title="Edit Supplier">

    <x-page-header
        title="Edit Supplier"
        subtitle="Perbarui informasi supplier {{ $supplier->name }}."
        :back="route('suppliers.index')"
        back-label="Mitra Supplier"
    />

    <x-card class="max-w-4xl">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Identitas Dasar --}}
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Identitas Dasar</p>
                <div class="grid grid-cols-2 gap-5">
                    <x-form-input label="Kode Supplier" name="code" :value="old('code', $supplier->code)" required />
                    <x-form-select label="Kategori Vendor" name="category" required>
                        @foreach(['Sayur' => 'Sayur & Buah', 'Daging' => 'Daging & Ikan', 'Bumbu' => 'Bumbu & Sembako', 'Kering' => 'Barang Kering', 'Lainnya' => 'Lainnya'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category', $supplier->category) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-form-select>
                    <div class="col-span-2">
                        <x-form-input label="Nama Perusahaan / Supplier" name="name" :value="old('name', $supplier->name)" required />
                    </div>
                </div>
            </div>

            {{-- Kontak & Alamat --}}
            <div class="pt-5 border-t border-slate-50">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Kontak & Alamat</p>
                <div class="grid grid-cols-3 gap-5">
                    <x-form-input label="Nama Kontak (PIC)" name="contact_person" :value="old('contact_person', $supplier->contact_person)" />
                    <x-form-input label="No. Telepon / WA" name="phone" :value="old('phone', $supplier->phone)" />
                    <x-form-input label="Email" name="email" type="email" :value="old('email', $supplier->email)" />
                    <div class="col-span-3">
                        <x-form-textarea label="Alamat Supplier" name="address" :rows="3">{{ old('address', $supplier->address) }}</x-form-textarea>
                    </div>
                </div>
            </div>

            {{-- Detail Pembayaran --}}
            <div class="pt-5 border-t border-slate-50">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Detail Pembayaran</p>
                <div class="grid grid-cols-3 gap-5">
                    <x-form-input label="Nama Bank" name="bank_name" :value="old('bank_name', $supplier->bank_name)" />
                    <x-form-input label="Nomor Rekening" name="bank_account" :value="old('bank_account', $supplier->bank_account)" />
                    <x-form-input label="Atas Nama (Pemilik)" name="bank_holder" :value="old('bank_holder', $supplier->bank_holder)" />
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-200 text-green-900 focus:ring-green-900/20">
                    <span class="text-[13px] font-medium text-slate-700">Aktif (Dapat menerima PO)</span>
                </label>
                <div class="flex items-center gap-3">
                    <x-btn href="{{ route('suppliers.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

</x-app-layout>
