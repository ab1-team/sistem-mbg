<x-app-layout title="Detail Periode">

    <div class="flex items-center justify-between mb-8">
        <x-page-header
            title="Detail Periode: {{ $period->name }}"
            subtitle="Informasi rentang waktu operasional dan status audit {{ $period->code }}."
            class="mb-0"
        />
        <div class="flex items-center gap-3">
             <x-btn href="{{ route('periods.index') }}" variant="secondary">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                 Kembali
             </x-btn>
             <x-btn href="{{ route('periods.edit', $period) }}">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                 Edit Periode
             </x-btn>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Informasi Periode">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Kode Periode" :value="$period->code" />
                    <x-show-field label="Nama Periode" :value="$period->name" />
                    <x-show-field label="Tanggal Mulai" :value="$period->start_date->format('d M Y')" />
                    <x-show-field label="Tanggal Selesai" :value="$period->end_date->format('d M Y')" />
                    <x-show-field label="Status Operasional">
                         @if($period->status == 'open')
                             <x-badge variant="success">OPEN / TRANSAKSI AKTIF</x-badge>
                         @elseif($period->status == 'closed')
                             <x-badge variant="error">CLOSED / TUTUP BUKU</x-badge>
                         @else
                             <x-badge variant="warning">LOCKED / AUDIT</x-badge>
                         @endif
                    </x-show-field>
                </div>
            </x-card>

            <x-card title="Informasi Lanjutan">
                <div class="grid grid-cols-1 gap-10">
                    <x-show-field label="Keterangan / Catatan Periode" :value="$period->description ?: 'Tidak ada catatan tambahan untuk periode ini.'" />
                </div>
            </x-card>
        </div>

        <div class="space-y-8">
            <x-card title="Timeline & Audit">
                <div class="space-y-6">
                    <x-show-field label="Dibuka Oleh" value="Super Admin" />
                    <x-show-field label="Sistem Status" value="Healthy" />
                    <x-show-field label="Dibuat Pada" :value="$period->created_at->format('d M Y, H:i')" />
                </div>
            </x-card>

            <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-6">
                <div class="flex items-start gap-4">
                     <div class="p-2 bg-indigo-100 text-indigo-700 rounded-xl">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                     </div>
                     <div>
                         <h4 class="text-[13px] font-black text-indigo-900 uppercase">Perhatian</h4>
                         <p class="text-[12px] text-indigo-600 font-medium leading-relaxed mt-1">Periode yang berstatus 'Closed' tidak dapat menerima transaksi baru untuk menjaga integritas data audit.</p>
                     </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
