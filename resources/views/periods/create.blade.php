<x-app-layout title="Tambah Periode">

    <x-page-header
        title="Buka Periode Operasional"
        subtitle="Buat periode baru untuk pencatatan transaksi dan bagi hasil."
        :back="route('periods.index')"
        back-label="Data Periode"
    />

    <x-card class="max-w-2xl mx-auto">
        <form action="{{ route('periods.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-5">
                <x-form-select label="Bulan" name="month" required>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </x-form-select>

                <x-form-input label="Tahun" name="year" type="number" :value="old('year', date('Y'))" required />
            </div>

            <div class="grid grid-cols-2 gap-5 pt-4 border-t border-slate-50">
                <x-form-input label="Tanggal Mulai" name="start_date" type="date" :value="old('start_date', date('Y-m-01'))" required />
                <x-form-input label="Tanggal Selesai" name="end_date" type="date" :value="old('end_date', date('Y-m-t'))" required />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50">
                <x-btn href="{{ route('periods.index') }}" variant="secondary">Batal</x-btn>
                <x-btn type="submit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Buka Periode
                </x-btn>
            </div>
        </form>
    </x-card>

    <div class="max-w-2xl mx-auto mt-6">
        <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex gap-3">
             <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             <p class="text-[13px] text-blue-700 leading-relaxed font-medium">
                Data periodik digunakan untuk membatasi akses transaksi dan perhitungan keuntungan. Pastikan rentang tanggal tidak tumpang tindih dengan periode lain.
             </p>
        </div>
    </div>

</x-app-layout>
