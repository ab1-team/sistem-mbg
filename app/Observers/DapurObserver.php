<?php

namespace App\Observers;

use App\Models\Dapur;

class DapurObserver
{
    /**
     * Handle the Dapur "created" event.
     * Sesuai Roadmap 1.2 (Auto-create Wallet)
     * Tambahan: Auto-create COA Accounts (Level 4)
     */
    public function created(Dapur $dapur): void
    {
        $dapur->initializeAccounting();
    }
}
