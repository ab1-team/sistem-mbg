<x-guest-layout title="Masuk Ke Sistem">
    <!-- Authentication Header -->
    <div class="mb-8 text-center animate-reveal" style="animation-delay: 0.1s">
        <h3 class="text-2xl font-bold text-slate-900 tracking-tight leading-none mb-3">
            Masuk ke <span class="text-emerald-700 font-extrabold">Dashboard</span>
        </h3>
        <p class="text-[13px] text-slate-500 font-medium leading-relaxed px-4">
            Gunakan akun Anda untuk mengelola operasional Yayasan MBG.
        </p>
    </div>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-8 animate-reveal" style="animation-delay: 0.2s">
        @csrf

        <!-- Email Address -->
        <div class="space-y-3">
            <x-form-input id="email" name="email" label="Alamat Email" type="email" :value="old('email')" required
                autofocus placeholder="admin@yayasan-mbg.id">
                <x-slot name="icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </x-slot>
            </x-form-input>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="space-y-3">
            <div class="relative">
                <x-form-input id="password" name="password" label="Kata Sandi" type="password" required
                    autocomplete="current-password" placeholder="••••••••">
                    <x-slot name="icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </x-slot>
                </x-form-input>
                @if (Route::has('password.request'))
                    <a class="absolute top-0 right-1 text-[10px] font-semibold text-emerald-700 hover:text-emerald-600 uppercase tracking-widest transition-all"
                        href="{{ route('password.request') }}">
                        Lupa?
                    </a>
                @endif
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me Session -->
        <div class="flex items-center group cursor-pointer">
            <input id="remember_me" type="checkbox"
                class="w-5 h-5 rounded-lg border-slate-200 bg-slate-50 text-emerald-700 focus:ring-emerald-700/10 cursor-pointer transition-all duration-300"
                name="remember">
            <label for="remember_me"
                class="ms-3 text-[12px] text-slate-500 font-medium cursor-pointer leading-none group-hover:text-slate-800 transition-colors">
                Ingat saya di perangkat ini
            </label>
        </div>

        <!-- Submit Call-To-Action -->
        <div class="pt-2">
            <x-btn type="submit" size="xl" class="w-full">
                Masuk ke Sistem
                <svg class="w-5 h-5 transform transition-transform duration-300 group-hover:translate-x-1 ml-2"
                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </x-btn>
        </div>

        <!-- Utility Navigation -->
        <div class="text-center">
            <p class="text-[12px] text-slate-500 font-medium">
                Belum punya akses?
                <a href="{{ route('register') }}"
                    class="text-emerald-700 hover:text-emerald-600 transition-all font-semibold underline underline-offset-4 decoration-emerald-700/20">
                    Hubungi Admin
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
