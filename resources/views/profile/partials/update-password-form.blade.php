<section>
    <header class="mb-5">
        <h2 class="text-[14px] font-bold text-slate-900">Update Password</h2>
        <p class="text-[12px] text-slate-400 mt-1">Gunakan password panjang dan acak agar akun lebih aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <x-form-input label="Password Saat Ini" name="current_password" id="update_password_current_password" type="password" />
        <x-form-input label="Password Baru" name="password" id="update_password_password" type="password" />
        <x-form-input label="Konfirmasi Password Baru" name="password_confirmation" id="update_password_password_confirmation" type="password" />

        <div class="flex items-center gap-3 pt-2">
            <x-btn type="submit">Simpan Password</x-btn>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-[12px] text-green-600 font-medium">Password diperbarui!</p>
            @endif
        </div>
    </form>
</section>
