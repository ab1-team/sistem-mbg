<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('finance.expenses.index');
    }

    public function create()
    {
        return view('finance.expenses.create');
    }

    public function edit(Expense $expense)
    {
        return view('finance.expenses.edit', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('finance.expenses.index')->with('success', 'Data pengeluaran berhasil dihapus.');
    }
}
