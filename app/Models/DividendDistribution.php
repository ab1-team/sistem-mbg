<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DividendDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'profit_calculation_id',
        'investor_id',
        'share_percentage',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'share_percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function profitCalculation(): BelongsTo
    {
        return $this->belongsTo(ProfitCalculation::class);
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }
}
