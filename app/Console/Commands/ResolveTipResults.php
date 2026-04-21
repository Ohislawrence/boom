<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use App\Services\TipResultService;
use Illuminate\Console\Command;
use Throwable;

class ResolveTipResults extends Command
{
    protected $signature = 'scout:resolve-results
                            {--days=3       : How many days back to look for finished fixtures}
                            {--fixture=     : Resolve tips for a specific internal fixture ID only}
                            {--no-refresh   : Skip fetching latest scores from API-Football}
                            {--dry-run      : Show what would be resolved without saving anything}';

    protected $description = 'Resolve published tip outcomes against final match scores.';

    public function __construct(private readonly TipResultService $resolver)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry-run');
        $refresh   = ! (bool) $this->option('no-refresh');
        $fixtureId = $this->option('fixture');

        $this->info('══════════════════════════════════════');
        $this->info('  SCOUT Tip Result Resolver');
        $this->info('══════════════════════════════════════');

        if ($dryRun) {
            $this->warn('  DRY RUN — no records will be saved.');
        }

        // ── Single fixture mode ──────────────────────────────────────
        if ($fixtureId) {
            return $this->resolveFixture((int) $fixtureId, $refresh, $dryRun);
        }

        // ── Batch mode ───────────────────────────────────────────────
        $days = (int) $this->option('days');
        $this->line("  Scanning last {$days} days for unresolved tips...");

        if ($dryRun) {
            return $this->dryRunBatch($days, $refresh);
        }

        try {
            $stats = $this->resolver->resolveRecentResults($days, $refresh);

            $this->info("  ✓ Resolved : {$stats['resolved']}");
            $this->info("  ↩ Voided   : {$stats['voided']}");
            $this->line("  — Skipped  : {$stats['skipped']} (fixture not finished yet)");

            if ($stats['errors'] > 0) {
                $this->warn("  ✗ Errors   : {$stats['errors']} (check laravel.log)");
            }

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error('  Fatal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    // ──────────────────────────────────────────────────────────────────

    private function resolveFixture(int $id, bool $refresh, bool $dryRun): int
    {
        $fixture = Fixture::find($id);

        if (! $fixture) {
            $this->error("  Fixture #{$id} not found.");
            return self::FAILURE;
        }

        $this->line("  Fixture: {$fixture->home_team} vs {$fixture->away_team} ({$fixture->match_date->format('d M Y')})");

        if ($refresh && $fixture->api_football_id) {
            $this->line('  Refreshing score from API-Football...');
            $fixture = $this->resolver->refreshFixtureScore($fixture);
        }

        $this->line("  Status: {$fixture->status} | Score: {$fixture->score_home}-{$fixture->score_away}");

        $tips = \App\Models\Tip::where('fixture_id', $fixture->id)
                               ->where('status', 'published')
                               ->whereDoesntHave('tipResult')
                               ->with('betMarket')
                               ->get();

        if ($tips->isEmpty()) {
            $this->line('  No unresolved published tips for this fixture.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($tips as $tip) {
            if ($dryRun) {
                $rows[] = [
                    $tip->id,
                    $tip->market,
                    $tip->selection,
                    $tip->odds ?? '—',
                    '(dry run)',
                ];
                continue;
            }

            try {
                $result = $this->resolver->resolveTip($tip, $fixture);
                $rows[] = [
                    $tip->id,
                    $tip->market,
                    $tip->selection,
                    $tip->odds ?? '—',
                    strtoupper($result->result) . ($result->profit_loss !== null ? '  P&L: ' . ($result->profit_loss >= 0 ? '+' : '') . $result->profit_loss : ''),
                ];
            } catch (Throwable $e) {
                $rows[] = [$tip->id, $tip->market, $tip->selection, $tip->odds ?? '—', 'ERROR: ' . $e->getMessage()];
            }
        }

        $this->table(['Tip ID', 'Market', 'Selection', 'Odds', 'Result'], $rows);

        return self::SUCCESS;
    }

    private function dryRunBatch(int $days, bool $refresh): int
    {
        $fixtures = Fixture::whereDate('match_date', '<=', now())
            ->whereDate('match_date', '>=', now()->subDays($days))
            ->whereHas('tips', fn ($q) =>
                $q->where('status', 'published')->whereDoesntHave('tipResult')
            )
            ->with(['tips' => fn ($q) => $q->where('status', 'published')->whereDoesntHave('tipResult')])
            ->get();

        if ($fixtures->isEmpty()) {
            $this->line('  No fixtures with unresolved tips found in this period.');
            return self::SUCCESS;
        }

        foreach ($fixtures as $fixture) {
            if ($refresh && $fixture->api_football_id) {
                $fixture = $this->resolver->refreshFixtureScore($fixture);
            }

            $statusLabel = in_array($fixture->status, ['FT', 'AET', 'PEN'])
                ? "<fg=green>{$fixture->status}</>"
                : "<fg=yellow>{$fixture->status}</>";

            $this->line("  [{$statusLabel}] {$fixture->home_team} vs {$fixture->away_team} — {$fixture->score_home}-{$fixture->score_away} — {$fixture->tips->count()} tip(s)");
        }

        $total = $fixtures->sum(fn ($f) => $f->tips->count());
        $this->info("  Total tips that would be resolved: {$total}");

        return self::SUCCESS;
    }
}
