<?php

namespace App\Imports;

use App\Models\Material;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MaterialsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        // Skip baris instruksi atau baris kosong
        if (empty($row['kode']) || str_contains($row['kode'], '---') || str_contains($row['kode'], 'PETUNJUK')) {
            return null;
        }

        return new Material([
            'code' => $row['kode'],
            'name' => $row['nama'],
            'category' => strtolower($row['kategori']),
            'unit' => $row['satuan'],
            'calories' => $row['kalori'] ?? 0,
            'protein' => $row['protein'] ?? 0,
            'carbs' => $row['karbo'] ?? 0,
            'fat' => $row['lemak'] ?? 0,
            'fiber' => $row['serat'] ?? 0,
            'price_estimate' => $row['estimasi_harga'] ?? 0,
            'min_stock_threshold' => $row['min_stok'] ?? 0,
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'nullable', // Boleh null untuk baris instruksi
            'nama' => 'required|string|max:150',
            'kategori' => 'nullable', // Boleh null untuk baris instruksi
            'satuan' => 'nullable', // Boleh null untuk baris instruksi
            'kalori' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'karbo' => 'nullable|numeric|min:0',
            'lemak' => 'nullable|numeric|min:0',
            'serat' => 'nullable|numeric|min:0',
            'estimasi_harga' => 'nullable|numeric|min:0',
            'min_stok' => 'nullable|numeric|min:0',
        ];
    }
}
