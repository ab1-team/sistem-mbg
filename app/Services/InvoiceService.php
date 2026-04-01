<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Generate Invoices from Purchase Order (Split by Supplier).
     * This ensures each supplier gets their own bill.
     */
    public function generateFromPo(PurchaseOrder $purchaseOrder): array
    {
        return DB::transaction(function () use ($purchaseOrder) {
            $invoices = [];
            
            // 1. Group items by supplier_id
            // We only care about items that were actually received
            $receivedItems = $purchaseOrder->items->where('quantity_received', '>', 0);
            
            $itemsBySupplier = $receivedItems->groupBy(function ($item) {
                // Get supplier_id from assignments
                return $item->assignments->first()->supplier_id ?? 1;
            });

            foreach ($itemsBySupplier as $supplierId => $items) {
                // Generate Unique Invoice Number for this supplier
                $invoiceNumber = 'INV-' . $purchaseOrder->dapur->code . '-' . $supplierId . '-' . now()->format('YmdHis');

                // Calculate total for this specific supplier
                $supplierTotal = $items->sum(function ($item) {
                    $price = $item->actual_unit_price ?? $item->estimated_unit_price ?? 0;
                    return (float)$item->quantity_received * (float)$price;
                });

                // 2. Create Header Invoice for this Supplier
                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'purchase_order_id' => $purchaseOrder->id,
                    'supplier_id' => $supplierId,
                    'dapur_id' => $purchaseOrder->dapur_id,
                    'total_amount' => $supplierTotal,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'grand_total' => $supplierTotal,
                    'status' => 'generated',
                    'due_date' => now()->addDays(14),
                ]);

                // 3. Create Detail Invoice Items
                foreach ($items as $item) {
                    $price = $item->actual_unit_price ?? $item->estimated_unit_price ?? 0;
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'po_item_id' => $item->id,
                        'material_id' => $item->material_id,
                        'quantity' => $item->quantity_received,
                        'unit_price' => $price,
                        'total_price' => (float)$item->quantity_received * (float)$price,
                    ]);
                }

                // 4. Create separate Expense record for this supplier
                Expense::create([
                    'dapur_id' => $purchaseOrder->dapur_id,
                    'period_id' => $purchaseOrder->menuPeriod->period_id ?? 1,
                    'category' => 'bahan_baku',
                    'amount' => $supplierTotal,
                    'notes' => "Tagihan otomatis dari {$invoice->invoice_number} (Supplier ID: {$supplierId}, PO: {$purchaseOrder->po_number})",
                    'created_by' => auth()->id() ?? $purchaseOrder->created_by ?? 1,
                    'expense_date' => now(),
                ]);

                $invoices[] = $invoice;
            }

            return $invoices;
        });
    }
}
