<?php

use App\Console\Commands\RunDailyAnalysis;
use App\Console\Commands\ResolveTipResults;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| SCOUT Scheduled Tasks
|--------------------------------------------------------------------------
| The daily analysis runs at 06:00 server time every day.
| It fetches today's fixtures for all active leagues, sends them through
| DeepSeek AI, and saves high-confidence tips to the database.
|
| To run manually:
|   php artisan scout:run-daily-analysis
|   php artisan scout:run-daily-analysis --date=2026-04-20
|   php artisan scout:run-daily-analysis --force
|
| Ensure the OS-level scheduler is running:
|   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// 22:00 — tomorrow's fixtures (D+1), published ~14h before kick-off
Schedule::command(RunDailyAnalysis::class, ['--days-ahead' => 1])
    ->dailyAt('22:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/daily-analysis.log'));

// 08:00 — day-after-tomorrow's fixtures (D+2), published ~38h before kick-off
Schedule::command(RunDailyAnalysis::class, ['--days-ahead' => 2])
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/daily-analysis.log'));

// Resolve tip outcomes against final scores — runs twice daily
// Looks back 2 days so late kick-offs and extra-time games are caught
Schedule::command(ResolveTipResults::class, ['--days=2'])
    ->twiceDaily(14, 23)
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/tip-results.log'));

