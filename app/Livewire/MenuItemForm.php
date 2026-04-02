<?php

namespace App\Livewire;

use App\Models\Dapur;
use App\Models\Material;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MenuItemForm extends Component
{
    public $menuItem;

    public $isEdit = false;

    // Menu Fields
    public $name;

    public $description;

    public $meal_type = 'sarapan';

    public $portion_size = 1;

    public $calories = 0;

    public $protein = 0;

    public $carbs = 0;

    public $fat = 0;

    public $fiber = 0;

    public $image;

    public $dapur_id;

    // BOM Rows
    public $rows = [];

    public $allMaterials = [];

    protected $rules = [
        'name' => 'required|string|max:150',
        'description' => 'nullable|string',
        'meal_type' => 'required|in:sarapan,makan_siang,makan_malam,snack',
        'portion_size' => 'required|integer|min:1',
        'rows.*.material_id' => 'required|exists:materials,id',
        'rows.*.quantity' => 'required|numeric|min:0.0001',
    ];

    public function mount($menuItem = null)
    {
        $user = auth()->user();

        if ($menuItem) {
            $this->menuItem = $menuItem;
            $this->isEdit = true;

            // Cek akses edit jika user terikat dapur tertentu
            if ($user->dapur_id && $menuItem->dapur_id && $menuItem->dapur_id !== $user->dapur_id) {
                return redirect()->route('menu-items.index')->with('error', 'Anda tidak memiliki akses ke menu ini.');
            }

            $this->name = $menuItem->name;
            $this->description = $menuItem->description;
            $this->meal_type = $menuItem->meal_type;
            $this->portion_size = $menuItem->portion_size;
            $this->calories = $menuItem->calories;
            $this->protein = $menuItem->protein;
            $this->carbs = $menuItem->carbs;
            $this->fat = $menuItem->fat;
            $this->fiber = $menuItem->fiber;
            $this->dapur_id = $menuItem->dapur_id;

            foreach ($menuItem->boms as $bom) {
                $this->rows[] = [
                    'id' => $bom->id,
                    'material_id' => $bom->material_id,
                    'quantity' => (float) $bom->quantity_per_portion * (int) $this->portion_size,
                    'unit' => $bom->unit,
                ];
            }
        } else {
            // Default dapur_id dari user jika ada
            $this->dapur_id = $user->dapur_id;
        }

        $this->loadMaterials();

        if (empty($this->rows)) {
            $this->rows[] = [
                'material_id' => '',
                'quantity' => 1,
                'unit' => '-',
            ];
        }
    }

    public function updatedDapurId(): void
    {
        $this->loadMaterials();
    }

    protected function loadMaterials(): void
    {
        $user = auth()->user();
        $targetDapurId = $user->dapur_id ?? $this->dapur_id;

        $this->allMaterials = Material::where('is_active', true)
            ->where(function ($query) use ($targetDapurId) {
                $query->whereNull('dapur_id')
                    ->when($targetDapurId, function ($q) use ($targetDapurId) {
                        $q->orWhere('dapur_id', $targetDapurId);
                    });
            })
            ->orderBy('name')
            ->get();
    }

    public function updatedRows($value, $key): void
    {
        // Auto-fill unit saat material_id dipilih
        if (str_ends_with($key, '.material_id')) {
            if (preg_match('/(?:rows\.)?(\d+)\.material_id/', $key, $matches)) {
                $index = (int) $matches[1];
                $material = collect($this->allMaterials)->firstWhere('id', $value);
                $this->rows[$index]['unit'] = $material?->unit ?? '-';
            }
        }
    }

    public function addRow(): void
    {
        $this->rows[] = [
            'material_id' => '',
            'quantity' => 1,
            'unit' => '-',
        ];
    }

    public function removeRow(int $index): void
    {
        if (count($this->rows) > 1) {
            array_splice($this->rows, $index, 1);
            $this->rows = array_values($this->rows);
        }
    }

    public function save()
    {
        $user = auth()->user();
        $this->validate();

        DB::beginTransaction();
        try {
            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'meal_type' => $this->meal_type,
                'portion_size' => $this->portion_size,
                'calories' => $this->calories ?: 0,
                'protein' => $this->protein ?: 0,
                'carbs' => $this->carbs ?: 0,
                'fat' => $this->fat ?: 0,
                'fiber' => $this->fiber ?: 0,
                'created_by' => auth()->id() ?? 1,
                'dapur_id' => $user->dapur_id ?: ($this->dapur_id ?: null),
            ];

            if ($this->isEdit) {
                // Double check akses sebelum update
                if ($user->dapur_id && $this->menuItem->dapur_id && $this->menuItem->dapur_id !== $user->dapur_id) {
                    throw new \Exception('Anda tidak memiliki akses untuk mengubah menu ini.');
                }
                $this->menuItem->update($data);
                $this->menuItem->boms()->delete();
            } else {
                $this->menuItem = MenuItem::create($data);
            }

            foreach ($this->rows as $row) {
                if (empty($row['material_id'])) {
                    continue;
                }

                $material = Material::find($row['material_id']);
                if (! $material) {
                    continue;
                }

                $this->menuItem->boms()->create([
                    'material_id' => $row['material_id'],
                    'quantity_per_portion' => (float) $row['quantity'] / (int) $this->portion_size,
                    'unit' => $material->unit,
                ]);
            }

            DB::commit();

            return redirect()->route('menu-items.index')->with('success', 'Menu masakan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('MenuItem Save Error: '.$e->getMessage());

            // Tampilkan error ke session agar muncul di Blade
            session()->flash('error', 'Gagal menyimpan: '.$e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $user = auth()->user();
        $dapurs = $user->dapur_id
            ? Dapur::where('id', $user->dapur_id)->get()
            : Dapur::orderBy('name')->get();

        return view('livewire.menu-item-form', [
            'dapurs' => $dapurs,
        ]);
    }
}
