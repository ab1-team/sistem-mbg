<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\User;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $investors = Investor::with('user')->latest()->paginate(10);

        return view('investors.index', compact('investors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::role('Investor')->whereDoesntHave('investor')->get();

        return view('investors.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:investors',
            'code' => 'required|string|max:20|unique:investors',
            'name' => 'required|string|max:150',
            'identity_number' => 'nullable|string|max:30',
            'investment_amount' => 'required|numeric|min:0',
            'join_date' => 'required|date',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'bank_holder' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        // Create the investor with 0 share_percentage initially
        $validated['share_percentage'] = 0;
        Investor::create($validated);

        $this->recalculateSharePercentages();

        return redirect()->route('investors.index')
            ->with('success', 'Investor berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Investor $investor)
    {
        return view('investors.show', compact('investor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investor $investor)
    {
        $users = User::role('Investor')->get(); // All investors for selection

        return view('investors.edit', compact('investor', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:investors,user_id,'.$investor->id,
            'code' => 'required|string|max:20|unique:investors,code,'.$investor->id,
            'name' => 'required|string|max:150',
            'identity_number' => 'nullable|string|max:30',
            'investment_amount' => 'required|numeric|min:0',
            'join_date' => 'required|date',
            'exit_date' => 'nullable|date|after_or_equal:join_date',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'bank_holder' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $investor->update($validated);

        $this->recalculateSharePercentages();

        return redirect()->route('investors.index')
            ->with('success', 'Investor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investor $investor)
    {
        $investor->delete();

        $this->recalculateSharePercentages();

        return redirect()->route('investors.index')
            ->with('success', 'Investor berhasil dihapus.');
    }

    /**
     * Recalculate share percentage for all active investors based on their investment amount.
     */
    protected function recalculateSharePercentages()
    {
        $totalInvestment = Investor::where('is_active', true)->sum('investment_amount');

        $investors = Investor::all();
        foreach ($investors as $inv) {
            if ($inv->is_active && $totalInvestment > 0) {
                $percentage = ($inv->investment_amount / $totalInvestment) * 100;
            } else {
                $percentage = 0;
            }

            // Only update if changed to avoid unnecessary queries
            if (round($inv->share_percentage, 4) !== round($percentage, 4)) {
                $inv->updateQuietly(['share_percentage' => $percentage]);
            }
        }
    }
}
