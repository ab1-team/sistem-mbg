<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AkunLevel1;
use App\Models\AkunLevel2;
use App\Models\AkunLevel3;
use App\Traits\Accounting\HasAccountTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    use HasAccountTemplate;

    /**
     * Run the database seeds.
     * Only seeds Levels 1, 2, and 3 (Global Accounting Categories).
     * Level 4 (Accounts) is now handled automatically by DapurObserver upon Dapur creation.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AkunLevel1::truncate();
        AkunLevel2::truncate();
        AkunLevel3::truncate();
        // We don't truncate Account table here because it belongs to specific Dapurs now.

        $akunLevel1 = [];
        $akunLevel2 = [];
        $akunLevel3 = [];

        $template = $this->accountTemplate();

        foreach ($template as $rek) {
            $kodeAkun = explode('.', $rek['kode']);
            $kodeLevel1 = intval($kodeAkun[0]);
            $kodeLevel2 = intval($kodeAkun[1]);
            $kodeLevel3 = intval($kodeAkun[2]);
            $kodeLevel4 = intval($kodeAkun[3]);

            $rek['created_at'] = now();
            $rek['updated_at'] = now();

            // Level 1: x.0.00.00
            if ($kodeLevel1 > 0 && $kodeLevel2 == 0 && $kodeLevel3 == 0 && $kodeLevel4 == 0) {
                $akunLevel1[] = $rek;
            }

            // Level 2: x.y.00.00
            if ($kodeLevel1 > 0 && $kodeLevel2 > 0 && $kodeLevel3 == 0 && $kodeLevel4 == 0) {
                $akunLevel2[] = $rek;
            }

            // Level 3: x.y.zz.00
            if ($kodeLevel1 > 0 && $kodeLevel2 > 0 && $kodeLevel3 > 0 && $kodeLevel4 == 0) {
                $akunLevel3[] = $rek;
            }
        }

        AkunLevel1::insert($akunLevel1);
        AkunLevel2::insert($akunLevel2);
        AkunLevel3::insert($akunLevel3);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
