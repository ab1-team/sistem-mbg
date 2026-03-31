<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'identity_number',
        'share_percentage',
        'join_date',
        'exit_date',
        'bank_name',
        'bank_account',
        'bank_holder',
        'is_active',
    ];

    protected $casts = [
        'share_percentage' => 'decimal:4',
        'join_date' => 'date',
        'exit_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the investor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet associated with the investor.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(InvestorWallet::class);
    }
}
