<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'po_items';

    protected $fillable = [
        'purchase_order_id',
        'material_id',
        'quantity_needed',
        'quantity_in_stock',
        'quantity_to_order',
        'unit',
        'estimated_unit_price',
        'actual_unit_price',
        'quantity_received',
        'item_status',
        'rejection_reason',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function assignments()
    {
        return $this->hasMany(PoSupplierAssignment::class, 'po_item_id');
    }
}
