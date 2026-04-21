<?php

namespace App\Console\Commands;

use App\Jobs\AnalyseFixtureJob;
use App\Models\DailyRunLog;
use App\Models\Fixture;
use App\Models\Tip;
use App\Services\MatchAnalysisService;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class RunDailyAnalysis extends Command
{
    protected $signature = 'scout:run-daily-analysis
                            {--date=         : Explicit date in Y-m-d format}
                            {--days-ahead=   : Days ahead from today (1=tomorrow, 2=day after, etc.)}
                            {--fetch-only    : Only fetch and store fixtures — skip AI analysis}
                            {--analyse-only  : Only run AI on fixtures already in DB — skip API fetch}
                            {--force         : Re-analyse fixtures that were already analysed}';

    protected $description = 'Fetch fixtures from API-Football and/or run DeepSeek AI analysis (parallel batch).';

    public function __construct(private readonly MatchAnalysisService $analysis)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $daysAhead   = (int) ($this->option('days-ahead') ?: 1);
        $date        = $this->option('date') ?: now()->addDays($daysAhead)->toDateString();
        $fetchOnly   = (bool) $this->option('fetch-only');
        $analyseOnly = (bool) $this->option('analyse-only');
        $force       = (bool) $this->option('force');

        $mode = $fetchOnly ? 'FETCH ONLY' : ($analyseOnly ? 'ANALYSE ONLY' : 'FULL RUN');

        $this->info("═══════════════════════════════════════");
        $this->info("  SCOUT Daily Analysis — {$date} [{$mode}]");
        $this->info("═══════════════════════════════════════");

        // ── Phase 1: Fetch fixtures from Football API ──────────────────────────
        if (! $analyseOnly) {
            $this->info('  → Phase 1: fetching fixtures from Football API…');

            try {
                $fetchResult = $this->analysis->runDailyBatch($date, fetchOnly: true);
                $this->info("  ✓ Fixtures stored : {$fetchResult['fixtures']}");
            } catch (Throwable $e) {
                $this->error("  ✗ Fetch failed: " . $e->getMessage());
                return self::FAILURE;
            }

            if ($fetchOnly) {
                $this->info('  Done (fetch-only mode).');
                return self::SUCCESS;
            }
        }

        // ── Guard: prevent duplicate full runs unless --force or log is stale ──────
        $existingLog = DailyRunLog::where('run_date', $date)
            ->whereIn('status', ['running', 'completed'])
            ->first();

        if ($existingLog && ! $force) {
            if ($existingLog->isStale()) {
                $this->warn("Stale 'running' log found for {$date} (started {$existingLog->started_at}). Treating as failed and re-running.");
                $existingLog->markFailed('Stale — worker likely died. Re-run triggered automatically.');
            } else {
                $this->warn("Analysis for {$date} already ran (status: {$existingLog->status}). Use --force to re-run.");
                return self::SUCCESS;
            }
        }

        // ── Phase 2: Dispatch parallel AnalyseFixtureJob batch ────────────────
        $this->info('  → Phase 2: dispatching parallel analysis batch…');

        $fixtureIds = Fixture::whereDate('match_date', $date)
            ->when(! $force, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('analysis_run_at')
                          ->orWhereDoesntHave('tips', fn ($t) => $t->where('is_ai_generated', true));
                });
            })
            ->pluck('id')
            ->all();

        if (empty($fixtureIds)) {
            $this->warn('  No fixtures need analysis for this date.');
            return self::SUCCESS;
        }

        $log = DailyRunLog::updateOrCreate(
            ['run_date' => $date],
            [
                'status'        => 'running',
                'started_at'    => now(),
                'completed_at'  => null,
                'error_message' => null,
            ]
        );

        $logId        = $log->id;
        $fixtureCount = count($fixtureIds);

        $jobs = array_map(fn ($id) => new AnalyseFixtureJob($id), $fixtureIds);

        Bus::batch($jobs)
            ->name("analyse:{$date}")
            ->allowFailures()
            ->then(function (Batch $batch) use ($date, $logId, $fixtureCount) {
                $tipCount = Tip::whereHas('fixture', fn ($q) => $q->whereDate('match_date', $date))
                    ->where('is_ai_generated', true)
                    ->count();

                DailyRunLog::find($logId)?->markCompleted($fixtureCount, $tipCount);

                \Illuminate\Support\Facades\Log::info('RunDailyAnalysis: batch completed', [
                    'date'        => $date,
                    'total_jobs'  => $batch->totalJobs,
                    'failed_jobs' => $batch->failedJobs,
                    'tips_saved'  => $tipCount,
                ]);
            })
            ->catch(function (Batch $batch, Throwable $e) use ($logId, $date) {
                DailyRunLog::find($logId)?->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                \Illuminate\Support\Facades\Log::error('RunDailyAnalysis: batch error', [
                    'date'  => $date,
                    'error' => $e->getMessage(),
                ]);
            })
            ->dispatch();

        $this->info("  ✓ Batch dispatched — {$fixtureCount} fixture(s) queued for analysis.");
        $this->info("  ✓ Daily log ID : {$logId}");
        $this->info('  Workers will process jobs in parallel. Monitor: php artisan queue:monitor');

        return self::SUCCESS;
    }
}

