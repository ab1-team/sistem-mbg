<x-app-layout title="Profil Supplier">
    <x-container>
        <div class="flex items-center justify-between">
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
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Informasi Supplier" subtitle="Detail identitas dan kontak mitra.">
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

                <x-card title="Alamat Penagihan & Logistik" subtitle="Lokasi operasional supplier.">
                    <div class="grid grid-cols-1 gap-10">
                        <x-show-field label="Alamat Lengkap" :value="$supplier->address ?: 'Alamat belum diinput.'" />
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Data Kemitraan">
                    <div class="space-y-6">
                        <x-show-field label="Mulai Bekerjasama" :value="$supplier->created_at->format('d M Y')" />
                        <x-show-field label="Total Transaksi" value="Rp 0,00" />
                    </div>
                </x-card>

                <div class="bg-emerald-50/50 border border-emerald-100 rounded-3xl p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white text-emerald-600 flex items-center justify-center shadow-sm border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-[14px] font-black text-emerald-900 capitalize">Status Aktif</h4>
                            <p class="text-[12px] text-emerald-600 font-bold leading-relaxed mt-0.5">Supplier terverifikasi aktif.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <h3 class="text-[20px] font-black text-slate-900 mb-6 flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-brand-soft text-brand flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
                Manajemen Sub-Supplier
            </h3>
            
            <livewire:sub-supplier-manager :supplier="$supplier" />
        </div>
    </x-container>
</x-app-layout>
