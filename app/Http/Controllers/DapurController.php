<?php

namespace App\Http\Controllers;

use App\Models\Dapur;
use Illuminate\Http\Request;

class DapurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dapurs = Dapur::latest()->paginate(10);

        return view('dapurs.index', compact('dapurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dapurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:dapurs',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'capacity_portions' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Dapur::create($validated);

        return redirect()->route('dapurs.index')
            ->with('success', 'Dapur berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dapur $dapur)
    {
        return view('dapurs.show', compact('dapur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dapur $dapur)
    {
        return view('dapurs.edit', compact('dapur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dapur $dapur)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:dapurs,code,'.$dapur->id,
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'capacity_portions' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $dapur->update($validated);

        return redirect()->route('dapurs.index')
            ->with('success', 'Dapur berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dapur $dapur)
    {
        $dapur->delete();

        return redirect()->route('dapurs.index')
            ->with('success', 'Dapur berhasil dihapus.');
    }
}
