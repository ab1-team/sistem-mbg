<?php

namespace App\Models;

use App\Enums\PoStatus;
use Illuminate\Database\Eloquent\Model;

class PoStatusHistory extends Model
{
    // Indikasi tabel jika perlu, tapi Laravel jamak otomatis sudah benar (po_status_histories)

    public $timestamps = false; // Karena kita pakai created_at manual di migrasi

    protected $fillable = [
        'purchase_order_id',
        'from_status',
        'to_status',
        'changed_by',
        'reason',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'from_status' => PoStatus::class,
        'to_status' => PoStatus::class,
        'metadata' => 'json',
        'created_at' => 'datetime',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
