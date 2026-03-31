<x-app-layout title="Tambah User">

    <x-page-header
        title="Tambah Pengguna"
        subtitle="Buat akun baru dan tetapkan hak aksesnya."
        :back="route('users.index')"
        back-label="Pengguna & Akses"
    />

    <x-card class="max-w-4xl">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Profil Dasar --}}
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Profil Dasar</p>
                <div class="grid grid-cols-2 gap-5">
                    <x-form-input label="Nama Lengkap" name="name" :value="old('name')" placeholder="John Doe" required />
                    <x-form-input label="Alamat Email" name="email" type="email" :value="old('email')" placeholder="john@company.com" required />
                    <div class="col-span-2">
                        <x-form-input
                            label="Password Sementara"
                            name="password"
                            type="password"
                            placeholder="Minimal 8 karakter"
                            hint="Berikan password ini kepada pengguna untuk login pertama kali."
                            required
                        />
                    </div>
                </div>
            </div>

            {{-- Akses & Peran --}}
            <div class="pt-5 border-t border-slate-50">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Akses & Peran</p>
                <div class="grid grid-cols-3 gap-5">
                    <x-form-select label="Role / Hak Akses" name="role" required>
                        <option value="" disabled selected>Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-select label="Akses Dapur" name="dapur_id">
                        <option value="">Tidak ada kaitan</option>
                        @foreach($dapurs as $dapur)
                            <option value="{{ $dapur->id }}" {{ old('dapur_id') == $dapur->id ? 'selected' : '' }}>{{ $dapur->name }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-select label="Akses Supplier" name="supplier_id">
                        <option value="">Tidak ada kaitan</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </x-form-select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-200 text-green-900 focus:ring-green-900/20">
                    <span class="text-[13px] font-medium text-slate-700">Akun Aktif</span>
                </label>
                <div class="flex items-center gap-3">
                    <x-btn href="{{ route('users.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Simpan Data
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

</x-app-layout>
