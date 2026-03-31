<?php

namespace App\Observers;

use App\Models\MenuBom;

class MenuBomObserver
{
    /**
     * Handle the MenuBom "created" event.
     */
    public function created(MenuBom $menuBom): void
    {
        $menuBom->menuItem->recalculateNutrition();
    }

    /**
     * Handle the MenuBom "updated" event.
     */
    public function updated(MenuBom $menuBom): void
    {
        $menuBom->menuItem->recalculateNutrition();
    }

    /**
     * Handle the MenuBom "deleted" event.
     */
    public function deleted(MenuBom $menuBom): void
    {
        $menuBom->menuItem->recalculateNutrition();
    }
}
