<?php

namespace App\Imports;

use App\Models\MenuItem;
use App\Models\MenuBom;
use App\Models\Material;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MenuItemsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $dapur_id;

    public function __construct($dapur_id = null)
    {
        $this->dapur_id = $dapur_id;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip baris instruksi atau baris kosong
            if (empty($row['nama']) || str_contains($row['nama'], '---') || str_contains($row['nama'], 'PETUNJUK')) {
                continue;
            }

            $menuItem = MenuItem::create([
                'name'         => $row['nama'],
                'meal_type'    => strtolower($this->mapTipeMakan($row['tipe_makan'])),
                'portion_size' => $row['porsi'] ?? 1,
                'description'  => $row['keterangan'] ?? null,
                'calories'     => $row['kalori'] ?? 0,
                'protein'      => $row['protein'] ?? 0,
                'carbs'        => $row['karbo'] ?? 0,
                'fat'          => $row['lemak'] ?? 0,
                'fiber'        => $row['serat'] ?? 0,
                'is_active'    => true,
                'created_by'   => auth()->id() ?? 1,
                'dapur_id'     => $this->dapur_id,
            ]);

            // Handle komposisi bahan jika ada
            // Format: KODE:JUMLAH|KODE:JUMLAH
            if (!empty($row['komposisi_bahan'])) {
                $items = explode('|', $row['komposisi_bahan']);
                foreach ($items as $item) {
                    $parts = explode(':', $item);
                    if (count($parts) >= 2) {
                        $code = trim($parts[0]);
                        $qty = (float) trim($parts[1]);
                        
                        $material = Material::where('code', $code)->first();
                        if ($material) {
                            MenuBom::create([
                                'menu_item_id' => $menuItem->id,
                                'material_id' => $material->id,
                                'quantity_per_portion' => $qty,
                                'unit' => $material->unit,
                            ]);
                        }
                    }
                }
                
                // Recalculate nutrition logic based on BOMs
                $menuItem->recalculateNutrition();
            }
        }
    }

    private function mapTipeMakan($tipe)
    {
        $map = [
            'sarapan' => 'pagi',
            'makan_siang' => 'siang',
            'makan_malam' => 'sore',
        ];
        $val = strtolower(str_replace(' ', '_', $tipe));
        return $map[$val] ?? $val;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'tipe_makan' => 'nullable', // Boleh null untuk baris instruksi yang akan diskip
            'porsi' => 'nullable|numeric|min:0',
            'kalori' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'karbo' => 'nullable|numeric|min:0',
            'lemak' => 'nullable|numeric|min:0',
            'serat' => 'nullable|numeric|min:0',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        // Jika ini baris instruksi, biarkan tipe_makan kosong agar tidak error
        // Kita tidak mensyaratkan tipe_makan di rules() untuk fleksibilitas skipping
        return $data;
    }
}
