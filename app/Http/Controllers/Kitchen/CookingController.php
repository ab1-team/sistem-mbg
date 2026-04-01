<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\CookingSchedule;
use App\Models\Dapur;
use App\Models\Stock;
use App\Services\ProductionService;
use Illuminate\Http\Request;

class CookingController extends Controller
{
    protected $productionService;

    public function __construct(ProductionService $productionService)
    {
        $this->productionService = $productionService;
    }

    /**
     * Tampilkan dashboard operasional koki harian.
     * Sesuai Roadmap 5.1 & 5.2
     */
    public function index(Request $request)
    {
        // Untuk demo/testing: ambil dapur pertama jika koki belum ter-assign secara ketat
        $dapur = auth()->user()->dapur ?? Dapur::first();

        if (! $dapur) {
            return redirect()->back()->with('error', 'Dapur tidak ditemukan untuk user ini.');
        }

        // Sinkronisasi jadwal hari ini
        $schedules = $this->productionService->syncDailySchedules($dapur);

        return view('kitchen.index', [
            'dapur' => $dapur,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Mulai proses memasak.
     */
    public function start(CookingSchedule $schedule)
    {
        $this->productionService->startCooking($schedule);

        return redirect()->back()->with('success', "Memulai memasak: {$schedule->menuSchedule->menuItem->name}");
    }

    /**
     * Selesaikan proses memasak dan potong stok.
     */
    public function finish(Request $request, CookingSchedule $schedule)
    {
        $request->validate([
            'portions_cooked' => 'required|integer|min:0',
        ]);

        $this->productionService->completeCooking($schedule, $request->portions_cooked);

        return redirect()->back()->with('success', "Produksi selesai: {$schedule->menuSchedule->menuItem->name}");
    }

    /**
     * Distribusikan porsi makanan.
     */
    public function distribute(CookingSchedule $schedule)
    {
        $this->productionService->distribute($schedule);

        return redirect()->back()->with('success', "Makanan didistribusikan: {$schedule->menuSchedule->menuItem->name}");
    }

    /**
     * Tampilkan stok inventaris dapur saat ini.
     * Sesuai Roadmap 5.3
     */
    public function inventory()
    {
        $dapur = auth()->user()->dapur ?? Dapur::first();

        $stocks = Stock::with('material')
            ->where('dapur_id', $dapur->id)
            ->get();

        return view('kitchen.inventory', [
            'dapur' => $dapur,
            'stocks' => $stocks,
        ]);
    }
}
