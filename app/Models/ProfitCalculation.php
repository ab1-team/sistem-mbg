<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function dapur(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function period(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function calculatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function distributions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DividendDistribution::class);
    }
}
