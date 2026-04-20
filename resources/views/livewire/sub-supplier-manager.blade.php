<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- LIST --}}
        <div class="lg:col-span-2">
            <x-card title="Daftar Sub-Supplier" subtitle="Personel yang melakukan pengiriman/penjualan barang.">
                @if(session()->has('success'))
                    <div class="mb-6">
                        <x-alert type="success" title="Berhasil" :message="session('success')" />
                    </div>
                @endif

                <x-table>
                    <x-slot name="thead">
                        <x-table-th>Nama / Personel</x-table-th>
                        <x-table-th>Kontak</x-table-th>
                        <x-table-th class="text-center">Status</x-table-th>
                        <x-table-th class="text-right">Aksi</x-table-th>
                    </x-slot>

                    @forelse($subSuppliers as $ss)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <x-table-td>
                                <p class="text-[14px] font-black text-slate-900 leading-none mb-1">{{ ucwords($ss->name) }}</p>
                                <p class="text-[11px] text-slate-400 font-medium truncate max-w-[200px]">{{ $ss->address ?: 'Alamat tidak diisi' }}</p>
                            </x-table-td>
                            <x-table-td>
                                <div class="flex items-center gap-2 text-[13px] font-bold text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $ss->phone ?: '-' }}
                                </div>
                            </x-table-td>
                            <x-table-td class="text-center">
                                <button wire:click="toggleStatus({{ $ss->id }})" class="focus:outline-none">
                                    <x-badge :variant="$ss->is_active ? 'success' : 'gray'">
                                        {{ $ss->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </x-badge>
                                </button>
                            </x-table-td>
                            <x-table-td class="text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $ss->id }})" class="p-2 text-slate-400 hover:text-emerald-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $ss->id }})" wire:confirm="Hapus sub-supplier ini?" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </x-table-td>
                        </tr>
                    @empty
                        <tr>
                            <x-table-td colspan="4">
                                <x-empty-state 
                                    title="Belum ada sub-supplier" 
                                    subtitle="Silakan gunakan formulir di samping untuk mendaftarkan personel baru." 
                                    icon="users"
                                />
                            </x-table-td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        {{-- FORM --}}
        <div>
            <x-card :title="$isEditing ? 'Edit Sub-Supplier' : 'Tambah Sub-Supplier'" :subtitle="$isEditing ? 'Perbarui data personel.' : 'Daftarkan personel baru.'">
                <form wire:submit.prevent="save" class="space-y-6">
                    <x-form-input label="Nama Lengkap" id="ss_name" name="name" wire:model="name" placeholder="Contoh: Bpk. Jajang" required />
                    <x-form-input label="Nomor WhatsApp" id="ss_phone" name="phone" wire:model="phone" placeholder="0812..." />
                    <x-form-textarea label="Alamat / Keterangan" id="ss_address" name="address" wire:model="address" placeholder="Opsional..." />

                    <div class="pt-4 flex flex-col gap-3">
                        <x-btn type="submit" class="w-full justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            {{ $isEditing ? 'Update Sub-Supplier' : 'Simpan Sub-Supplier' }}
                        </x-btn>
                        @if($isEditing)
                            <x-btn wire:click="resetForm" variant="secondary" class="w-full justify-center">
                                Batal
                            </x-btn>
                        @endif
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
