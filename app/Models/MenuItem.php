<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'meal_type',
        'portion_size',
        'calories',
        'protein',
        'carbs',
        'fat',
        'fiber',
        'image',
        'created_by',
    ];

    protected $casts = [
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fat' => 'decimal:2',
        'fiber' => 'decimal:2',
    ];

    /**
     * Hitung ulang total nilai gizi berdasarkan komposisi bahan baku (BOM).
     * Sesuai Roadmap 2.2 (Kalkulasi Gizi Otomatis)
     */
    public function recalculateNutrition(): void
    {
        $totals = $this->boms()->with('material')->get()->reduce(function ($carry, $bom) {
            $qty = (float) $bom->quantity_per_portion;
            $material = $bom->material;

            if ($material) {
                $carry['calories'] += $qty * (float) ($material->calories ?? 0);
                $carry['protein'] += $qty * (float) ($material->protein ?? 0);
                $carry['carbs'] += $qty * (float) ($material->carbs ?? 0);
                $carry['fat'] += $qty * (float) ($material->fat ?? 0);
                $carry['fiber'] += $qty * (float) ($material->fiber ?? 0);
            }

            return $carry;
        }, [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'fiber' => 0,
        ]);

        $this->update($totals);
    }

    /**
     * Get the user who created the menu item.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the BOMs (Bill of Materials) for this menu item.
     */
    public function boms()
    {
        return $this->hasMany(MenuBom::class);
    }
}
