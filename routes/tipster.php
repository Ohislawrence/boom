<?php

use App\Http\Controllers\Tipster\DashboardController;
use App\Http\Controllers\Tipster\TipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tipster Portal Routes
|--------------------------------------------------------------------------
| Accessible to users with the 'tipster' or 'admin' role.
|
*/

Route::prefix('tipster')->name('tipster.')->middleware(['auth', 'verified', 'tipster'])->group(function () {

    Route::get('/',              [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/tips',          [TipController::class, 'index'])->name('tips.index');
    Route::get('/tips/create',   [TipController::class, 'create'])->name('tips.create');
    Route::post('/tips',         [TipController::class, 'store'])->name('tips.store');
    Route::get('/tips/{tip}',    [TipController::class, 'show'])->name('tips.show');
    Route::delete('/tips/{tip}', [TipController::class, 'destroy'])->name('tips.destroy');
});
