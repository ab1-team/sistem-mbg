<?php

namespace App\Services;

use App\Models\Dapur;
use App\Models\GoodsReceipt;
use App\Models\Material;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Catat penambahan stok dari Goods Receipt.
     */
    public function recordIngress(Dapur $dapur, Material $material, float $quantity, GoodsReceipt $gr, ?string $notes = null): Stock
    {
        return DB::transaction(function () use ($dapur, $material, $quantity, $gr, $notes) {
            $stock = Stock::firstOrCreate(
                [
                    'dapur_id' => $dapur->id,
                    'material_id' => $material->id,
                ],
                [
                    'current_stock' => 0,
                ]
            );

            $stock->increment('current_stock', $quantity);

            StockMovement::create([
                'dapur_id' => $dapur->id,
                'material_id' => $material->id,
                'type' => 'in',
                'quantity' => $quantity,
                'reference_type' => 'goods_receipt',
                'reference_id' => $gr->id,
                'created_by' => auth()->id(),
                'notes' => $notes ?? "Penerimaan barang via {$gr->gr_number}",
            ]);

            return $stock;
        });
    }

    /**
     * Catat pengurangan stok (Misal: Cooking Schedule - Fase 5).
     */
    public function recordEgress(Dapur $dapur, Material $material, float $quantity, $reference, ?string $notes = null): Stock
    {
        return DB::transaction(function () use ($dapur, $material, $quantity, $reference, $notes) {
            $stock = Stock::where('dapur_id', $dapur->id)
                ->where('material_id', $material->id)
                ->firstOrFail();

            $stock->decrement('current_stock', $quantity);

            StockMovement::create([
                'dapur_id' => $dapur->id,
                'material_id' => $material->id,
                'type' => 'out',
                'quantity' => $quantity,
                'reference_type' => get_class($reference),
                'reference_id' => $reference->id,
                'created_by' => auth()->id(),
                'notes' => $notes ?? 'Pengeluaran stok barang',
            ]);

            // Check for Low Stock (Roadmap 3.2)
            $threshold = $stock->material?->min_stock_threshold ?? 5; // Fallback to 5 units
            if ($stock->current_stock <= $threshold) {
                // Notify Logistics/Admins
                $logistiks = User::role(['logistik', 'admin_yayasan'])->get();
                foreach ($logistiks as $logistik) {
                    $logistik->notify(new LowStockAlert($stock));
                }
            }

            return $stock;
        });
    }
}
