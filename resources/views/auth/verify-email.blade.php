<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Verifikasi Email</h2>
        <p class="text-[13px] text-slate-400 mt-2">
            Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik link yang telah kami kirimkan.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" message="Link verifikasi baru telah dikirim ke alamat email Anda." />
        <div class="mb-4"></div>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-btn type="submit">Kirim Ulang Email Verifikasi</x-btn>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-btn type="submit" variant="ghost">Log Out</x-btn>
        </form>
    </div>
</x-guest-layout>
