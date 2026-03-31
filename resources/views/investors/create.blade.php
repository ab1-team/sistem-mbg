<x-app-layout title="Tambah Investor">

    <x-page-header
        title="Registrasi Investor"
        subtitle="Isi detail identitas dan persentase saham investor baru."
        :back="route('investors.index')"
        back-label="Data Investor"
    />

    <x-card class="max-w-4xl">
        <form action="{{ route('investors.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Akun & Identitas --}}
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Akun & Identitas</p>
                <div class="grid grid-cols-2 gap-5">
                    <div class="col-span-2">
                        <x-form-select label="Pilih Akun User (Role: Investor)" name="user_id" required>
                            <option value="" disabled selected>Pilih User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </x-form-select>
                    </div>
                    <x-form-input label="Kode Investor" name="code" :value="old('code')" placeholder="INV-001" required />
                    <x-form-input label="Nama Lengkap Investor" name="name" :value="old('name')" placeholder="Nama sesuai identitas" required />
                    <x-form-input label="No. KTP / Identitas" name="identity_number" :value="old('identity_number')" placeholder="330xxx" />
                    <x-form-input
                        label="Porsi Saham (%)"
                        name="share_percentage"
                        type="number"
                        step="0.0001"
                        :value="old('share_percentage', 0)"
                        required
                    />
                </div>
            </div>

            {{-- Keuangan & Periode --}}
            <div class="pt-5 border-t border-slate-50">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Keuangan & Rekening</p>
                <div class="grid grid-cols-3 gap-5">
                    <div class="col-span-3">
                        <x-form-input label="Tanggal Bergabung" name="join_date" type="date" :value="old('join_date', date('Y-m-d'))" required />
                    </div>
                    <x-form-input label="Nama Bank" name="bank_name" :value="old('bank_name')" placeholder="BCA/Mandiri" />
                    <x-form-input label="Nomor Rekening" name="bank_account" :value="old('bank_account')" />
                    <x-form-input label="Atas Nama Rekening" name="bank_holder" :value="old('bank_holder')" />
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-200 text-green-900 focus:ring-green-900/20">
                    <span class="text-[13px] font-medium text-slate-700">Status Aktif (Berhak Dividend)</span>
                </label>
                <div class="flex items-center gap-3">
                    <x-btn href="{{ route('investors.index') }}" variant="secondary">Batal</x-btn>
                    <x-btn type="submit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Simpan Data
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>

</x-app-layout>
