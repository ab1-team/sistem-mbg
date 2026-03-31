<x-guest-layout title="Daftar Akun Baru">
    <!-- Authentication Header -->
    <div class="mb-8 text-center animate-reveal" style="animation-delay: 0.1s">
        <h3 class="text-2xl font-bold text-slate-900 tracking-tight leading-none mb-3">
            Daftar <span class="text-green-800 font-extrabold">Akun Baru</span>
        </h3>
        <p class="text-[13px] text-slate-500 font-medium leading-relaxed px-4">
            Bergabunglah dengan ekosistem operasional Yayasan MBG.
        </p>
    </div>

    <!-- Register Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-6 animate-reveal" style="animation-delay: 0.2s">
        @csrf

        <!-- Name -->
        <div class="space-y-3">
            <x-form-input 
                id="name" 
                name="name" 
                label="Nama Lengkap" 
                type="text" 
                :value="old('name')" 
                required 
                autofocus 
                autocomplete="name" 
                placeholder="John Doe"
            >
                <x-slot name="icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </x-slot>
            </x-form-input>
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div class="space-y-3">
            <x-form-input 
                id="email" 
                name="email" 
                label="Alamat Email" 
                type="email" 
                :value="old('email')" 
                required 
                autocomplete="username" 
                placeholder="john@yayasan-mbg.id"
            >
                <x-slot name="icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                </x-slot>
            </x-form-input>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="space-y-3">
            <x-form-input 
                id="password" 
                name="password" 
                label="Kata Sandi" 
                type="password" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
            >
                <x-slot name="icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </x-slot>
            </x-form-input>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-3">
            <x-form-input 
                id="password_confirmation" 
                name="password_confirmation" 
                label="Konfirmasi Kata Sandi" 
                type="password" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
            >
                <x-slot name="icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </x-slot>
            </x-form-input>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Submit Call-To-Action -->
        <div class="pt-2">
            <x-btn type="submit" size="xl" class="w-full bg-green-700 hover:bg-green-800">
                Buat akun baru
                <svg class="w-5 h-5 transform transition-transform duration-300 group-hover:translate-x-1 ml-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </x-btn>
        </div>

        <!-- Utility Navigation -->
        <div class="text-center">
            <p class="text-[12px] text-slate-500 font-medium">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-green-800 hover:text-green-700 transition-all font-semibold underline underline-offset-4 decoration-green-800/20">Masuk sekarang</a>
            </p>
        </div>
    </form>
</x-guest-layout>
