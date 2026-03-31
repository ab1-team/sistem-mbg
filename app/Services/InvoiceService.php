<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Expense;
use App\Enums\PoStatus;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate Invoice dari Purchase Order (Lengkap/Sebagian).
     * Sesuai Roadmap 4.2
     */
    public function generateFromPo(PurchaseOrder $purchaseOrder): ?Invoice
    {
        return DB::transaction(function () use ($purchaseOrder) {
            
            // Generate Nomor Invoice
            $invoiceNumber = 'INV-' . $purchaseOrder->dapur->code . '-' . now()->format('Ymd-His');

            // Kalkulasi Total dari Harga Aktual PO
            $totalAmount = $purchaseOrder->items->all()->sum(function ($item) {
                $price = $item->actual_unit_price ?? $item->estimated_unit_price ?? 0;
                return $item->quantity_received * (float) $price;
            });

            // 1. Buat Header Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'purchase_order_id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->items->first()->assignments->first()->supplier_id ?? 1,
                'dapur_id' => $purchaseOrder->dapur_id,
                'total_amount' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'grand_total' => $totalAmount,
                'status' => 'generated',
                'due_date' => now()->addDays(14),
            ]);

            // 2. Buat Detail Invoice
            foreach ($purchaseOrder->items as $item) {
                if ($item->quantity_received > 0) {
                    $price = $item->actual_unit_price ?? $item->estimated_unit_price ?? 0;
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'po_item_id' => $item->id,
                        'material_id' => $item->material_id,
                        'quantity' => $item->quantity_received,
                        'unit_price' => $price,
                        'total_price' => $item->quantity_received * $price,
                    ]);
                }
            }

            // 3. Otomasi Pencatatan Beban (Fase 4.2)
            Expense::create([
                'dapur_id' => $purchaseOrder->dapur_id,
                'period_id' => $purchaseOrder->menuPeriod->period_id ?? 1, // Link ke periode akuntansi
                'category' => 'bahan_baku',
                'amount' => $totalAmount,
                'notes' => "Tagihan otomatis dari {$invoice->invoice_number} (PO: {$purchaseOrder->po_number})",
                'created_by' => auth()->id() ?? $purchaseOrder->created_by,
            ]);

            return $invoice;
        });
    }
}
