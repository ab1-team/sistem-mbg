<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArusKasRekening extends Model
{
    use HasFactory;

    protected $fillable = [
        'arus_kas_id',
        'rekening_debit',
        'rekening_kredit',
    ];

    public function arusKas(): BelongsTo
    {
        return $this->belongsTo(ArusKas::class, 'arus_kas_id');
    }
}
