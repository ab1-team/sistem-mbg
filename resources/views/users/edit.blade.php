<x-app-layout title="Edit User">

    <x-page-header
        title="Edit Pengguna"
        subtitle="Perbarui profil dan hak akses {{ $user->name }}."
        :back="route('users.index')"
        back-label="Pengguna & Akses"
    />

    <x-card class="max-w-4xl">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Profil Dasar --}}
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Profil Dasar</p>
                <div class="grid grid-cols-2 gap-5">
                    <x-form-input label="Nama Lengkap" name="name" :value="old('name', $user->name)" required />
                    <x-form-input label="Alamat Email" name="email" type="email" :value="old('email', $user->email)" required />
                    <div class="col-span-2">
                        <x-form-input
                            label="Ganti Password (Opsional)"
                            name="password"
                            type="password"
                            placeholder="Kosongkan jika tidak ingin mengubah"
                        />
                    </div>
                </div>
            </div>

            {{-- Akses & Peran --}}
            <div class="pt-6 border-t border-slate-50">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Akses & Peran</p>
                <div class="grid grid-cols-3 gap-5">
                    <x-form-searchable-select 
                        label="Role / Hak Akses" 
                        name="role" 
                        :selected="old('role', $user->roles->first()?->name)"
                        :options="$roles->map(fn($role) => ['value' => $role->name, 'label' => ucwords(str_replace('_', ' ', $role->name))])"
                        required 
                    />

                    <x-form-searchable-select 
                        label="Akses Dapur" 
                        name="dapur_id" 
                        :selected="old('dapur_id', $user->dapur_id)"
                        :options="$dapurs->map(fn($d) => ['value' => (string)$d->id, 'label' => $d->name])->prepend(['value' => '', 'label' => 'Tidak ada kaitan'])"
                    />

                    <x-form-searchable-select 
                        label="Akses Supplier" 
                        name="supplier_id" 
                        :selected="old('supplier_id', $user->supplier_id)"
                        :options="$suppliers->map(fn($s) => ['value' => (string)$s->id, 'label' => $s->name])->prepend(['value' => '', 'label' => 'Tidak ada kaitan'])"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        value="1"
                        {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-200 text-green-900 focus:ring-green-900/20"
                    >
                    <span class="text-[13px] font-medium text-slate-700">Akun Aktif</span>
                </label>

                <div class="flex items-center gap-3">
                    <x-btn href="{{ route('users.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

</x-app-layout>
