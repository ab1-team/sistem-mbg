<x-guest-layout>
    <div class="mb-10 animate-slide-up" style="animation-delay: 0.2s">
        <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
            Reset <span class="bg-clip-text text-transparent bg-linear-to-r from-emerald-600 to-green-500">Kata Sandi</span>
        </h3>
        <p class="mt-4 text-lg text-gray-500 dark:text-gray-400 font-medium">
            Lupa kata sandi? Masukkan email Anda dan kami akan mengirimkan tautan pemulihan.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-8" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-8 animate-slide-up" style="animation-delay: 0.3s">
        @csrf

        <!-- Email Address -->
        <div class="space-y-4">
            <x-input-label for="email" :value="__('ALAMAT EMAIL PEMULIHAN')" class="text-[10px] font-black uppercase tracking-widest text-gray-400" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-primary-500 transition-colors">
                    <svg class="w-5 h-5 text-gray-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <input id="email" class="block w-full pl-12 pr-4 py-4 bg-gray-50/50 dark:bg-brand-dark/50 border border-gray-100 dark:border-brand-border text-gray-900 dark:text-white focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl shadow-sm transition-all duration-300 placeholder:text-gray-300 dark:placeholder:text-gray-600 font-medium" type="email" name="email" :value="old('email')" required autofocus placeholder="email@anda.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full h-16 bg-linear-to-tr from-emerald-600 to-green-500 hover:from-emerald-500 hover:to-green-400 text-white font-bold text-lg tracking-tight rounded-2xl shadow-[0_20px_40px_-15px_rgba(16,185,129,0.3)] hover:shadow-[0_20px_40px_-10px_rgba(16,185,129,0.4)] hover:-translate-y-1 transition-all duration-300 active:scale-95">
                {{ __('Kirim Tautan Pemulihan') }}
            </button>
        </div>

        <div class="text-center pt-8">
            <a href="{{ route('login') }}" class="text-sm font-bold text-gray-500 hover:text-primary-500 flex items-center justify-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Halaman Login
            </a>
        </div>
    </form>
</x-guest-layout>
