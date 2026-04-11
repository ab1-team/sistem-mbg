<x-app-layout title="Tambah Periode">
    <x-container>
        <x-page-header title="Buka Periode Operasional"
            subtitle="Buat periode baru untuk pencatatan transaksi dan bagi hasil." :back="route('periods.index')"
            back-label="Data Periode" />

        <x-card title="Informasi Periode" subtitle="Tentukan rentang waktu operasional.">
            <form action="{{ route('periods.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Global Error Alert --}}
                @if ($errors->any())
                    <div
                        class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex gap-3 animate-in fade-in slide-in-from-top-1 duration-300">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-[13px] text-rose-700 leading-none font-bold mb-1">Gagal Membuka Periode</p>
                            <ul class="text-[12px] text-rose-600/80">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-form-searchable-select label="Bulan" name="month" :selected="old('month', date('n'))" :options="collect(range(1, 12))
                        ->map(
                            fn($m) => [
                                'value' => (string) $m,
                                'label' => \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F'),
                            ],
                        )
                        ->toArray()" required />

                    <x-form-input label="Tahun" name="year" type="number" :value="old('year', date('Y'))" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-datepicker label="Tanggal Mulai" name="start_date" :value="old('start_date', date('Y-m-01'))" required />
                    <x-datepicker label="Tanggal Selesai" name="end_date" :value="old('end_date', date('Y-m-t'))" required />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50">
                    <x-btn href="{{ route('periods.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        Buka Periode
                    </x-btn>
                </div>
            </form>
        </x-card>

        <x-alert variant="info" title="Informasi">
            <p class="text-[12px] leading-relaxed font-medium">
                Data periodik digunakan untuk membatasi akses transaksi dan perhitungan keuntungan. Pastikan rentang
                tanggal tidak tumpang tindih dengan periode lain.
            </p>
        </x-alert>
    </x-container>
</x-app-layout>
