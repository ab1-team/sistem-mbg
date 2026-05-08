<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip baris instruksi atau baris kosong
            if (empty($row['kode']) || str_contains($row['kode'], '---') || str_contains($row['kode'], 'PETUNJUK')) {
                continue;
            }

            // 1. Create/Update Material
            $material = Material::updateOrCreate(
                ['code' => $row['kode']],
                [
                    'name' => $row['nama'],
                    'category' => strtolower($row['kategori'] ?? 'lainnya'),
                    'unit' => $row['satuan'] ?? 'Pcs',
                    'calories' => $row['kalori'] ?? 0,
                    'protein' => $row['protein'] ?? 0,
                    'carbs' => $row['karbo'] ?? 0,
                    'fat' => $row['lemak'] ?? 0,
                    'fiber' => $row['serat'] ?? 0,
                    'price_estimate' => $row['estimasi_harga'] ?? 0,
                    'min_stock_threshold' => $row['min_stok'] ?? 0,
                    'is_active' => true,
                ]
            );

            // 2. Handle Dynamic Suppliers
            $supplierIds = [];

            // Loop through all keys that start with 'supplier_'
            foreach ($row as $key => $value) {
                if (str_starts_with($key, 'supplier_') && ! empty($value)) {
                    $supplierCode = trim($value);

                    // Find or create supplier by CODE
                    $supplier = Supplier::firstOrCreate(
                        ['code' => $supplierCode],
                        [
                            'name' => $supplierCode, // Nama disamakan dengan kode jika baru
                            'category' => 'lainnya',
                            'is_active' => true,
                        ]
                    );

                    $supplierIds[] = $supplier->id;
                }
            }

            // Sync suppliers to material
            if (! empty($supplierIds)) {
                $material->suppliers()->syncWithoutDetaching($supplierIds);
            }
        }
    }
}
