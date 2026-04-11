<x-app-layout title="Edit Periode">
    <x-container>
        <x-page-header title="Detail Periode"
            subtitle="Perbarui rentang tanggal dan status operasional periode {{ $period->name }}." :back="route('periods.index')"
            back-label="Data Periode" />

        <x-card class="{{ $period->status != 'open' ? 'opacity-80 grayscale-[0.1]' : '' }}" title="Informasi Periode" subtitle="Pembaruan status dan identitas periode.">
            <form action="{{ route('periods.update', $period) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-white text-slate-900 flex items-center justify-center font-black text-[14px] border border-slate-200 shadow-sm uppercase">
                            {{ $period->code }}
                        </div>
                        <div>
                            <h3 class="text-[14px] font-black text-slate-900 uppercase tracking-tight">{{ $period->name }}</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <div class="w-2 h-2 rounded-full {{ $period->status == 'open' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                    Status: <span class="text-slate-900">{{ strtoupper($period->status) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <x-form-input label="Nama Tampilan Periode" name="name" :value="old('name', $period->name)" required />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-2">
                    <x-datepicker label="Tanggal Mulai" name="start_date" :value="old('start_date', $period->start_date->format('Y-m-d'))" required />
                    <x-datepicker label="Tanggal Selesai" name="end_date" :value="old('end_date', $period->end_date->format('Y-m-d'))" required />
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <x-form-searchable-select label="Status Periode" name="status" :selected="old('status', $period->status)" :options="[
                        ['value' => 'open', 'label' => 'OPEN (Aktif Transaksi)'],
                        ['value' => 'closed', 'label' => 'CLOSED (Tutup Buku - Siap Audit)'],
                        ['value' => 'locked', 'label' => 'LOCKED (Terkunci - Hanya Superadmin)'],
                    ]"
                        required />
                    <p class="mt-2 text-[11px] text-slate-400 italic">Periode yang sudah CLOSED akan mencatat waktu
                        penutupan dan user yang melakukan aksi.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <x-btn href="{{ route('periods.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </x-btn>
                </div>
            </form>
        </x-card>

        @if ($period->status != 'open')
            <div class="p-8 bg-slate-50 border border-slate-100 rounded-3xl shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="text-[13px] font-black text-slate-900 uppercase tracking-widest">Informasi Penutupan Audit</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-5 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1.5">Waktu Penutupan</p>
                        <p class="text-slate-900 font-black text-[14px]">{{ $period->closed_at ?: 'Belum ditutup' }}</p>
                    </div>
                    <div class="p-5 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1.5">Oleh Petugas</p>
                        <p class="text-slate-900 font-black text-[14px]">{{ $period->closedBy ? $period->closedBy->name : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </x-container>
</x-app-layout>
