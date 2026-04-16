<?php

namespace App\Models;

use App\Traits\Accounting\HasAccountTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dapur extends Model
{
    use HasAccountTemplate, HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'slug',
        'address',
        'city',
        'province',
        'capacity_portions',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($dapur) {
            if (! $dapur->slug) {
                $originalSlug = Str::slug($dapur->name);
                $slug = $originalSlug;
                $count = 1;

                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $originalSlug.'-'.$count++;
                }

                $dapur->slug = $slug;
            }
        });
    }

    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'owner');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Initialize accounting structure for this Dapur.
     * Robust: safe to run multiple times.
     */
    public function initializeAccounting(): void
    {
        // 1. Create/Ensure Wallet
        $this->wallet()->firstOrCreate([], [
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

            if ($kodeLevel1 > 0 && $kodeLevel2 > 0 && $kodeLevel3 > 0 && $kodeLevel4 > 0) {
                // Check if account already exists to avoid duplicates
                if (! $this->accounts()->where('kode', $rek['kode'])->exists()) {
                    $accountsToInsert[] = [
                        'dapur_id' => $this->id,
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
        }

        if (! empty($accountsToInsert)) {
            Account::insert($accountsToInsert);
        }
    }
}
