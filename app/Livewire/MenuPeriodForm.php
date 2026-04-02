<?php

namespace App\Livewire;

use App\Models\Dapur;
use App\Models\MenuItem;
use App\Models\MenuPeriod;
use App\Models\MenuSchedule;
use App\Models\Period;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MenuPeriodForm extends Component
{
    public $menuPeriodId;

    protected $queryString = [
        'dapur_id',
        'period_id',
        'title',
    ];

    public $dapur_id;

    public $period_id;

    public $title;

    public $schedules = []; // Array of days, each with an array of meal slots

    public function mount($menuPeriodId = null)
    {
        $user = auth()->user();

        if ($menuPeriodId) {
            $this->menuPeriodId = $menuPeriodId;
            $mp = MenuPeriod::with('schedules.items')->findOrFail($menuPeriodId);

            // Cek akses edit jika user terikat dapur tertentu
            if ($user->dapur_id && $mp->dapur_id && $mp->dapur_id !== $user->dapur_id) {
                return redirect()->route('menu-periods.index')->with('error', 'Anda tidak memiliki akses ke rencana menu ini.');
            }

            $this->dapur_id = $mp->dapur_id;
            $this->period_id = $mp->period_id;
            $this->title = $mp->title;

            // Map existing schedules to our local array
            $this->loadExistingSchedules($mp->schedules);
        } else {
            // Default dapur_id dari user jika ada
            $this->dapur_id = $user->dapur_id;
            
            if ($this->period_id) {
                // Support generation from query string
                $period = Period::find($this->period_id);
                if ($period) {
                    $this->generateScheduleSlots($period);
                }
            }
        }
    }

    public function updatedPeriodId($value)
    {
        if ($value) {
            $period = Period::findOrFail($value);
            $this->generateScheduleSlots($period);
        } else {
            $this->schedules = [];
        }
    }

    protected function generateScheduleSlots(Period $period)
    {
        $this->schedules = [];
        $dateRange = CarbonPeriod::create($period->start_date, $period->end_date);

        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            $this->schedules[$dateStr] = [
                'date' => $dateStr,
                'display' => $date->translatedFormat('D, d M'),
                'meals' => [
                    ['type' => 'sarapan', 'menu_item_ids' => [], 'portions' => 0],
                    ['type' => 'makan_siang', 'menu_item_ids' => [], 'portions' => 0],
                    ['type' => 'makan_malam', 'menu_item_ids' => [], 'portions' => 0],
                    ['type' => 'snack', 'menu_item_ids' => [], 'portions' => 0],
                ],
            ];
        }
    }

    protected function loadExistingSchedules($dbSchedules)
    {
        // First generate based on period to get full dates
        $period = Period::findOrFail($this->period_id);
        $this->generateScheduleSlots($period);

        foreach ($dbSchedules as $s) {
            $dateStr = $s->serve_date->format('Y-m-d');
            if (isset($this->schedules[$dateStr])) {
                foreach ($this->schedules[$dateStr]['meals'] as $idx => $m) {
                    if ($m['type'] === $s->meal_type) {
                        $this->schedules[$dateStr]['meals'][$idx] = [
                            'type' => $s->meal_type,
                            'menu_item_ids' => $s->items->pluck('id')->toArray(),
                            'portions' => $s->target_portions,
                        ];
                    }
                }
            }
        }
    }

    public function save()
    {
        $user = auth()->user();
        $this->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'period_id' => 'required|exists:periods,id',
            'title' => 'required|string|max:150',
            'schedules.*.meals.*.menu_item_ids' => 'nullable|array',
            'schedules.*.meals.*.menu_item_ids.*' => 'exists:menu_items,id',
            'schedules.*.meals.*.portions' => 'required|integer|min:0',
        ]);

        // Force dapur_id jika user terikat dapur
        if ($user->dapur_id) {
            $this->dapur_id = $user->dapur_id;
        }

        // Ensure at least one menu is selected
        $hasAnyMenu = false;
        foreach ($this->schedules as $day) {
            foreach ($day['meals'] as $meal) {
                if (! empty($meal['menu_item_ids'])) {
                    $hasAnyMenu = true;
                    break 2;
                }
            }
        }

        if (! $hasAnyMenu) {
            $this->addError('schedules', 'Anda harus memilih minimal satu menu untuk disimpan.');

            return;
        }

        DB::beginTransaction();
        try {
            $mp = MenuPeriod::updateOrCreate(
                ['id' => $this->menuPeriodId],
                [
                    'dapur_id' => $this->dapur_id,
                    'period_id' => $this->period_id,
                    'title' => $this->title,
                    'created_by' => auth()->id(),
                    'status' => 'draf',
                ]
            );

            // Clear old schedules for this period if updating
            MenuSchedule::where('menu_period_id', $mp->id)->delete();

            foreach ($this->schedules as $day) {
                foreach ($day['meals'] as $meal) {
                    if (! empty($meal['menu_item_ids'])) {
                        $s = MenuSchedule::create([
                            'menu_period_id' => $mp->id,
                            'menu_item_id' => $meal['menu_item_ids'][0] ?? null,
                            'serve_date' => $day['date'],
                            'meal_type' => $meal['type'],
                            'target_portions' => $meal['portions'],
                        ]);
                        $s->items()->sync($meal['menu_item_ids']);
                    }
                }
            }

            DB::commit();
            session()->flash('success', 'Rencana Menu berhasil disimpan sebagai draf.');

            return redirect()->route('menu-periods.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();
        $dapurs = $user->dapur_id 
            ? Dapur::where('id', $user->dapur_id)->get() 
            : Dapur::orderBy('name')->get();

        return view('livewire.menu-period-form', [
            'dapurs' => $dapurs,
            'periods' => Period::where('status', 'open')->orderBy('start_date', 'desc')->get(),
            'menuItems' => MenuItem::query()
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('dapur_id')
                        ->when($this->dapur_id, function ($query) {
                            $query->orWhere('dapur_id', $this->dapur_id);
                        });
                })
                ->orderBy('name')
                ->get(),
            'mealTypes' => ['sarapan', 'makan_siang', 'makan_malam', 'snack'],
        ]);
    }
}
