<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PoItemsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected PurchaseOrder $purchaseOrder;

    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip header/instruction rows
        if (empty($row['kode_material']) || str_contains($row['kode_material'], '---')) {
            return null;
        }

        $material = Material::where('code', $row['kode_material'])->first();

        if (!$material) {
            throw new \Exception("Material dengan kode '{$row['kode_material']}' tidak ditemukan.");
        }

        // Check if item already exists in PO
        $item = PurchaseOrderItem::where('purchase_order_id', $this->purchaseOrder->id)
            ->where('material_id', $material->id)
            ->first();

        if ($item) {
            $item->increment('quantity_to_order', $row['jumlah']);
            $item->increment('quantity_needed', $row['jumlah']);
            return null; // Return null because we modified existing model
        }

        return new PurchaseOrderItem([
            'purchase_order_id'    => $this->purchaseOrder->id,
            'material_id'         => $material->id,
            'quantity_needed'     => $row['jumlah'],
            'quantity_to_order'   => $row['jumlah'],
            'unit'                => $material->unit,
            'estimated_unit_price' => $material->price_estimate ?? 0,
            'item_status'         => 'pending',
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_material' => 'required',
            'jumlah'        => 'required|numeric|min:0.001',
        ];
    }
}
