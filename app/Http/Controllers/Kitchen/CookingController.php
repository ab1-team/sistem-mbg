<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\CookingSchedule;
use App\Models\Dapur;
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
        $user = auth()->user();
        $dapurId = $request->get('dapur_id');

        // Force dapur_id jika user terikat dapur tertentu
        if ($user->dapur_id) {
            $dapur = $user->dapur;
        } elseif ($dapurId) {
            $dapur = Dapur::find($dapurId);
        }

        // Fallback jika belum ada yang terpilih
        if (!isset($dapur) || !$dapur) {
            $dapur = Dapur::first();
        }

        if (!$dapur) {
            return redirect()->back()->with('error', 'Dapur tidak ditemukan.');
        }

        // Cek akses (Double check)
        if ($user->dapur_id && $dapur->id !== $user->dapur_id) {
            $dapur = $user->dapur;
        }

        // Sinkronisasi jadwal hari ini
        $schedules = $this->productionService->syncDailySchedules($dapur);

        $allDapurs = $user->dapur_id ? collect([$dapur]) : Dapur::orderBy('name')->get();

        return view('kitchen.index', [
            'dapur' => $dapur,
            'allDapurs' => $allDapurs,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Masuk ke fase persiapan.
     */
    public function prepare(CookingSchedule $schedule)
    {
        $this->productionService->prepareCooking($schedule);

        return redirect()->back()->with('success', "Persiapan dimulai: {$schedule->menuSchedule->menuItem->name}");
    }

    /**
     * Mulai proses memasak.
     */
    public function start(CookingSchedule $schedule)
    {
        $this->productionService->startCooking($schedule);

        return redirect()->back()->with('success', "Sedang memasak: {$schedule->menuSchedule->menuItem->name}");
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

        return redirect()->back()->with('success', "Produksi selesai & stok terpotong: {$schedule->menuSchedule->menuItem->name}");
    }

    /**
     * Distribusikan porsi makanan.
     */
    public function distribute(CookingSchedule $schedule)
    {
        $this->productionService->distribute($schedule);

        return redirect()->back()->with('success', "Berhasil didistribusikan: {$schedule->menuSchedule->menuItem->name}");
    }

    /**
     * Tampilkan stok inventaris dapur saat ini.
     * Sesuai Roadmap 5.3
     */
    public function inventory(Request $request)
    {
        $user = auth()->user();
        $dapurId = $request->get('dapur_id');

        // Force dapur_id jika user terikat dapur tertentu
        if ($user->dapur_id) {
            $dapur = $user->dapur;
        } elseif ($dapurId) {
            $dapur = Dapur::find($dapurId);
        }

        // Fallback jika belum ada yang terpilih
        if (!isset($dapur) || !$dapur) {
            $dapur = Dapur::first();
        }

        if (!$dapur) {
            return redirect()->back()->with('error', 'Dapur tidak ditemukan.');
        }

        // Cek akses (Double check)
        if ($user->dapur_id && $dapur->id !== $user->dapur_id) {
            $dapur = $user->dapur;
        }

        $allDapurs = $user->dapur_id ? collect([$dapur]) : Dapur::orderBy('name')->get();

        return view('kitchen.inventory', [
            'dapur' => $dapur,
            'allDapurs' => $allDapurs,
        ]);
    }
}
