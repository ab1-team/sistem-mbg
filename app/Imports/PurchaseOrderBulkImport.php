<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;

class PurchaseOrderBulkImport implements ToCollection
{
    protected PurchaseOrder $purchaseOrder;

    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function collection(Collection $rows)
    {
        $startProcessing = false;
        $parsedRows = [];
        $materialNames = [];

        // 1. Parsing data dan kumpulkan nama material
        foreach ($rows as $row) {
            $no = trim($row[0] ?? '');
            if (! $startProcessing) {
                if (is_numeric($no)) {
                    $startProcessing = true;
                } else {
                    continue;
                }
            }

            $name = trim($row[1] ?? '');
            if (empty($name) || strtoupper($name) === 'TOTAL') {
                break;
            }

            $quantity = $this->parseNumber($row[2] ?? 0);
            $unit = trim($row[3] ?? '');
            $price = $this->parseNumber($row[4] ?? 0);

            $parsedRows[] = [
                'name' => $name,
                'quantity' => $quantity,
                'unit' => $unit,
                'price' => $price,
            ];

            $materialNames[] = $name;
        }

        if (empty($parsedRows)) {
            return;
        }

        // 2. Ambil semua material yang sudah ada di database dalam 1 query
        $existingMaterials = Material::whereIn('name', array_unique($materialNames))
            ->pluck('id', 'name')
            ->toArray();

        $existingMaterialUnits = Material::whereIn('name', array_unique($materialNames))
            ->pluck('unit', 'name')
            ->toArray();

        $newMaterialsData = [];
        $poItemsData = [];
        $now = now();

        // 3. Persiapkan data material baru jika belum ada
        foreach ($parsedRows as $data) {
            if (! isset($existingMaterials[$data['name']]) && ! isset($newMaterialsData[$data['name']])) {
                $newMaterialsData[$data['name']] = [
                    'code' => 'MAT-'.strtoupper(Str::random(6)),
                    'name' => $data['name'],
                    'category' => 'lainnya',
                    'unit' => $data['unit'] ?: 'Pcs',
                    'price_estimate' => $data['price'],
                    'dapur_id' => $this->purchaseOrder->dapur_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // 4. Insert material baru dalam 1 query
        if (! empty($newMaterialsData)) {
            Material::insert(array_values($newMaterialsData));

            // Ambil ID dari material yang baru saja diinsert
            $newMaterialNames = array_keys($newMaterialsData);
            $newlyCreatedMaterials = Material::whereIn('name', $newMaterialNames)->get();

            foreach ($newlyCreatedMaterials as $mat) {
                $existingMaterials[$mat->name] = $mat->id;
                $existingMaterialUnits[$mat->name] = $mat->unit;
            }
        }

        // 5. Persiapkan data Purchase Order Item
        foreach ($parsedRows as $data) {
            $materialId = $existingMaterials[$data['name']] ?? null;
            $materialUnit = $existingMaterialUnits[$data['name']] ?? 'Pcs';

            if ($materialId) {
                $poItemsData[] = [
                    'purchase_order_id' => $this->purchaseOrder->id,
                    'material_id' => $materialId,
                    'quantity_needed' => $data['quantity'],
                    'quantity_in_stock' => 0,
                    'quantity_to_order' => $data['quantity'],
                    'unit' => $data['unit'] ?: $materialUnit,
                    'estimated_unit_price' => $data['price'],
                    'item_status' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // 6. Insert semua PO Item dalam 1 query
        if (! empty($poItemsData)) {
            PurchaseOrderItem::insert($poItemsData);
        }
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (empty($value)) {
            return 0;
        }

        // Remove "Rp", whitespace, and thousand separator (dots)
        $clean = preg_replace('/[RrPp\s]/', '', (string) $value);

        // If there's a comma, it might be a decimal separator (Indonesian style)
        // or it might be thousand separator (English style).
        // Standard Indonesian format: 1.000.000,00
        // Standard English format: 1,000,000.00

        // Check if there are dots AND commas
        if (str_contains($clean, '.') && str_contains($clean, ',')) {
            // Likely Indonesian: dot is thousand, comma is decimal
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (str_contains($clean, ',')) {
            // Only comma. Could be 1,000 (thousand) or 1,5 (decimal).
            // Usually in our context it's 1.000 (Indonesian dots) or 1,5 (Indonesian comma).
            // But Excel often gives numbers as floats.

            // If it looks like a decimal (e.g. 1,5), convert to 1.5
            // If it looks like a thousand (e.g. 1,000), it's tricky.
            // Let's assume comma is decimal if it's towards the end.
            $clean = str_replace(',', '.', $clean);
        } elseif (str_contains($clean, '.')) {
            // Only dot. Could be 1.000 (thousand) or 1.5 (decimal).
            // In Indonesian it's usually thousand.
            // But if it's like 1.5, it's decimal.
            // If it's 1.000, it's thousand.
            // Let's check length after dot.
            $parts = explode('.', $clean);
            if (count($parts) > 2 || strlen(end($parts)) === 3) {
                // Thousand separator
                $clean = str_replace('.', '', $clean);
            }
        }

        return (float) $clean;
    }
}
