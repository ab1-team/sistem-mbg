<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use App\Models\Material;
use App\Models\MenuItem;
use App\Models\Period;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_dapurs' => Dapur::count(),
            'total_materials' => Material::count(),
            'total_menu_items' => MenuItem::count(),
            'total_periods' => Period::count(),
        ];

        // Placeholder for future logic (e.g., low stock)
        $lowStockCount = 0;

        $recentMenus = MenuItem::latest()->take(5)->get();

        return view('dashboard', compact('stats', 'lowStockCount', 'recentMenus'));
    }
}
