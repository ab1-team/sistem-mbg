<x-app-layout title="Portofolio Investor">

    <div class="flex items-center justify-between mb-8">
        <x-page-header
            title="Portfolio Investor: {{ $investor->name }}"
            subtitle="Ringkasan porsi saham dan status dividen bagi mitra {{ $investor->code }}."
            class="mb-0"
        />
        <div class="flex items-center gap-3">
             <x-btn href="{{ route('investors.index') }}" variant="secondary">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                 Kembali
             </x-btn>
             <x-btn href="{{ route('investors.edit', $investor) }}">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                 Edit Portofolio
             </x-btn>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Informasi Identitas">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Kode Investor" :value="$investor->code" />
                    <x-show-field label="Nama Lengkap Investor" :value="$investor->name" />
                    <x-show-field label="Nomor Identitas (KTP/SK)" :value="$investor->identity_number ?: 'Belum diisi'" />
                    <x-show-field label="Status Keinvestasian">
                         @if($investor->is_active)
                             <x-badge variant="success">Aktif / Berhak Dividen</x-badge>
                         @else
                             <x-badge variant="error">Non-Aktif</x-badge>
                         @endif
                    </x-show-field>
                </div>
            </x-card>

            <x-card title="Data Pendanaan & Saham">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Persentase Saham">
                         <div class="flex items-center gap-2">
                             <div class="flex items-baseline gap-1">
                                 <span class="text-[24px] font-black text-slate-900 tracking-tighter">{{ number_format($investor->share_percentage, 2) }}</span>
                                 <span class="text-[12px] font-bold text-slate-400 uppercase tracking-widest">%</span>
                             </div>
                         </div>
                    </x-show-field>
                    <x-show-field label="Tanggal Bergabung" :value="$investor->join_date->format('d M Y')" />
                    <div class="md:col-span-2">
                         <x-show-field label="Total Dividen Terbayar" value="Rp 0,00" />
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-8">
            <x-card title="Audit & Kepatuhan">
                <div class="space-y-6">
                    <x-show-field label="Didaftarkan Oleh" value="Super Admin" />
                    <x-show-field label="Didaftarkan Pada" :value="$investor->created_at->format('d M Y, H:i')" />
                </div>
            </x-card>

            <div class="bg-brand-soft border border-emerald-100 rounded-3xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-[13px] font-bold text-emerald-900 capitalize">Perhatian</h4>
                        <p class="text-[12px] text-emerald-600 font-medium leading-relaxed mt-1">Status Non-Aktif akan menghentikan seluruh perhitungan pembagian laba harian untuk investor ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
