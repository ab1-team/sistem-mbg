<div>
    <x-page-header title="Manajemen Periode Keuangan"
        subtitle="Kontrol pembukaan, penutupan, dan penguncian periode untuk laporan laba rugi.">
        <x-slot name="actions">
            <x-btn href="{{ route('periods.create') }}" icon="plus">Buka Periode Baru</x-btn>
        </x-slot>
    </x-page-header>

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th>Kode</x-table-th>
                <x-table-th>Nama Periode</x-table-th>
                <x-table-th>Rentang Tanggal</x-table-th>
                <x-table-th>Status</x-table-th>
                <x-table-th>Ditutup Oleh</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($periods as $period)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <span class="text-slate-500 font-mono text-[11px] font-bold">{{ $period->code }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span
                            class="font-black text-slate-900 group-hover:text-green-700 transition-colors uppercase tracking-tight">{{ $period->name }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="text-slate-400 font-medium text-[11px]">{{ $period->start_date->format('d M') }} —
                            {{ $period->end_date->format('d M Y') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <x-badge :variant="$period->status === 'open' ? 'success' : ($period->status === 'closed' ? 'warning' : 'danger')">
                            {{ strtoupper($period->status) }}
                        </x-badge>
                    </x-table-td>
                    <x-table-td>
                        @if ($period->closedBy)
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[9px] font-bold text-slate-500 border border-slate-200">
                                    {{ substr($period->closedBy->name, 0, 2) }}
                                </div>
                                <span
                                    class="text-slate-500 font-medium text-[11px]">{{ $period->closedBy->name }}</span>
                            </div>
                        @else
                            <span class="text-slate-300 italic text-[11px]">Belum ditutup</span>
                        @endif
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2">
                            @if ($period->isOpen())
                                <x-dialog title="Tutup Periode"
                                    description="Apakah Anda yakin ingin menutup periode {{ $period->name }}? Transaksi baru tidak akan bisa dicatat."
                                    action-label="Ya, Tutup" wire:click="closePeriod({{ $period->id }})">
                                    <x-btn variant="warning" class="py-1.5! px-3! text-[11px]!">Tutup Periode</x-btn>
                                </x-dialog>
                            @elseif($period->isClosed())
                                <x-btn wire:click="reopenPeriod({{ $period->id }})" variant="secondary"
                                    class="py-1.5! px-3! text-[11px]!">Buka Kembali</x-btn>
                                <x-dialog title="Kunci Periode & Bagi Hasil"
                                    description="Tindakan ini akan mengunci periode {{ $period->name }} secara permanen dan menghitung bagi hasil untuk investor. Lanjutkan?"
                                    action-label="Kunci & Proses" wire:click="lockPeriod({{ $period->id }})">
                                    <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Kunci Periode</x-btn>
                                </x-dialog>
                            @else
                                <span
                                    class="text-slate-400 font-bold text-[10px] uppercase border border-slate-100 px-2 py-1 rounded-lg bg-slate-50">FINAL
                                    & TERKUNCI</span>
                            @endif
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Tidak ada periode ditemukan"
                            subtitle="Silakan buat periode baru di manajemen sistem." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-auto">
            {{ $periods->links() }}
        </div>
    </x-card>

    <div class="bg-amber-50 border border-amber-100 rounded-[24px] p-6">
        <h5 class="text-amber-900 font-black text-[14px] uppercase tracking-wider mb-2">💡 Alur Periode</h5>
        <ul class="text-amber-800 text-[12px] space-y-2 list-disc list-inside leading-relaxed opacity-90">
            <li><strong>OPEN:</strong> Pencatatan pendapatan dan beban diizinkan.</li>
            <li><strong>CLOSED:</strong> Pencatatan dinonaktifkan, tim finance melakukan rekonsiliasi data.</li>
            <li><strong>LOCKED:</strong> Laba bersih dikalkulasi dan dividen otomatis didistribusikan ke dompet
                investor. <em>(Tidak dapat dibuka kembali)</em></li>
        </ul>
    </div>
</div>
