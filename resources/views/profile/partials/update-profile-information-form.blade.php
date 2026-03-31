<section>
    <header class="mb-5">
        <h2 class="text-[14px] font-bold text-slate-900">Informasi Profil</h2>
        <p class="text-[12px] text-slate-400 mt-1">Perbarui nama dan alamat email akun Anda.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <x-form-input label="Nama" name="name" :value="old('name', $user->name)" required />
        <x-form-input label="Email" name="email" type="email" :value="old('email', $user->email)" required />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl">
                <p class="text-[12px] text-amber-700">
                    Email belum diverifikasi.
                    <button form="send-verification" class="underline font-medium hover:text-amber-900">Kirim ulang verifikasi.</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="text-[11px] text-green-600 mt-1">Link verifikasi telah dikirim.</p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <x-btn type="submit">Simpan</x-btn>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-[12px] text-green-600 font-medium">Tersimpan!</p>
            @endif
        </div>
    </form>
</section>
