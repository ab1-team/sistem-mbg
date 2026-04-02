<x-guest-layout title="Atur Ulang Kata Sandi">
    <!-- Authentication Header -->
    <div class="mb-10 text-center animate-reveal" style="animation-delay: 0.1s">
        <h3 class="text-2xl font-bold text-slate-900 tracking-tight leading-none mb-4">
            Lupa <span class="text-emerald-700 font-extrabold">Kata Sandi?</span>
        </h3>
        <p class="text-[13px] text-slate-500 font-medium leading-relaxed px-4">
            Beritahu kami alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-8 animate-reveal"
        style="animation-delay: 0.2s">
        @csrf

        <!-- Email Address -->
        <div class="space-y-3">
            <x-form-input id="email" name="email" label="Alamat Email Pemulihan" type="email" :value="old('email')"
                required autofocus placeholder="admin@yayasan-mbg.id">
                <x-slot name="icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </x-slot>
            </x-form-input>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="pt-2">
            <x-btn type="submit" size="xl" class="w-full">
                Kirim Tautan Pemulihan
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </x-btn>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}"
                class="text-[12px] font-bold text-slate-500 hover:text-emerald-700 inline-flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Halaman Login
            </a>
        </div>
    </form>
</x-guest-layout>
