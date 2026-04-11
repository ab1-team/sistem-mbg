<x-app-layout title="Edit Supplier">
    <x-container>
        <x-page-header
            title="Edit Supplier"
            subtitle="Perbarui informasi supplier {{ $supplier->name }}."
            :back="route('suppliers.index')"
            back-label="Mitra Supplier"
        />

        <x-card title="Edit Informasi" subtitle="Pembaruan detail mitra supplier.">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Identitas Dasar --}}
                <div class="space-y-4">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Identitas Dasar</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 py-4 border-y border-slate-50">
                        <x-form-input label="Kode Supplier" name="code" :value="old('code', $supplier->code)" placeholder="Contoh: SUP-BMB-01" required />
                        <x-form-searchable-select 
                            label="Kategori Vendor" 
                            name="category" 
                            :selected="old('category', $supplier->category)"
                            :options="[
                                ['value' => 'sayuran', 'label' => 'Sayur & Buah'],
                                ['value' => 'daging', 'label' => 'Daging & Ikan'],
                                ['value' => 'bumbu', 'label' => 'Bumbu'],
                                ['value' => 'sembako', 'label' => 'Sembako & Barang Kering'],
                                ['value' => 'lainnya', 'label' => 'Lainnya'],
                            ]"
                            required 
                        />
                        <div class="md:col-span-2">
                            <x-form-input label="Nama Perusahaan / Supplier" name="name" :value="old('name', $supplier->name)" placeholder="Nama lengkap usaha" required />
                        </div>
                    </div>
                </div>

                {{-- Kontak & Alamat --}}
                <div class="space-y-4 pt-4">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Kontak & Alamat</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 py-4 border-y border-slate-50">
                        <x-form-input label="Nama Kontak (PIC)" name="contact_person" :value="old('contact_person', $supplier->contact_person)" placeholder="Nama orang lapangan" />
                        <x-form-input label="No. Telepon / WA" name="phone" :value="old('phone', $supplier->phone)" placeholder="081xxx" />
                        <x-form-input label="Email" name="email" type="email" :value="old('email', $supplier->email)" placeholder="user@provider.com" />
                        <div class="md:col-span-3">
                            <x-form-textarea label="Alamat Supplier" name="address" :rows="3" placeholder="Alamat lengkap operasional">{{ old('address', $supplier->address) }}</x-form-textarea>
                        </div>
                    </div>
                </div>

                {{-- Detail Pembayaran --}}
                <div class="space-y-4 pt-4">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Detail Pembayaran</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 py-4 border-y border-slate-50">
                        <x-form-input label="Nama Bank" name="bank_name" :value="old('bank_name', $supplier->bank_name)" placeholder="BCA / Mandiri / dll" />
                        <x-form-input label="Nomor Rekening" name="bank_account" :value="old('bank_account', $supplier->bank_account)" placeholder="1234xxxx" />
                        <x-form-input label="Atas Nama (Pemilik)" name="bank_holder" :value="old('bank_holder', $supplier->bank_holder)" placeholder="Nama di buku tabungan" />
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 border-t border-slate-100">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded-lg border-slate-300 text-green-700 focus:ring-green-900/20">
                        <span class="text-[13px] font-bold text-slate-600">Aktif (Dapat menerima Pesanan)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <x-btn href="{{ route('suppliers.index') }}" variant="secondary">Batal</x-btn>
                        <x-btn type="submit">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </x-btn>
                    </div>
                </div>
            </form>
        </x-card>
    </x-container>
</x-app-layout>
