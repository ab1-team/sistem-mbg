<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfitCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'dapur_id',
        'period_id',
        'total_revenue',
        'total_cogs',
        'total_expenses',
        'gross_profit',
        'net_profit',
        'yayasan_share',
        'investor_total_share',
        'status',
        'calculated_by',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'total_cogs' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'yayasan_share' => 'decimal:2',
        'investor_total_share' => 'decimal:2',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(DividendDistribution::class);
    }
}
