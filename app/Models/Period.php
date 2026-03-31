<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'month',
        'year',
        'start_date',
        'end_date',
        'status',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the user who closed the period.
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
