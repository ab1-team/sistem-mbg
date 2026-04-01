<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;

class FinancialPeriodController extends Controller
{
    public function index()
    {
        return view('finance.periods.index');
    }
}
