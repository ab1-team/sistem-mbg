<x-app-layout title="Edit Dapur">

    <x-page-header
        title="Edit Dapur"
        subtitle="Perbarui informasi dapur {{ $dapur->name }}."
        :back="route('dapurs.index')"
        back-label="Data Dapur"
    />

    <x-card class="max-w-3xl">
        <form action="{{ route('dapurs.update', $dapur) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-5 pb-6 border-b border-slate-50">
                <x-form-input label="Kode Dapur" name="code" :value="old('code', $dapur->code)" required />
                <x-form-input label="Nama Dapur" name="name" :value="old('name', $dapur->name)" required />
            </div>

            <div class="space-y-5 pb-6 border-b border-slate-50">
                <x-form-textarea label="Alamat Lengkap" name="address">{{ old('address', $dapur->address) }}</x-form-textarea>

                <div class="grid grid-cols-2 gap-5">
                    <x-form-input label="Kota" name="city" :value="old('city', $dapur->city)" />
                    <x-form-input label="Provinsi" name="province" :value="old('province', $dapur->province)" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <x-form-input
                    label="Kapasitas Produksi"
                    name="capacity_portions"
                    type="number"
                    :value="old('capacity_portions', $dapur->capacity_portions)"
                    hint="Jumlah porsi per hari."
                    required
                />

                <div class="flex flex-col justify-end pb-0.5 space-y-1.5">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_active"
                            id="is_active"
                            value="1"
                            {{ old('is_active', $dapur->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-200 text-green-900 focus:ring-green-900/20"
                        >
                        <span class="text-[13px] font-medium text-slate-700">Dapur Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50">
                <x-btn href="{{ route('dapurs.index') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </x-btn>
            </div>
        </form>
    </x-card>

</x-app-layout>
