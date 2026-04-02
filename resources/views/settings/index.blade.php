<x-app-layout title="{{ $title }}">
    <x-page-header :title="$title" :subtitle="$subtitle" />

    <div class="max-w-3xl">
        <x-card :padding="true">
            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                @if ($type === 'kitchen')
                    {{-- Kitchen Specific Fields --}}
                    <x-form-input label="Nama Unit Dapur" name="name" :value="old('name', $model->name)" required />
                    <x-form-textarea label="Alamat / Lokasi" name="address" :value="old('address', $model->address)" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-input label="Kota" name="city" :value="old('city', $model->city)" />
                        <x-form-input label="Provinsi" name="province" :value="old('province', $model->province)" />
                    </div>

                    <x-form-input label="Kapasitas Porsi (Per Sesi)" name="capacity_portions" type="number"
                        :value="old('capacity_portions', $model->capacity_portions)" />
                @else
                    {{-- Yayasan Specific Fields --}}
                    <x-form-input label="Nama Yayasan / Foundation" name="name" :value="old('name', $model->name)" required />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-input label="Email Yayasan" name="email" type="email" :value="old('email', $model->email)" />
                        <x-form-input label="Nomor Telepon" name="phone" :value="old('phone', $model->phone)" />
                    </div>

                    <x-form-textarea label="Alamat Pusat" name="address" :value="old('address', $model->address)" />

                    <div class="p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-[12px] text-slate-500 leading-relaxed">
                            <strong class="text-slate-700">Catatan:</strong> Pengaturan domain dan paket langganan
                            (plan) hanya dapat dikelola oleh administrator pusat melalui Central Portal.
                        </p>
                    </div>
                @endif

                <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-50">
                    @if (session('status') === 'settings-updated')
                        <p class="text-[12px] text-green-600 font-bold" x-data="{ show: true }" x-show="show"
                            x-init="setTimeout(() => show = false, 3000)" x-transition>Tersimpan!</p>
                    @endif
                    <x-btn type="submit">Simpan Perubahan</x-btn>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
