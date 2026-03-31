<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Konfirmasi Password</h2>
        <p class="text-[13px] text-slate-400 mt-2">Area aman. Konfirmasi password Anda untuk melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <x-form-input label="Password" name="password" type="password" required />

        <x-btn type="submit" class="w-full justify-center">Konfirmasi</x-btn>
    </form>
</x-guest-layout>
