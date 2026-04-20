<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoSupplierAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_item_id',
        'supplier_id',
        'sub_supplier_id',
        'assigned_by',
        'quantity_assigned',
        'unit_price_agreed',
        'status',
        'rejection_reason',
        'responded_at',
        'shipped_at',
        'quantity_received',
        'is_fulfillment_closed',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'shipped_at' => 'datetime',
        'is_fulfillment_closed' => 'boolean',
        'quantity_received' => 'decimal:3',
    ];

    public function item()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function subSupplier()
    {
        return $this->belongsTo(SubSupplier::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function goodsReceiptItems()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
}
