<?php

namespace App\Jobs;

use App\Models\DailyRunLog;
use App\Models\Fixture;
use App\Models\Tip;
use App\Services\MatchAnalysisService;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout covers Phase 1 (Football API fetch for all fixtures) only.
     * Phase 2 analysis runs as individual AnalyseFixtureJobs — each with their own timeout.
     * 60 minutes is generous even for 200+ fixture days.
     */
    public int $timeout = 3600;

    /** Never retry the orchestrator — if fetch fails, alert and fix. */
    public int $tries = 1;

    public function __construct(
        public readonly string $date,
        public readonly bool   $fetchOnly   = false,
        public readonly bool   $analyseOnly = false,
        public readonly bool   $force       = false,
    ) {}

    public function handle(MatchAnalysisService $service): void
    {
        $mode = $this->fetchOnly ? 'fetch-only' : ($this->analyseOnly ? 'analyse-only' : 'full');
        Log::info("RunAnalysisJob: started [{$mode}]", ['date' => $this->date]);

        // ── Phase 1: fetch fixtures from Football API ──────────────────────────
        // Always runs unless --analyse-only. Stores/updates fixture rows in DB.
        // No AI calls happen here.
        if (! $this->analyseOnly) {
            $fetchResult = $service->runDailyBatch($this->date, fetchOnly: true);

            Log::info('RunAnalysisJob: fixtures fetched', [
                'date'     => $this->date,
                'fixtures' => $fetchResult['fixtures'],
            ]);

            if ($this->fetchOnly) {
                return; // caller only wanted the fetch phase
            }
        }

        // ── Phase 2: dispatch parallel analysis batch ──────────────────────────
        // One AnalyseFixtureJob per fixture. Workers process them concurrently.
        $fixtureIds = Fixture::whereDate('match_date', $this->date)
            ->when(! $this->force, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('analysis_run_at')
                          ->orWhereDoesntHave('tips', fn ($t) => $t->where('is_ai_generated', true));
                });
            })
            ->pluck('id')
            ->all();

        if (empty($fixtureIds)) {
            Log::info('RunAnalysisJob: no fixtures need analysis', ['date' => $this->date]);
            return;
        }

        $log = DailyRunLog::updateOrCreate(
            ['run_date' => $this->date],
            [
                'status'        => 'running',
                'started_at'    => now(),
                'completed_at'  => null,
                'error_message' => null,
            ]
        );

        // If a previous run left the log stuck in 'running' beyond the stale threshold,
        // reset it so the batch callbacks can write the correct final status.
        if ($log->wasRecentlyCreated === false && $log->isStale()) {
            $log->update(['status' => 'running', 'started_at' => now(), 'completed_at' => null, 'error_message' => null]);
        }

        $date        = $this->date;
        $logId       = $log->id;
        $fixtureCount = count($fixtureIds);

        $jobs = array_map(fn ($id) => new AnalyseFixtureJob($id), $fixtureIds);

        Bus::batch($jobs)
            ->name("analyse:{$this->date}")
            ->allowFailures() // partial failures don't cancel the whole batch
            ->then(function (Batch $batch) use ($date, $logId, $fixtureCount) {
                // Count AI tips generated for the day once batch completes
                $tipCount = Tip::whereHas('fixture', fn ($q) => $q->whereDate('match_date', $date))
                    ->where('is_ai_generated', true)
                    ->count();

                DailyRunLog::find($logId)?->markCompleted($fixtureCount, $tipCount);

                Log::info('RunAnalysisJob: analysis batch completed', [
                    'date'          => $date,
                    'total_jobs'    => $batch->totalJobs,
                    'failed_jobs'   => $batch->failedJobs,
                    'tips_saved'    => $tipCount,
                ]);
            })
            ->catch(function (Batch $batch, Throwable $e) use ($logId, $date) {
                DailyRunLog::find($logId)?->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                Log::error('RunAnalysisJob: analysis batch error', [
                    'date'  => $date,
                    'error' => $e->getMessage(),
                ]);
            })
            ->dispatch();

        Log::info('RunAnalysisJob: analysis batch dispatched', [
            'date'     => $this->date,
            'fixtures' => $fixtureCount,
        ]);
    }
}

