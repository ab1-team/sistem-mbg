<?php

namespace App\Models;

use App\Enums\CookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_schedule_id',
        'dapur_id',
        'status',
        'started_at',
        'completed_at',
        'distributed_at',
        'cooked_at', // Legacy/Alias for compatibility
        'cooked_by',
        'portions_cooked',
        'notes',
    ];

    protected $casts = [
        'status' => CookingStatus::class,
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'distributed_at' => 'datetime',
        'cooked_at' => 'datetime',
    ];

    public function menuSchedule()
    {
        return $this->belongsTo(MenuSchedule::class);
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class);
    }

    public function koki()
    {
        return $this->belongsTo(User::class, 'cooked_by');
    }
}
