<x-app-layout title="Profil Pengguna">

    <x-container>
        <div class="flex items-center justify-between mb-8">
            <x-page-header
                title="Profil Pengguna: {{ $user->name }}"
                subtitle="Detail akun, hak akses, dan aktivitas sistem dari {{ $user->email }}."
                class="mb-0"
            />
            <div class="flex items-center gap-3">
                 <x-btn href="{{ route('users.index') }}" variant="secondary">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                     Kembali
                 </x-btn>
                 <x-btn href="{{ route('users.edit', $user) }}">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                     Edit Akses
                 </x-btn>
            </div>
        </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Informasi Identitas">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Nama Lengkap" :value="$user->name" />
                    <x-show-field label="Alamat Email" :value="$user->email" />
                    <x-show-field label="Grup Akses (Roles)">
                        <div class="flex flex-wrap gap-2">
                             @foreach($user->roles as $role)
                                 <x-badge variant="success">{{ ucwords(str_replace('_', ' ', $role->name)) }}</x-badge>
                             @endforeach
                        </div>
                    </x-show-field>
                    <x-show-field label="Status Akun">
                         <x-badge variant="success">AKTIF / TERVERIFIKASI</x-badge>
                    </x-show-field>
                </div>
            </x-card>

            <x-card title="Data Kepegawaian">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                    <x-show-field label="Bagian / Divisi" value="Administrator" />
                    <x-show-field label="Waktu Registrasi" :value="$user->created_at->format('d M Y, H:i')" />
                </div>
            </x-card>
        </div>

        <div class="space-y-8">
            <x-card title="Keamanan & Audit">
                <div class="space-y-6">
                    <x-show-field label="Email Verified At" :value="$user->email_verified_at ? $user->email_verified_at->format('d M Y, H:i') : '-'" />
                    <x-show-field label="Daftar Melalui" value="Sistem Administrator" />
                </div>
            </x-card>

            <div class="bg-brand-soft border border-emerald-100 rounded-3xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-[13px] font-bold text-emerald-900 capitalize">Status Keamanan</h4>
                        <p class="text-[12px] text-emerald-600 font-medium leading-relaxed mt-1">Akun ini memiliki izin akses penuh ke seluruh modul sistem Yayasan MBG.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </x-container>
</x-app-layout>
