<x-app-layout title="Edit Periode">

    <x-page-header
        title="Detail Periode"
        subtitle="Perbarui rentang tanggal dan status operasional periode {{ $period->name }}."
        :back="route('periods.index')"
        back-label="Data Periode"
    />

    <x-card class="max-w-2xl mx-auto {{ $period->status != 'open' ? 'opacity-80 grayscale-[0.1]' : '' }}">
        <form action="{{ route('periods.update', $period) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                 <div class="flex items-center gap-3">
                     <div class="w-11 h-11 rounded-xl bg-green-50 text-green-700 flex items-center justify-center font-bold text-[12px] border border-green-100 uppercase">
                         {{ $period->code }}
                     </div>
                     <div>
                         <h3 class="text-[13px] font-bold text-slate-900 uppercase tracking-tight">{{ $period->name }}</h3>
                         <div class="flex items-center gap-1.5 mt-0.5">
                             <div class="w-1.5 h-1.5 rounded-full {{ $period->status == 'open' ? 'bg-emerald-500' : 'bg-slate-400' }}"></div>
                             <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                Status: <span class="text-slate-900">{{ strtoupper($period->status) }}</span>
                             </p>
                         </div>
                     </div>
                 </div>
            </div>

            <x-form-input label="Nama Tampilan Periode" name="name" :value="old('name', $period->name)" required />

            <div class="grid grid-cols-2 gap-5 pt-4 border-t border-slate-50">
                <x-form-input label="Tanggal Mulai" name="start_date" type="date" :value="old('start_date', $period->start_date->format('Y-m-d'))" required />
                <x-form-input label="Tanggal Selesai" name="end_date" type="date" :value="old('end_date', $period->end_date->format('Y-m-d'))" required />
            </div>

            <div class="pt-4 border-t border-slate-50">
                <x-form-select label="Status Periode" name="status" required>
                    <option value="open" {{ old('status', $period->status) == 'open' ? 'selected' : '' }}>OPEN (Aktif Transaksi)</option>
                    <option value="closed" {{ old('status', $period->status) == 'closed' ? 'selected' : '' }}>CLOSED (Tutup Buku - Siap Audit)</option>
                    <option value="locked" {{ old('status', $period->status) == 'locked' ? 'selected' : '' }}>LOCKED (Terkunci - Hanya Superadmin)</option>
                </x-form-select>
                <p class="mt-2 text-[11px] text-slate-400 italic">Periode yang sudah CLOSED akan mencatat waktu penutupan dan user yang melakukan aksi.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50">
                <x-btn href="{{ route('periods.index') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </x-btn>
            </div>
        </form>
    </x-card>

    @if($period->status != 'open')
         <div class="max-w-2xl mx-auto mt-6">
             <div class="p-6 bg-slate-50 border border-slate-100 rounded-3xl">
                 <div class="flex items-center gap-2 mb-4">
                     <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                     <h4 class="text-[12px] font-bold text-slate-900 uppercase tracking-widest">Audit Info</h4>
                 </div>
                 <div class="grid grid-cols-2 gap-6 text-[12px] font-medium">
                     <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Ditutup Pada</p>
                        <p class="text-slate-900 font-bold">{{ $period->closed_at ?: 'Belum ditutup' }}</p>
                     </div>
                     <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Ditutup Oleh</p>
                        <p class="text-slate-900 font-bold">{{ $period->closedBy ? $period->closedBy->name : 'N/A' }}</p>
                     </div>
                 </div>
             </div>
         </div>
    @endif

</x-app-layout>
