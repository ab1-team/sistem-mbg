<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'balance',
        'last_transaction_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_transaction_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi polimorfik ke pemilik dompet (Dapur atau Investor)
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
