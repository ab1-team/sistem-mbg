<?php

namespace App\Services;

use App\Enums\CookingStatus;
use App\Models\CookingSchedule;
use App\Models\Dapur;
use App\Models\MenuSchedule;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Sinkronisasi jadwal masak hari ini dari perencanaan menu (Fase 5.1).
     */
    public function syncDailySchedules(Dapur $dapur, $date = null)
    {
        $date = $date ?? now()->format('Y-m-d');

        // Tarik menu yang sudah di-approve untuk hari ini
        $menuSchedules = MenuSchedule::whereHas('menuPeriod', function ($q) use ($dapur) {
            $q->where('dapur_id', $dapur->id)
                ->where('status', 'disetujui');
        })
            ->where('serve_date', $date)
            ->get();

        foreach ($menuSchedules as $menu) {
            // Cek apakah sudah ada di jadwal masak (CookingSchedule)
            CookingSchedule::firstOrCreate(
                [
                    'menu_schedule_id' => $menu->id,
                    'dapur_id' => $dapur->id,
                ],
                [
                    'status' => CookingStatus::BELUM_MULAI,
                    'portions_cooked' => 0,
                ]
            );
        }

        return CookingSchedule::where('dapur_id', $dapur->id)
            ->whereHas('menuSchedule', function ($q) use ($date) {
                $q->where('serve_date', $date);
            })->get();
    }

    /**
     * Pindah ke status persiapan (Fase 5.2).
     */
    public function prepareCooking(CookingSchedule $schedule)
    {
        return $schedule->update([
            'status' => CookingStatus::PERSIAPAN,
            'prepared_at' => now(), // Menambahkan timestamp persiapan
        ]);
    }

    /**
     * Mulai proses memasak (Fase 5.2).
     */
    public function startCooking(CookingSchedule $schedule)
    {
        return $schedule->update([
            'status' => CookingStatus::MEMASAK,
            'started_at' => now(),
            'cooked_by' => auth()->id() ?? 1,
        ]);
    }

    /**
     * Selesaikan proses memasak dan potong stok otomatis (Fase 5.3).
     */
    public function completeCooking(CookingSchedule $schedule, int $actualPortions)
    {
        return DB::transaction(function () use ($schedule, $actualPortions) {
            $schedule->load(['menuSchedule.items.boms.material']);

            // 1. Update status memasak
            $schedule->update([
                'status' => CookingStatus::SELESAI,
                'portions_cooked' => $actualPortions,
                'completed_at' => now(),
                'cooked_by' => auth()->id() ?? 1,
            ]);

            // 2. Kalkulasi & Potong Stok berdasarkan BOM (Loop Multiple Items)
            foreach ($schedule->menuSchedule->items as $menuItem) {
                foreach ($menuItem->boms as $bom) {
                    $totalUsage = (float) $bom->quantity_per_portion * $actualPortions;

                    // Catat pengurangan stok via StockService
                    $this->stockService->recordEgress(
                        $schedule->dapur,
                        $bom->material,
                        $totalUsage,
                        $schedule,
                        "Pemakaian bahan baku untuk masak [{$menuItem->name}] - {$actualPortions} porsi"
                    );
                }
            }

            return $schedule;
        });
    }

    /**
     * Tandai makanan sudah didistribusikan (Fase 5.2).
     */
    public function distribute(CookingSchedule $schedule)
    {
        return $schedule->update([
            'status' => CookingStatus::DIDISTRIBUSIKAN,
            'distributed_at' => now(),
        ]);
    }
}
