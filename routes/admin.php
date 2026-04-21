<?php

use App\Http\Controllers\Admin\BetMarketController;
use App\Http\Controllers\Admin\BookmakerController;
use App\Http\Controllers\Admin\ClickAnalyticsController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FixtureController;
use App\Http\Controllers\Admin\LeagueController;
use App\Http\Controllers\Admin\RunController;
use App\Http\Controllers\Admin\TipController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes  —  protected by auth + admin role
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Bookmakers CRUD
        Route::resource('bookmakers', BookmakerController::class);

        // Leagues management
        Route::resource('leagues', LeagueController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('leagues/{league}/toggle', [LeagueController::class, 'toggle'])->name('leagues.toggle');

        // Tips moderation + editing
        Route::resource('tips', TipController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::patch('tips/{tip}/publish',    [TipController::class, 'publish'])->name('tips.publish');
        Route::patch('tips/{tip}/reject',     [TipController::class, 'reject'])->name('tips.reject');
        Route::patch('tips/{tip}/set-result', [TipController::class, 'setResult'])->name('tips.set-result');

        // AI Run Control
        Route::get('run-control',              [RunController::class, 'index'])->name('run-control.index');
        Route::post('run-control/analysis',     [RunController::class, 'triggerAnalysis'])->name('run-control.analysis');
        Route::post('run-control/fetch',        [RunController::class, 'triggerFetch'])->name('run-control.fetch');
        Route::post('run-control/analyse-only', [RunController::class, 'triggerAnalyseOnly'])->name('run-control.analyse-only');
        Route::post('run-control/resolve',      [RunController::class, 'triggerResolve'])->name('run-control.resolve');

        // User management
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');

        // Countries
        Route::resource('countries', CountryController::class)->except(['show']);
        Route::patch('countries/{country}/toggle', [CountryController::class, 'toggle'])->name('countries.toggle');

        // Bet Markets
        Route::resource('bet-markets', BetMarketController::class)->except(['show']);
        Route::patch('bet-markets/{betMarket}/toggle', [BetMarketController::class, 'toggle'])->name('bet-markets.toggle');

        // Fixtures (read-only)
        Route::get('fixtures', [FixtureController::class, 'index'])->name('fixtures.index');

        // Click Analytics
        Route::get('click-analytics', [ClickAnalyticsController::class, 'index'])->name('click-analytics.index');

    });
