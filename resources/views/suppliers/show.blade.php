<x-app-layout title="Profil Supplier">

    <div class="flex items-center justify-between mb-8">
        <x-page-header
            title="Profil Supplier: {{ $supplier->name }}"
            subtitle="Informasi kemitraan pengadaan bahan baku {{ $supplier->code }}."
            class="mb-0"
        />
        <div class="flex items-center gap-3">
             <x-btn href="{{ route('suppliers.index') }}" variant="secondary">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                 Kembali
             </x-btn>
             <x-btn href="{{ route('suppliers.edit', $supplier) }}">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                 Edit Profil
             </x-btn>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Informasi Supplier">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Kode Supplier" :value="$supplier->code" />
                    <x-show-field label="Nama Perusahaan/Toko" :value="$supplier->name" />
                    <x-show-field label="Nama Kontak (PIC)" :value="$supplier->contact_name ?: '-'" />
                    <x-show-field label="Nomor Telepon">
                         <div class="flex items-center gap-2 text-green-700 font-bold">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                             {{ $supplier->phone ?: '-' }}
                         </div>
                    </x-show-field>
                </div>
            </x-card>

            <x-card title="Alamat Penagihan & Logistik">
                <div class="grid grid-cols-1 gap-10">
                    <x-show-field label="Alamat Lengkap" :value="$supplier->address ?: 'Alamat belum diinput.'" />
                </div>
            </x-card>
        </div>

        <div class="space-y-8">
            <x-card title="Data Kemitraan">
                <div class="space-y-6">
                    <x-show-field label="Mulai Bekerjasama" :value="$supplier->created_at->format('d M Y')" />
                    <x-show-field label="Total Transaksi" value="Rp 0,00" />
                </div>
            </x-card>

            <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-6">
                <div class="flex items-start gap-4">
                     <div class="p-2 bg-indigo-100 text-indigo-700 rounded-xl">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                     </div>
                     <div>
                         <h4 class="text-[13px] font-black text-indigo-900 uppercase">Status Aktif</h4>
                         <p class="text-[12px] text-indigo-600 font-medium leading-relaxed mt-1">Supplier ini terverifikasi aktif untuk pengadaan barang Yayasan MBG.</p>
                     </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
