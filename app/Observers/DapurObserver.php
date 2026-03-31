<?php

namespace App\Observers;

use App\Models\Dapur;

class DapurObserver
{
    /**
     * Handle the Dapur "created" event.
     * Sesuai Roadmap 1.2 (Auto-create Wallet)
     */
    public function created(Dapur $dapur): void
    {
        $dapur->wallet()->create([
            'balance' => 0,
            'is_active' => true,
            'notes' => 'Otomatis dibuat saat pendaftaran Dapur.',
        ]);
    }
}
