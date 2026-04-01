<x-app-layout title="Detail Dapur">

    <div class="flex items-center justify-between mb-8">
        <x-page-header
            title="Detail Dapur: {{ $dapur->name }}"
            subtitle="Informasi lengkap unit operasional {{ $dapur->code }}."
            class="mb-0"
        />
        <div class="flex items-center gap-3">
             <x-btn href="{{ route('dapurs.index') }}" variant="secondary">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                 Kembali
             </x-btn>
             <x-btn href="{{ route('dapurs.edit', $dapur) }}">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                 Edit Data
             </x-btn>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Informasi Utama">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Kode Dapur" :value="$dapur->code" />
                    <x-show-field label="Nama Dapur" :value="$dapur->name" />
                    <x-show-field label="Kapasitas (Porsi)" :value="number_format($dapur->capacity_portions) . ' Porsi'" />
                    <x-show-field label="Status">
                         @if($dapur->is_active)
                             <x-badge variant="success">Aktif / Beroperasi</x-badge>
                         @else
                             <x-badge variant="error">Non-Aktif</x-badge>
                         @endif
                    </x-show-field>
                </div>
            </x-card>

            <x-card title="Lokasi & Alamat">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Provinsi" :value="$dapur->province ?: '-'" />
                    <x-show-field label="Kota/Kabupaten" :value="$dapur->city ?: '-'" />
                    <div class="md:col-span-2">
                        <x-show-field label="Alamat Lengkap" :value="$dapur->address ?: 'Alamat belum diinput.'" />
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-8">
            <x-card title="Audit & Log">
                <div class="space-y-6">
                    <x-show-field label="Dibuat Pada" :value="$dapur->created_at->format('d M Y, H:i')" />
                    <x-show-field label="Terakhir Diupdate" :value="$dapur->updated_at->format('d M Y, H:i')" />
                </div>
            </x-card>

            <div class="bg-brand-soft border border-emerald-100 rounded-3xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-[13px] font-bold text-emerald-900 capitalize">Statistik Unit</h4>
                        <p class="text-[12px] text-emerald-600 font-medium leading-relaxed mt-1">Dapur ini merupakan salah satu unit operasional utama Yayasan MBG.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
