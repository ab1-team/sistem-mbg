<section>
    <header>
        <h2 class="text-[16px] font-bold text-slate-800">
            {{ __('Notifikasi Push') }}
        </h2>

        <p class="mt-1 text-[13px] text-slate-500">
            {{ __('Aktifkan notifikasi push untuk menerima pemberitahuan langsung di perangkat Anda.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div class="flex items-center gap-4">
            <x-btn type="button" variant="success" id="enable-push-btn" onclick="window.requestPushPermission()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                {{ __('Aktifkan Notifikasi di Perangkat Ini') }}
            </x-button>

            <p id="push-status" class="text-[12px] text-slate-500 italic">
                {{ __('Status: Belum aktif / Belum diizinkan') }}
            </p>
        </div>

        <div id="push-success-msg" class="hidden text-sm text-green-600 font-medium">
            {{ __('Notifikasi berhasil diaktifkan!') }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (Notification.permission === 'granted') {
                const btn = document.getElementById('enable-push-btn');
                const status = document.getElementById('push-status');
                if (btn) btn.disabled = true;
                if (status) status.innerText = 'Status: Aktif';
            }
        });
    </script>
</section>
