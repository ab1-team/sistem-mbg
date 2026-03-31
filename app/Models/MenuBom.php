<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuBom extends Model
{
    use HasFactory;

    protected $table = 'menu_boms';

    protected $fillable = [
        'menu_item_id',
        'material_id',
        'quantity_per_portion',
        'unit',
        'notes',
    ];

    protected $casts = [
        'quantity_per_portion' => 'decimal:4',
    ];

    /**
     * Get the menu item that owns the BOM.
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the material that belongs to the BOM.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
