<x-app-layout title="Tambah Dapur">

    <x-page-header
        title="Tambah Dapur"
        subtitle="Isi detail lokasi dan kapasitas dapur baru."
        :back="route('dapurs.index')"
        back-label="Data Dapur"
    />

    <x-card class="max-w-3xl">
        <form action="{{ route('dapurs.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-5 pb-6 border-b border-slate-50">
                <x-form-input label="Kode Dapur" name="code" :value="old('code')" placeholder="Contoh: DPR-JKT-001" required hint="Gunakan kode unik per lokasi." />
                <x-form-input label="Nama Dapur" name="name" :value="old('name')" placeholder="Contoh: Dapur Utama Jakarta" required />
            </div>

            <div class="space-y-5 pb-6 border-b border-slate-50">
                <x-form-textarea label="Alamat Lengkap" name="address" placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan...">{{ old('address') }}</x-form-textarea>

                <div class="grid grid-cols-2 gap-5">
                    <x-form-input label="Kota" name="city" :value="old('city')" placeholder="Contoh: Jakarta Selatan" />
                    <x-form-input label="Provinsi" name="province" :value="old('province')" placeholder="Contoh: DKI Jakarta" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <x-form-input
                    label="Kapasitas Produksi"
                    name="capacity_portions"
                    type="number"
                    :value="old('capacity_portions', 0)"
                    hint="Jumlah porsi yang bisa diproduksi per hari."
                    required
                />

                <div class="space-y-1.5 flex flex-col justify-end pb-0.5">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_active"
                            id="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-200 text-green-900 focus:ring-green-900/20"
                        >
                        <span class="text-[13px] font-medium text-slate-700">Dapur Aktif (Siap Operasional)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50">
                <x-btn href="{{ route('dapurs.index') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Simpan Data
                </x-btn>
            </div>
        </form>
    </x-card>

</x-app-layout>
