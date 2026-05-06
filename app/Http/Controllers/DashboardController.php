<?php

namespace App\Http\Controllers;

use App\Enums\PoStatus;
use App\Models\Dapur;
use App\Models\Material;
use App\Models\MenuItem;
use App\Models\Period;
use App\Models\PoSupplierAssignment;
use App\Models\PurchaseOrder;
use App\Services\ProductionService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. DASHBOARD SUPPLIER
        if ($user->hasRole('supplier')) {
            $supplierId = $user->supplier_id;

            $stats = [
                'pending_orders' => PoSupplierAssignment::where('supplier_id', $supplierId)
                    ->whereIn('status', ['diteruskan', 'diterima', 'diproses'])
                    ->count(),
                'total_orders' => PoSupplierAssignment::where('supplier_id', $supplierId)->count(),
                'total_value' => PoSupplierAssignment::where('supplier_id', $supplierId)
                    ->get()
                    ->sum(fn ($a) => $a->quantity_assigned * $a->unit_price_agreed),
            ];

            $recentOrders = PurchaseOrder::whereNotIn('status', [
                PoStatus::DRAF,
                PoStatus::DIKIRIM_KE_YAYASAN,
                PoStatus::DIREVIEW_YAYASAN,
            ])
                ->whereHas('items.assignments', fn ($q) => $q->where('supplier_id', $supplierId))
                ->with(['dapur', 'items' => function ($q) use ($supplierId) {
                    $q->whereHas('assignments', fn ($sq) => $sq->where('supplier_id', $supplierId))
                        ->with(['material', 'assignments' => fn ($sq) => $sq->where('supplier_id', $supplierId)]);
                }])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.supplier', compact('stats', 'recentOrders'));
        }

        // 2. DASHBOARD INVESTOR
        if ($user->hasRole('investor')) {
            return view('dashboard.investor');
        }

        // 3. DASHBOARD DAPUR (KITCHEN)
        if ($user->hasRole(['kepala_dapur', 'koki'])) {
            $dapur = $user->dapur ?: Dapur::first();
            if ($dapur) {
                $schedules = app(ProductionService::class)->syncDailySchedules($dapur);
                $allDapurs = $user->dapur_id ? collect([$dapur]) : Dapur::orderBy('name')->get();

                return view('kitchen.index', compact('dapur', 'allDapurs', 'schedules'));
            }
        }

        // 4. DEFAULT DASHBOARD (ADMIN / YAYASAN)
        $stats = [
            'total_dapurs' => Dapur::count(),
            'total_materials' => Material::count(),
            'total_menu_items' => MenuItem::count(),
            'total_periods' => Period::count(),
        ];

        $lowStockCount = 0;
        $recentMenus = MenuItem::latest()->take(5)->get();

        return view('dashboard', compact('stats', 'lowStockCount', 'recentMenus'));
    }
}
