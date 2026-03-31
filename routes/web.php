<?php

use App\Http\Controllers\DapurController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('dapurs', DapurController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('investors', InvestorController::class);
    Route::resource('periods', PeriodController::class);
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
