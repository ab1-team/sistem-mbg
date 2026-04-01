<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_period_id',
        'menu_item_id',
        'serve_date',
        'meal_type',
        'target_portions',
        'notes',
    ];

    protected $casts = [
        'serve_date' => 'date',
    ];

    public function menuPeriod()
    {
        return $this->belongsTo(MenuPeriod::class);
    }

    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_schedule_items');
    }

    /**
     * Legacy single item relationship.
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
