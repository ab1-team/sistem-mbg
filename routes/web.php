<?php

use App\Http\Controllers\Admin\YayasanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $host = request()->getHost();
    $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);

    if (! in_array($host, $centralDomains)) {
        return redirect('/login');
    }

    return view('landing');
})->name('landing');

// Admin Landlord Portal - Only for Central
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth.central'])->group(function () {
    Route::get('/yayasans', [YayasanController::class, 'index'])->name('yayasans.index');
    Route::post('/yayasans', [YayasanController::class, 'store'])->name('yayasans.store');
    Route::delete('/yayasans/{tenant}', [YayasanController::class, 'destroy'])->name('yayasans.destroy');
    Route::patch('/yayasans/{tenant}/toggle', [YayasanController::class, 'toggleStatus'])->name('yayasans.toggle');
});
