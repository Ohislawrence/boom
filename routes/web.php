<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Front Pages
|--------------------------------------------------------------------------
*/
require __DIR__.'/frontend.php';

/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/
require __DIR__.'/admin.php';

/*
|--------------------------------------------------------------------------
| Tipster Portal
|--------------------------------------------------------------------------
*/
require __DIR__.'/tipster.php';

/*
|--------------------------------------------------------------------------
| Bettor Portal
|--------------------------------------------------------------------------
*/
require __DIR__.'/bettor.php';

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin'))   return redirect()->route('admin.dashboard');
    if ($user->hasRole('tipster')) return redirect()->route('tipster.dashboard');
    return redirect()->route('bettor.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
