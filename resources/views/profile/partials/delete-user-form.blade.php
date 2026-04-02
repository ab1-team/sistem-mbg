<section>
    <header class="mb-5">
        <h2 class="text-[14px] font-bold text-slate-900">Hapus Akun</h2>
        <p class="text-[12px] text-slate-400 mt-1">
            Setelah akun dihapus, semua data akan dihapus permanen. Pastikan Anda sudah mengunduh data yang diperlukan.
        </p>
    </header>

    <x-dialog name="confirm-user-deletion" title="Hapus Akun?" :max-width="'md'">
        <x-slot:trigger>
            <x-btn variant="danger" x-on:click="$dispatch('open-modal', 'confirm-user-deletion')">
                Hapus Akun
            </x-btn>
        </x-slot:trigger>

        <form method="post" action="{{ route('profile.destroy') }}" id="delete-account-form">
            @csrf
            @method('delete')
            <p class="text-[13px] text-slate-600 mb-4">
                Tindakan ini tidak dapat dibatalkan. Masukkan password untuk konfirmasi.
            </p>
            <x-form-input label="Password" name="password" type="password" placeholder="Password Anda" />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </form>

        <x-slot:footer>
            <x-btn variant="secondary" size="md"
                x-on:click="$dispatch('close-modal', 'confirm-user-deletion')">Batal</x-btn>
            <x-btn variant="danger" size="md"
                x-on:click="document.getElementById('delete-account-form').submit()">Ya, Hapus Akun</x-btn>
        </x-slot:footer>
    </x-dialog>
</section>
