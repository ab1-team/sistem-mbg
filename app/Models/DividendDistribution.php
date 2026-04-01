<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function profitCalculation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProfitCalculation::class);
    }

    public function investor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }
}
