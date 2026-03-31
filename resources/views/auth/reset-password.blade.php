<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Reset Password</h2>
        <p class="text-[13px] text-slate-400 mt-2">Buat password baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-form-input label="Email" name="email" type="email" :value="old('email', $request->email)" required />
        <x-form-input label="Password Baru" name="password" type="password" required />
        <x-form-input label="Konfirmasi Password" name="password_confirmation" type="password" required />

        <x-btn type="submit" class="w-full justify-center">Reset Password</x-btn>
    </form>
</x-guest-layout>
