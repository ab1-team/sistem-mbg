<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'dapur_id',
        'period_id',
        'reference_type',
        'reference_id',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function dapur(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function period(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
}
