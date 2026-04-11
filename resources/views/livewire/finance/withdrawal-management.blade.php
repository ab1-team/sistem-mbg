<x-container>
    <x-page-header title="Manajemen Penarikan Dana" subtitle="Verifikasi dan proses permintaan pencairan bagi hasil dari investor." />

    <x-card :padding="false" class="overflow-hidden">
        <x-table>
            <x-slot name="thead">
                <x-table-th sort="created_at" :active="$sortField === 'created_at'" :asc="$sortAsc">Tanggal</x-table-th>
                <x-table-th>Investor</x-table-th>
                <x-table-th class="text-right">Jumlah</x-table-th>
                <x-table-th>Bank</x-table-th>
                <x-table-th>Status</x-table-th>
                <x-table-th class="text-right">Aksi</x-table-th>
            </x-slot>

            @forelse($withdrawals as $wd)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <x-table-td>
                        <span class="text-slate-500 font-medium">{{ $wd->created_at->format('d/m/Y H:i') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <span class="font-bold text-slate-900 group-hover:text-blue-700 transition-colors uppercase tracking-tight">{{ $wd->investor->name }}</span>
                    </x-table-td>
                    <x-table-td class="text-right">
                        <span class="font-black text-slate-900">Rp {{ number_format($wd->amount, 0, ',', '.') }}</span>
                    </x-table-td>
                    <x-table-td>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-[11px]">{{ $wd->investor->bank_name }}</span>
                            <span class="text-slate-400 font-mono text-[10px]">{{ $wd->investor->bank_account }}</span>
                        </div>
                    </x-table-td>
                    <x-table-td>
                        <x-badge :variant="$wd->status === 'processed' ? 'success' : ($wd->status === 'pending' ? 'warning' : 'danger')">
                            {{ strtoupper($wd->status) }}
                        </x-badge>
                    </x-table-td>
                    <x-table-td class="text-right py-3 px-4">
                        <div class="flex items-center justify-end gap-2">
                            @if($wd->status === 'pending')
                                <x-dialog title="Setujui Penarikan" 
                                    description="Konfirmasi bahwa dana sebesar Rp {{ number_format($wd->amount, 0, ',', '.') }} telah ditransfer ke {{ $wd->investor->bank_holder }} ({{ $wd->investor->bank_name }}). Saldo akan dikurangi."
                                    action-label="Ya, Sudah Transfer" wire:click="approveRequest({{ $wd->id }})">
                                    <x-btn variant="success" class="py-1.5! px-3! text-[11px]!">Setujui</x-btn>
                                </x-dialog>
                                <x-dialog title="Tolak Penarikan" 
                                    description="Apakah Anda yakin ingin menolak permintaan penarikan dari {{ $wd->investor->name }}?"
                                    action-label="Tolak" wire:click="rejectRequest({{ $wd->id }})">
                                    <x-btn variant="danger" class="py-1.5! px-3! text-[11px]!">Tolak</x-btn>
                                </x-dialog>
                            @else
                                <span class="text-slate-300 font-bold text-[10px] uppercase">DIPROSES OLEH: {{ $wd->processedBy->name ?? 'SYSTEM' }}</span>
                            @endif
                        </div>
                    </x-table-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state title="Tidak ada permintaan"
                            subtitle="Semua permintaan penarikan telah diproses." />
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if ($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-slate-50">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </x-card>
</x-container>
