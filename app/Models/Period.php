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

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    public static function getActive()
    {
        return self::where('status', 'open')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first() ?? self::where('status', 'open')->orderBy('start_date', 'desc')->first();
    }
}
