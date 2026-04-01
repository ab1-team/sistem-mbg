<x-app-layout title="Produksi Dapur">
    <x-page-header title="Produksi Dapur Harian"
        subtitle="{{ $dapur->name }} — {{ now()->translatedFormat('d F Y') }} — Unit Operasional Aktif">
        <x-slot name="actions">
            @if (auth()->user()->hasRole('superadmin'))
                <div class="w-[240px]">
                    <x-form-searchable-select name="dapur_id" :options="$allDapurs->map(fn($d) => ['value' => $d->id, 'label' => $d->name])->toArray()" :selected="$dapur->id"
                        placeholder="Cari Unit Dapur..."
                        class="border-none shadow-none bg-slate-50/50 hover:bg-white transition-all font-black text-slate-900"
                        onSelected="window.location.href = '?dapur_id=' + opt.value" />
                </div>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($schedules as $schedule)
            <x-card :title="$schedule->menuSchedule->menuItem->name" :subtitle="Str::headline($schedule->menuSchedule->meal_type) . ' • ' . $dapur->name">

                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status
                            Produksi</span>
                        <span
                            class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $schedule->status->color() }}">
                            {{ $schedule->status->label() }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-3 border-y border-slate-50">
                        <x-show-field label="Target Porsi" :value="number_format($schedule->menuSchedule->target_portions)" />
                        <x-show-field label="Realisasi">
                            <span
                                class="font-bold {{ $schedule->portions_cooked ? 'text-emerald-600' : 'text-slate-300' }}">
                                {{ $schedule->portions_cooked ? number_format($schedule->portions_cooked) : '---' }}
                            </span>
                        </x-show-field>
                    </div>

                    {{-- AKSI (Kecil & Rapi - Sesuai Modul Penagihan) --}}
                    <div class="flex justify-end pt-2">
                        @if ($schedule->status === \App\Enums\CookingStatus::BELUM_MULAI)
                            <form action="{{ route('kitchen.prepare', $schedule) }}" method="POST">
                                @csrf
                                <x-btn type="submit" variant="secondary" size="sm">Mulai Persiapan</x-btn>
                            </form>
                        @elseif($schedule->status === \App\Enums\CookingStatus::PERSIAPAN)
                            <form action="{{ route('kitchen.start', $schedule) }}" method="POST">
                                @csrf
                                <x-btn type="submit" size="sm"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white">Mulai Memasak</x-btn>
                            </form>
                        @elseif($schedule->status === \App\Enums\CookingStatus::MEMASAK)
                            <form action="{{ route('kitchen.finish', $schedule) }}" method="POST"
                                class="flex items-center gap-3 w-full">
                                @csrf
                                <div class="flex-1">
                                    <input type="number" name="portions_cooked"
                                        value="{{ $schedule->menuSchedule->target_portions }}" required
                                        class="w-full rounded-xl border-slate-200 text-[13px] font-bold py-1.5 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                <x-btn type="submit" size="sm"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shrink-0">
                                    Selesai
                                </x-btn>
                            </form>
                        @elseif($schedule->status === \App\Enums\CookingStatus::SELESAI)
                            <form action="{{ route('kitchen.distribute', $schedule) }}" method="POST">
                                @csrf
                                <x-btn type="submit" size="sm"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white">Distribusikan</x-btn>
                            </form>
                        @else
                            <div
                                class="px-3 py-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 rounded-full border border-emerald-100">
                                Terdistribusi
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-empty-state title="Tidak Ada Jadwal"
                    subtitle="Pilih unit dapur lain atau hubungi admin untuk sinkronisasi jadwal menu." />
            </div>
        @endforelse
    </div>
</x-app-layout>
