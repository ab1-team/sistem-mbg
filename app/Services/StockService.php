<?php

namespace App\Services;

use App\Models\Dapur;
use App\Models\Material;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\GoodsReceipt;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Catat penambahan stok dari Goods Receipt.
     */
    public function recordIngress(Dapur $dapur, Material $material, float $quantity, GoodsReceipt $gr, string $notes = null): Stock
    {
        return DB::transaction(function () use ($dapur, $material, $quantity, $gr, $notes) {
            $stock = Stock::firstOrCreate(
                [
                    'dapur_id' => $dapur->id,
                    'material_id' => $material->id
                ],
                [
                    'current_stock' => 0
                ]
            );

            $stock->increment('current_stock', $quantity);

            StockMovement::create([
                'stock_id' => $stock->id,
                'type' => 'masuk',
                'quantity' => $quantity,
                'reference_type' => 'goods_receipt',
                'reference_id' => $gr->id,
                'performed_by' => auth()->id(),
                'notes' => $notes ?? "Penerimaan barang via {$gr->gr_number}",
            ]);

            return $stock;
        });
    }

    /**
     * Catat pengurangan stok (Misal: Cooking Schedule - Fase 5).
     */
    public function recordEgress(Dapur $dapur, Material $material, float $quantity, $reference, string $notes = null): Stock
    {
        return DB::transaction(function () use ($dapur, $material, $quantity, $reference, $notes) {
            $stock = Stock::where('dapur_id', $dapur->id)
                ->where('material_id', $material->id)
                ->firstOrFail();

            $stock->decrement('current_stock', $quantity);

            StockMovement::create([
                'stock_id' => $stock->id,
                'type' => 'keluar',
                'quantity' => $quantity,
                'reference_type' => get_class($reference),
                'reference_id' => $reference->id,
                'performed_by' => auth()->id(),
                'notes' => $notes ?? "Pengeluaran stok barang",
            ]);

            return $stock;
        });
    }
}
