<x-app-layout title="Tambah Supplier">

    <x-page-header
        title="Tambah Supplier"
        subtitle="Isi detail identitas, kontak, dan informasi pembayaran supplier baru."
        :back="route('suppliers.index')"
        back-label="Mitra Supplier"
    />

    <x-card class="max-w-4xl">
        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Identitas Dasar --}}
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Identitas Dasar</p>
                <div class="grid grid-cols-2 gap-5">
                    <x-form-input label="Kode Supplier" name="code" :value="old('code')" placeholder="Contoh: SUP-BMB-01" required />
                    <x-form-searchable-select 
                        label="Kategori Vendor" 
                        name="category" 
                        :selected="old('category')"
                        :options="[
                            ['value' => 'Sayur', 'label' => 'Sayur & Buah'],
                            ['value' => 'Daging', 'label' => 'Daging & Ikan'],
                            ['value' => 'Bumbu', 'label' => 'Bumbu & Sembako'],
                            ['value' => 'Kering', 'label' => 'Barang Kering'],
                            ['value' => 'Lainnya', 'label' => 'Lainnya'],
                        ]"
                        required 
                    />
                    <div class="col-span-2">
                        <x-form-input label="Nama Perusahaan / Supplier" name="name" :value="old('name')" placeholder="Nama lengkap usaha" required />
                    </div>
                </div>
            </div>

            {{-- Kontak & Alamat --}}
            <div class="pt-5 border-t border-slate-50">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Kontak & Alamat</p>
                <div class="grid grid-cols-3 gap-5">
                    <x-form-input label="Nama Kontak (PIC)" name="contact_person" :value="old('contact_person')" placeholder="Nama orang lapangan" />
                    <x-form-input label="No. Telepon / WA" name="phone" :value="old('phone')" placeholder="081xxx" />
                    <x-form-input label="Email" name="email" type="email" :value="old('email')" placeholder="user@provider.com" />
                    <div class="col-span-3">
                        <x-form-textarea label="Alamat Supplier" name="address" :rows="3">{{ old('address') }}</x-form-textarea>
                    </div>
                </div>
            </div>

            {{-- Detail Pembayaran --}}
            <div class="pt-5 border-t border-slate-50">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Detail Pembayaran</p>
                <div class="grid grid-cols-3 gap-5">
                    <x-form-input label="Nama Bank" name="bank_name" :value="old('bank_name')" placeholder="BCA / Mandiri / dll" />
                    <x-form-input label="Nomor Rekening" name="bank_account" :value="old('bank_account')" placeholder="1234xxxx" />
                    <x-form-input label="Atas Nama (Pemilik)" name="bank_holder" :value="old('bank_holder')" placeholder="Nama di buku tabungan" />
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-200 text-green-900 focus:ring-green-900/20">
                    <span class="text-[13px] font-medium text-slate-700">Aktif (Dapat menerima PO)</span>
                </label>
                <div class="flex items-center gap-3">
                    <x-btn href="{{ route('suppliers.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Simpan Data
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

</x-app-layout>
