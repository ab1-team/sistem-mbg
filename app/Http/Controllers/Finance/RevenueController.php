<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Revenue;

class RevenueController extends Controller
{
    public function index()
    {
        return view('finance.revenues.index');
    }

    public function create()
    {
        return view('finance.revenues.create');
    }

    public function edit(Revenue $revenue)
    {
        return view('finance.revenues.edit', compact('revenue'));
    }

    public function destroy(Revenue $revenue)
    {
        $revenue->delete();

        return redirect()->route('finance.revenues.index')->with('success', 'Data pendapatan berhasil dihapus.');
    }
}
