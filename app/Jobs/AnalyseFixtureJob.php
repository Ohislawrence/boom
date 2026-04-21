<?php

namespace App\Jobs;

use App\Models\Fixture;
use App\Services\MatchAnalysisService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class AnalyseFixtureJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Per-fixture timeout: Football API calls + DeepSeek inference.
     * 90s DeepSeek timeout + API calls + buffer = 3 minutes.
     */
    public int $timeout = 180;

    /**
     * Retry up to 2 times with back-off to handle transient API errors and 429s.
     */
    public int $tries = 2;

    public function __construct(public readonly int $fixtureId) {}

    /**
     * Back-off schedule: wait 30s then 90s between retries.
     * Handles DeepSeek / Football API 429 rate limits gracefully.
     *
     * @return array<int>
     */
    public function backoff(): array
    {
        return [30, 90];
    }

    public function handle(MatchAnalysisService $service): void
    {
        // Respect batch cancellation
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Spread concurrent starts — avoids thundering-herd against Football API
        // when a large batch begins simultaneously (0–3 s random jitter).
        usleep(random_int(0, 3_000_000));

        $fixture = Fixture::findOrFail($this->fixtureId);

        $result = $service->analyseFixture($fixture);

        Log::info('AnalyseFixtureJob: done', [
            'fixture_id' => $this->fixtureId,
            'tips_saved' => $result['tips_saved'],
        ]);
    }

    public function failed(Throwable $e): void
    {
        Log::error('AnalyseFixtureJob: failed after all retries', [
            'fixture_id' => $this->fixtureId,
            'error'      => $e->getMessage(),
        ]);
    }
}
