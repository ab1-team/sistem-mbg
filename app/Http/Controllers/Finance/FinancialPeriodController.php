<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinancialPeriodController extends Controller
{
    public function index()
    {
        return view('finance.periods.index');
    }
}
