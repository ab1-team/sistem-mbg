<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPeriod extends Model
{
    use HasFactory;

    const STATUS_DRAF = 'draf';

    const STATUS_PENDING = 'menunggu_approval';

    const STATUS_APPROVED = 'disetujui';

    const STATUS_REJECTED = 'ditolak';

    protected $fillable = [
        'dapur_id',
        'period_id',
        'title',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_note',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($model) {
            // Jika ada perubahan pada data inti setelah disetujui, kembalikan ke Pending
            if ($model->status === self::STATUS_APPROVED && $model->isDirty(['title', 'dapur_id', 'period_id'])) {
                $model->status = self::STATUS_PENDING;
            }
        });
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function schedules()
    {
        return $this->hasMany(MenuSchedule::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
