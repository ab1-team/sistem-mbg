<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'dapur_id',
        'kode_akun',
        'tahun',
        'debit_01', 'kredit_01', 'debit_02', 'kredit_02', 'debit_03', 'kredit_03',
        'debit_04', 'kredit_04', 'debit_05', 'kredit_05', 'debit_06', 'kredit_06',
        'debit_07', 'kredit_07', 'debit_08', 'kredit_08', 'debit_09', 'kredit_09',
        'debit_10', 'kredit_10', 'debit_11', 'kredit_11', 'debit_12', 'kredit_12',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }
}
