<x-app-layout title="Dapur Harian">
    <x-page-header title="Produksi Dapur Harian"
        subtitle="{{ $dapur->name }} — {{ now()->translatedFormat('d F Y') }} — Unit Operasional Aktif" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($schedules as $schedule)
            <div
                class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full transform transition-all active:scale-95 duration-75">
                {{-- Header Kartu --}}
                <div class="p-6 pb-4 border-b border-slate-50 grow">
                    <div class="flex justify-between items-start mb-4">
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-500 uppercase tracking-widest">
                            {{ $schedule->menuSchedule->meal_type }}
                        </span>
                        @if ($schedule->status === 'cooking')
                            <span
                                class="px-2 py-0.5 rounded-md text-[9px] font-black bg-green-50 text-green-900 uppercase tracking-widest border border-green-100">
                                Memasak
                            </span>
                        @endif
                    </div>

                    <h3 class="text-[20px] font-extrabold text-slate-900 leading-tight mb-2">
                        {{ $schedule->menuSchedule->menuItem->name }}
                    </h3>
                    <p class="text-[13px] text-slate-400 mt-1 mb-4 italic">
                        Kategori: {{ $schedule->menuSchedule->meal_type }}
                    </p>

                    <div class="flex items-center gap-4 py-3 px-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Target</p>
                            <p class="text-[16px] font-black text-slate-900">
                                {{ number_format($schedule->menuSchedule->target_portions) }}
                            </p>
                        </div>
                        <div class="w-px h-8 bg-slate-200"></div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aktual</p>
                            <p
                                class="text-[16px] font-black @if ($schedule->status === 'done') text-green-900 @else text-slate-300 @endif">
                                {{ $schedule->portions_cooked ? number_format($schedule->portions_cooked) : '---' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Aksi Kartu --}}
                <div class="p-4 bg-slate-50/50 border-t border-slate-50 flex gap-2">
                    @if ($schedule->status === 'pending')
                        <form action="{{ route('kitchen.start', $schedule) }}" method="POST" class="w-full">
                            @csrf
                            <x-btn type="submit" class="w-full">Mulai Masak</x-btn>
                        </form>
                    @elseif($schedule->status === 'cooking')
                        <x-btn @click="$dispatch('open-modal', 'modal-finish-{{ $schedule->id }}')" class="w-full">
                            Selesai & Potong Stok
                        </x-btn>
                    @else
                        <div
                            class="w-full text-center py-2.5 text-[12px] font-black text-green-900 bg-green-50 rounded-2xl border border-green-100 uppercase tracking-widest">
                            Tuntas
                        </div>
                    @endif
                </div>
            </div>

            {{-- Modal Konfirmasi Selesai --}}
            <x-dialog name="modal-finish-{{ $schedule->id }}" title="Konfirmasi Hasil Masakan">
                <form action="{{ route('kitchen.finish', $schedule) }}" method="POST" class="p-2">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Jumlah
                            Porsi Aktual</label>
                        <input type="number" name="portions_cooked"
                            value="{{ $schedule->menuSchedule->target_portions }}" required
                            class="w-full rounded-2xl border-slate-200 text-[18px] font-black px-6 py-4 focus:ring-orange-500 focus:border-orange-500">
                        <p class="mt-3 text-[11px] text-slate-400 italic leading-relaxed">
                            ⚠️ Mensubmit hasil masakan akan **mengurangi stok bahan baku otomatis** di gudang dapur
                            sesuai rincian BOM.
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <x-btn @click="$dispatch('close-modal', 'modal-finish-{{ $schedule->id }}')" type="button"
                            variant="secondary" class="flex-1 py-4!">Batal</x-btn>
                        <x-btn type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-4!">Simpan &
                            Distribusi</x-btn>
                    </div>
                </form>
            </x-dialog>

        @empty
            <div class="col-span-full border-2 border-dashed border-slate-200 rounded-[32px] p-20 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-[20px] font-black text-slate-900 mb-2">Belum Ada Jadwal Masak</h3>
                <p class="text-slate-400 text-[14px]">Ahli Gizi belum merencanakan menu untuk tanggal ini atau menu
                    belum disetujui Kepala Dapur.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
