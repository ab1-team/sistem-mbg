<?php

namespace App\Observers;

use App\Models\Investor;

class InvestorObserver
{
    /**
     * Handle the Investor "created" event.
     * Sesuai Roadmap 1.2 (Auto-create Wallet)
     */
    public function created(Investor $investor): void
    {
        $investor->wallet()->create([
            'balance' => 0,
            'is_active' => true,
            'notes' => 'Otomatis dibuat saat pendaftaran Investor.',
        ]);
    }
}
