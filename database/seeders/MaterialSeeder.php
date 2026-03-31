<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            // SEMBAKO (Karbohidrat & Minyak)
            ['code' => 'MAT001', 'name' => 'Beras Putih', 'category' => 'sembako', 'unit' => 'kg', 'calories' => 365, 'protein' => 7, 'carbs' => 80, 'fat' => 0.6, 'fiber' => 1.3],
            ['code' => 'MAT002', 'name' => 'Jagung Pipilan', 'category' => 'sembako', 'unit' => 'kg', 'calories' => 355, 'protein' => 9.2, 'carbs' => 73.7, 'fat' => 3.9, 'fiber' => 2.2],
            ['code' => 'MAT003', 'name' => 'Kentang', 'category' => 'sembako', 'unit' => 'kg', 'calories' => 77, 'protein' => 2, 'carbs' => 17, 'fat' => 0.1, 'fiber' => 2.2],
            ['code' => 'MAT040', 'name' => 'Minyak Goreng Sawit', 'category' => 'sembako', 'unit' => 'liter', 'calories' => 884, 'protein' => 0, 'carbs' => 0, 'fat' => 100, 'fiber' => 0],
            ['code' => 'MAT020', 'name' => 'Tempe Kedelai', 'category' => 'sembako', 'unit' => 'kg', 'calories' => 193, 'protein' => 19, 'carbs' => 9, 'fat' => 11, 'fiber' => 1.4],
            ['code' => 'MAT021', 'name' => 'Tahu Putih', 'category' => 'sembako', 'unit' => 'kg', 'calories' => 76, 'protein' => 8, 'carbs' => 1.9, 'fat' => 4.8, 'fiber' => 0.3],

            // DAGING
            ['code' => 'MAT010', 'name' => 'Daging Ayam (Fillet)', 'category' => 'daging', 'unit' => 'kg', 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6, 'fiber' => 0],
            ['code' => 'MAT011', 'name' => 'Daging Sapi (Has Dalam)', 'category' => 'daging', 'unit' => 'kg', 'calories' => 143, 'protein' => 26, 'carbs' => 0, 'fat' => 3.5, 'fiber' => 0],
            ['code' => 'MAT012', 'name' => 'Telur Ayam', 'category' => 'daging', 'unit' => 'kg', 'calories' => 155, 'protein' => 13, 'carbs' => 1.1, 'fat' => 11, 'fiber' => 0],

            // IKAN
            ['code' => 'MAT013', 'name' => 'Ikan Nila', 'category' => 'ikan', 'unit' => 'kg', 'calories' => 96, 'protein' => 20.1, 'carbs' => 0, 'fat' => 1.7, 'fiber' => 0],

            // SAYURAN
            ['code' => 'MAT030', 'name' => 'Bayam', 'category' => 'sayuran', 'unit' => 'kg', 'calories' => 23, 'protein' => 2.9, 'carbs' => 3.6, 'fat' => 0.4, 'fiber' => 2.2],
            ['code' => 'MAT031', 'name' => 'Wortel', 'category' => 'sayuran', 'unit' => 'kg', 'calories' => 41, 'protein' => 0.9, 'carbs' => 9.6, 'fat' => 0.2, 'fiber' => 2.8],
            ['code' => 'MAT032', 'name' => 'Kangkung', 'category' => 'sayuran', 'unit' => 'kg', 'calories' => 19, 'protein' => 2.6, 'carbs' => 3.1, 'fat' => 0.4, 'fiber' => 2.1],
            ['code' => 'MAT033', 'name' => 'Kubis (Kol)', 'category' => 'sayuran', 'unit' => 'kg', 'calories' => 25, 'protein' => 1.3, 'carbs' => 5.8, 'fat' => 0.1, 'fiber' => 2.5],

            // BUMBU
            ['code' => 'MAT041', 'name' => 'Garam Dapur', 'category' => 'bumbu', 'unit' => 'kg', 'calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0],
            ['code' => 'MAT042', 'name' => 'Gula Pasir', 'category' => 'bumbu', 'unit' => 'kg', 'calories' => 387, 'protein' => 0, 'carbs' => 99.8, 'fat' => 0, 'fiber' => 0],
            ['code' => 'MAT043', 'name' => 'Bawang Merah', 'category' => 'bumbu', 'unit' => 'kg', 'calories' => 72, 'protein' => 2.5, 'carbs' => 16.8, 'fat' => 0.1, 'fiber' => 0.6],
            ['code' => 'MAT044', 'name' => 'Bawang Putih', 'category' => 'bumbu', 'unit' => 'kg', 'calories' => 149, 'protein' => 6.4, 'carbs' => 33.1, 'fat' => 0.5, 'fiber' => 2.1],
            ['code' => 'MAT045', 'name' => 'Cabai Merah', 'category' => 'bumbu', 'unit' => 'kg', 'calories' => 40, 'protein' => 1.9, 'carbs' => 9.3, 'fat' => 0.4, 'fiber' => 1.5],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(['code' => $material['code']], array_merge($material, [
                'price_estimate' => 0,
                'min_stock_threshold' => 1.0,
                'is_active' => true,
            ]));
        }
    }
}
