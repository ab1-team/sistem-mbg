<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'calories',
        'protein',
        'carbs',
        'fat',
        'fiber',
        'price_estimate',
        'min_stock_threshold',
        'is_active',
        'dapur_id',
    ];

    protected $casts = [
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fat' => 'decimal:2',
        'fiber' => 'decimal:2',
        'price_estimate' => 'decimal:2',
        'min_stock_threshold' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function dapur(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    /**
     * Get the BOMs associated with this material.
     */
    public function boms()
    {
        return $this->hasMany(MenuBom::class);
    }
}
