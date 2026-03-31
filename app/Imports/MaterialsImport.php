<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MaterialsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Material([
            'code'                => $row['kode'],
            'name'                => $row['nama'],
            'category'            => strtolower($row['kategori']),
            'unit'                => $row['satuan'],
            'price_estimate'      => $row['estimasi_harga'] ?? 0,
            'min_stock_threshold' => $row['min_stok'] ?? 0,
            'is_active'           => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:30|unique:materials,code',
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:sayuran,daging,ikan,bumbu,sembako,minuman,lainnya',
            'satuan' => 'required|string|max:20',
            'estimasi_harga' => 'nullable|numeric|min:0',
            'min_stok' => 'nullable|numeric|min:0',
        ];
    }
}
