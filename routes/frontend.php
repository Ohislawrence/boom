<?php

use App\Http\Controllers\Frontend\AccumulatorController;
use App\Http\Controllers\Frontend\BookmakerController;
use App\Http\Controllers\Frontend\ClickTrackingController;
use App\Http\Controllers\Frontend\FixtureController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LeagueController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PollController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\TipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
| All public-facing front page routes. Accessible by guests and auth users.
|
*/

Route::middleware('geo')->group(function () {
    Route::get('/',                                          [HomeController::class,       'index'])->name('home');
    Route::get('/search',                                    [SearchController::class,     'index'])->name('search');
    Route::get('/sitemap.xml',                               [SitemapController::class,    'index'])->name('sitemap');
    Route::post('/track/click',                              [ClickTrackingController::class, 'track'])->name('track.click');
    Route::get('/fixture/betting-tips',                      [FixtureController::class, 'index'])->name('fixture.betting-tips.index');
    Route::get('/fixture/betting-tips/{fixture}',            [FixtureController::class, 'bettingTips'])->name('fixture.betting-tips');
    Route::post('/fixture/{fixture}/poll',                   [PollController::class, 'vote'])->name('fixture.poll.vote');
    Route::get('/fixture/{fixture}/poll/results',            [PollController::class, 'results'])->name('fixture.poll.results');
    Route::get('/accumulator',                               [AccumulatorController::class, 'index'])->name('accumulator.index');
    Route::get('/tips',                                      [TipController::class,     'index'])->name('tips.index');
    Route::get('/tips/{tip}',                                [TipController::class,     'show'])->name('tips.show');
    Route::get('/league/{league}',     [LeagueController::class,  'show'])->name('league.show');
    Route::get('/bookmakers',                       [BookmakerController::class, 'index'])->name('bookmakers.index');
    Route::get('/bookmakers/{bookmaker:slug}',      [BookmakerController::class, 'show'])->name('bookmakers.show');

    // Platform / static pages
    Route::get('/about',                [PageController::class, 'about'])->name('page.about');
    Route::get('/how-it-works',         [PageController::class, 'howItWorks'])->name('page.how-it-works');
    Route::get('/editorial-policy',     [PageController::class, 'editorialPolicy'])->name('page.editorial-policy');
    Route::get('/privacy-notice',       [PageController::class, 'privacyNotice'])->name('page.privacy-notice');
    Route::get('/responsible-gambling', [PageController::class, 'responsibleGambling'])->name('page.responsible-gambling');
    Route::get('/virtual-games',        [PageController::class, 'virtualGames'])->name('page.virtual-games');
    Route::get('/virtual-games/{slug}',  [PageController::class, 'virtualGame'])->name('page.virtual-game');
});
