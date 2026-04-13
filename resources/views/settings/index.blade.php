<x-app-layout title="{{ $title }}">
    <x-container>
        <x-page-header :title="$title" :subtitle="$subtitle" />

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

                    <div class="pt-4 border-t border-slate-50">
                        <h5 class="text-[13px] font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Konfigurasi Bagi Hasil
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-form-input label="Bagian Yayasan (%)" name="profit_share_yayasan" type="number" 
                                :value="old('profit_share_yayasan', \App\Models\Setting::get('profit_share_yayasan', 20))" required />
                            <x-form-input label="Bagian Investor (%)" name="profit_share_investor" type="number" 
                                :value="old('profit_share_investor', \App\Models\Setting::get('profit_share_investor', 80))" required />
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2 font-medium italic">
                            * Persentase di atas akan digunakan sebagai rumus perhitungan laba bersih setiap kali periode ditutup. Total harus berjumlah 100%.
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
    </x-container>
</x-app-layout>
