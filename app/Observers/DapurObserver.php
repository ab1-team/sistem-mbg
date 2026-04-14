<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Dapur;
use App\Traits\Accounting\HasAccountTemplate;

class DapurObserver
{
    use HasAccountTemplate;

    /**
     * Handle the Dapur "created" event.
     * Sesuai Roadmap 1.2 (Auto-create Wallet)
     * Tambahan: Auto-create COA Accounts (Level 4)
     */
    public function created(Dapur $dapur): void
    {
        // 1. Create Wallet
        $dapur->wallet()->create([
            'balance' => 0,
            'is_active' => true,
            'notes' => 'Otomatis dibuat saat pendaftaran Dapur.',
        ]);

        // 2. Create COA Level 4 Accounts
        $template = $this->accountTemplate();
        $accountsToInsert = [];

        foreach ($template as $rek) {
            $kodeAkun = explode('.', $rek['kode']);
            $kodeLevel1 = intval($kodeAkun[0]);
            $kodeLevel2 = intval($kodeAkun[1]);
            $kodeLevel3 = intval($kodeAkun[2]);
            $kodeLevel4 = intval($kodeAkun[3]);

            // Filter for Level 4 accounts (x.y.zz.ww where ww > 0)
            if ($kodeLevel1 > 0 && $kodeLevel2 > 0 && $kodeLevel3 > 0 && $kodeLevel4 > 0) {
                $accountsToInsert[] = [
                    'dapur_id' => $dapur->id,
                    'kode' => $rek['kode'],
                    'nama' => $rek['nama'],
                    'parent_id' => $rek['parent_id'],
                    'jenis_mutasi' => $rek['jenis_mutasi'],
                    'no_rek_bank' => $rek['no_rek_bank'],
                    'atas_nama_rek' => $rek['atas_nama_rek'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Batch insert for better performance
        if (! empty($accountsToInsert)) {
            Account::insert($accountsToInsert);
        }
    }
}
