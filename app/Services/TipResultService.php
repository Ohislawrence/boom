<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Tip;
use App\Models\TipResult;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * TipResultService
 *
 * Resolves published tips against final match scores.
 *
 * Supported markets (case-insensitive keyword matching):
 *   • Match Result / 1X2 / Full Time Result
 *   • Both Teams to Score / BTTS
 *   • Over / Under X.5 Goals
 *   • Double Chance (1X, X2, 12)
 *   • Draw No Bet (DNB)
 *   • Correct Score
 *   • Anything else → void with explanatory note
 */
class TipResultService
{
    // Finished statuses from API-Football
    private const FINISHED_STATUSES = ['FT', 'AET', 'PEN'];

    public function __construct(private readonly FootballApiService $api) {}

    // ══════════════════════════════════════════════════════════════════
    //  Public API
    // ══════════════════════════════════════════════════════════════════

    /**
     * Main entry point: scan for unresolved tips on finished fixtures.
     *
     * @param  int  $daysBack    How many days back to scan for finished fixtures
     * @param  bool $refreshApi  Whether to call API-Football to update fixture scores
     * @return array{resolved:int, voided:int, skipped:int, errors:int}
     */
    public function resolveRecentResults(int $daysBack = 3, bool $refreshApi = true): array
    {
        $stats = ['resolved' => 0, 'voided' => 0, 'skipped' => 0, 'errors' => 0];

        // Find fixtures that:
        //  - had their match in the last $daysBack days
        //  - have at least one published tip without a result
        $fixtures = Fixture::whereDate('match_date', '<=', now())
            ->whereDate('match_date', '>=', now()->subDays($daysBack))
            ->whereHas('tips', fn ($q) =>
                $q->where('status', 'published')
                  ->whereDoesntHave('tipResult')
            )
            ->get();

        foreach ($fixtures as $fixture) {
            try {
                // Optionally refresh scores from API
                if ($refreshApi && $fixture->api_football_id) {
                    $fixture = $this->refreshFixtureScore($fixture);
                }

                // Only resolve if the match is finished and has a score
                if (! $this->isResolvable($fixture)) {
                    $stats['skipped']++;
                    continue;
                }

                $resolved = $this->resolveFixtureTips($fixture);
                $stats['resolved'] += $resolved;

            } catch (Throwable $e) {
                $stats['errors']++;
                Log::error("TipResultService: failed for fixture #{$fixture->id} — {$e->getMessage()}");
            }
        }

        return $stats;
    }

    /**
     * Resolve all unresolved published tips for a specific (already-finished) fixture.
     * Returns the number of tips resolved.
     */
    public function resolveFixtureTips(Fixture $fixture): int
    {
        if (! $this->isResolvable($fixture)) {
            return 0;
        }

        $tips = Tip::where('fixture_id', $fixture->id)
                   ->where('status', 'published')
                   ->whereDoesntHave('tipResult')
                   ->get();

        $count = 0;
        foreach ($tips as $tip) {
            $this->resolveTip($tip, $fixture);
            $count++;
        }

        return $count;
    }

    /**
     * Resolve a single tip against a finished fixture.
     * Creates a TipResult record and returns it.
     */
    public function resolveTip(Tip $tip, Fixture $fixture): TipResult
    {
        $scoreHome = (int) $fixture->score_home;
        $scoreAway = (int) $fixture->score_away;

        [$outcome, $notes] = $this->determineOutcome(
            $tip->market,
            $tip->selection,
            $scoreHome,
            $scoreAway,
            $fixture
        );

        $profitLoss = $this->calculateProfitLoss($outcome, $tip->odds);

        return TipResult::create([
            'tip_id'       => $tip->id,
            'result'       => $outcome,
            'profit_loss'  => $profitLoss,
            'notes'        => $notes,
            'resolved_at'  => now(),
        ]);
    }

    /**
     * Fetch the latest score from API-Football and update the fixture record.
     */
    public function refreshFixtureScore(Fixture $fixture): Fixture
    {
        if (! $fixture->api_football_id) {
            return $fixture;
        }

        $result = $this->api->getFixtureResult($fixture->api_football_id);

        if ($result === null) {
            return $fixture;
        }

        $fixture->update([
            'status'     => $result['status'],
            'score_home' => $result['score_home'],
            'score_away' => $result['score_away'],
        ]);

        return $fixture->fresh();
    }

    // ══════════════════════════════════════════════════════════════════
    //  Outcome resolution
    // ══════════════════════════════════════════════════════════════════

    /**
     * Determine if a fixture has a final score we can resolve against.
     */
    private function isResolvable(Fixture $fixture): bool
    {
        return in_array($fixture->status, self::FINISHED_STATUSES, true)
            && $fixture->score_home !== null
            && $fixture->score_away !== null;
    }

    /**
     * Core resolution logic. Returns [outcome, notes].
     *
     * @return array{0: 'win'|'loss'|'void', 1: string}
     */
    private function determineOutcome(
        string $market,
        string $selection,
        int $scoreHome,
        int $scoreAway,
        Fixture $fixture
    ): array {
        $m = strtolower(trim($market));
        $s = strtolower(trim($selection));

        $totalGoals  = $scoreHome + $scoreAway;
        $homeWins    = $scoreHome > $scoreAway;
        $awayWins    = $scoreAway > $scoreHome;
        $draw        = $scoreHome === $scoreAway;
        $btts        = $scoreHome > 0 && $scoreAway > 0;
        $scoreStr    = "{$scoreHome}-{$scoreAway}";

        // ── Match Result / 1X2 ──────────────────────────────────────
        if ($this->matchesMarket($m, ['match result', '1x2', 'full time result', 'moneyline', 'winner'])) {
            if ($this->matchesHomeWin($s, $fixture)) {
                return [$homeWins ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            if ($this->matchesDraw($s)) {
                return [$draw ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            if ($this->matchesAwayWin($s, $fixture)) {
                return [$awayWins ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            return ['void', "Could not parse selection '{$selection}' for market '{$market}'."];
        }

        // ── Both Teams to Score (BTTS) ───────────────────────────────
        if ($this->matchesMarket($m, ['both teams to score', 'btts', 'both teams score', 'gg/ng', 'gg', 'ng'])) {
            $yesSelected = $this->matchesYes($s);
            $noSelected  = $this->matchesNo($s);

            if ($yesSelected) {
                return [$btts ? 'win' : 'loss', "FT: {$scoreStr}. BTTS: " . ($btts ? 'Yes' : 'No')];
            }
            if ($noSelected) {
                return [! $btts ? 'win' : 'loss', "FT: {$scoreStr}. BTTS: " . ($btts ? 'Yes' : 'No')];
            }
            return ['void', "Could not parse Yes/No from selection '{$selection}'."];
        }

        // ── Over / Under ─────────────────────────────────────────────
        if ($this->matchesMarket($m, ['over', 'under', 'total goals', 'goals over', 'goals under', 'o/u'])) {
            $parsed = $this->parseOverUnder($s);
            if ($parsed !== null) {
                [$direction, $line] = $parsed;
                if ($direction === 'over') {
                    return [$totalGoals > $line ? 'win' : 'loss', "FT: {$scoreStr}. Total goals: {$totalGoals} vs line {$line}"];
                }
                return [$totalGoals < $line ? 'win' : 'loss', "FT: {$scoreStr}. Total goals: {$totalGoals} vs line {$line}"];
            }
            return ['void', "Could not parse line from selection '{$selection}'."];
        }

        // ── Double Chance ────────────────────────────────────────────
        if ($this->matchesMarket($m, ['double chance', 'dc'])) {
            if (str_contains($s, '1x') || str_contains($s, 'home or draw') || str_contains($s, '1 or x')) {
                return [! $awayWins ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            if (str_contains($s, 'x2') || str_contains($s, 'draw or away') || str_contains($s, 'x or 2')) {
                return [! $homeWins ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            if (str_contains($s, '12') || str_contains($s, 'home or away') || str_contains($s, '1 or 2')) {
                return [! $draw ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            return ['void', "Could not parse Double Chance selection '{$selection}'."];
        }

        // ── Draw No Bet (DNB) ────────────────────────────────────────
        if ($this->matchesMarket($m, ['draw no bet', 'dnb'])) {
            if ($draw) {
                return ['void', "FT: {$scoreStr}. Draw — stake returned (void)."];
            }
            if ($this->matchesHomeWin($s, $fixture)) {
                return [$homeWins ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            if ($this->matchesAwayWin($s, $fixture)) {
                return [$awayWins ? 'win' : 'loss', "FT: {$scoreStr}"];
            }
            return ['void', "Could not parse DNB selection '{$selection}'."];
        }

        // ── Correct Score ─────────────────────────────────────────────
        if ($this->matchesMarket($m, ['correct score', 'exact score', 'scoreline'])) {
            // Match patterns like "2-1", "2 - 1", "2:1"
            if (preg_match('/(\d+)\s*[-:]\s*(\d+)/', $s, $m2)) {
                $predHome = (int) $m2[1];
                $predAway = (int) $m2[2];
                $isWin    = $predHome === $scoreHome && $predAway === $scoreAway;
                return [$isWin ? 'win' : 'loss', "FT: {$scoreStr}. Predicted: {$predHome}-{$predAway}"];
            }
            return ['void', "Could not parse correct score from '{$selection}'."];
        }

        // ── Asian Handicap ────────────────────────────────────────────
        if ($this->matchesMarket($m, ['asian handicap', 'ah', 'handicap'])) {
            $resolved = $this->resolveAsianHandicap($s, $scoreHome, $scoreAway, $fixture);
            if ($resolved !== null) {
                return [$resolved, "FT: {$scoreStr}"];
            }
            return ['void', "Asian Handicap '{$selection}' could not be automatically resolved."];
        }

        // ── Half-Time Result ──────────────────────────────────────────
        if ($this->matchesMarket($m, ['half time', 'ht result', 'half-time', '1st half'])) {
            return ['void', "Half-time results require half-time score data, which is not stored. Please resolve manually."];
        }

        // ── Fallback ──────────────────────────────────────────────────
        return ['void', "Market '{$market}' is not automatically resolvable. Please resolve manually."];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Resolution helpers
    // ══════════════════════════════════════════════════════════════════

    private function matchesMarket(string $market, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($market, $kw)) {
                return true;
            }
        }
        return false;
    }

    private function matchesHomeWin(string $selection, Fixture $fixture): bool
    {
        $homeTeam = strtolower($fixture->home_team);
        return str_contains($selection, 'home')
            || str_contains($selection, 'home win')
            || $selection === '1'
            || str_contains($selection, $homeTeam);
    }

    private function matchesAwayWin(string $selection, Fixture $fixture): bool
    {
        $awayTeam = strtolower($fixture->away_team);
        return str_contains($selection, 'away')
            || str_contains($selection, 'away win')
            || $selection === '2'
            || str_contains($selection, $awayTeam);
    }

    private function matchesDraw(string $selection): bool
    {
        return str_contains($selection, 'draw')
            || $selection === 'x'
            || str_contains($selection, 'tie');
    }

    private function matchesYes(string $selection): bool
    {
        return str_contains($selection, 'yes') || $selection === 'gg';
    }

    private function matchesNo(string $selection): bool
    {
        return str_contains($selection, 'no') || $selection === 'ng';
    }

    /**
     * Parse "Over 2.5", "Under 3.5" etc from a selection string.
     *
     * @return array{0: 'over'|'under', 1: float}|null
     */
    private function parseOverUnder(string $selection): ?array
    {
        if (preg_match('/(over|under|o|u)\s*(\d+(?:\.\d+)?)/i', $selection, $m)) {
            $direction = strtolower($m[1][0]) === 'o' ? 'over' : 'under';
            return [$direction, (float) $m[2]];
        }
        return null;
    }

    /**
     * Basic Asian Handicap resolver for quarter-less lines.
     * Returns 'win', 'loss', 'void' or null (unresolvable).
     */
    private function resolveAsianHandicap(
        string $selection,
        int $scoreHome,
        int $scoreAway,
        Fixture $fixture
    ): ?string {
        // Parse: "Arsenal -1.5", "Home -1", "Away +0.5"
        $homeTeam = strtolower($fixture->home_team);
        $awayTeam = strtolower($fixture->away_team);

        $isHome = str_contains($selection, 'home') || str_contains($selection, $homeTeam);
        $isAway = str_contains($selection, 'away') || str_contains($selection, $awayTeam);

        if (! $isHome && ! $isAway) {
            return null;
        }

        // Extract handicap value e.g. -1.5, +0.5
        if (! preg_match('/([+-]?\d+(?:\.\d+)?)/', $selection, $m)) {
            return null;
        }

        $handicap   = (float) $m[1];
        $adjustedHome = $scoreHome + ($isHome ? $handicap : -$handicap);
        $adjustedAway = $scoreAway + ($isAway ? $handicap : -$handicap);

        if ($adjustedHome > $adjustedAway) {
            return $isHome ? 'win' : 'loss';
        }
        if ($adjustedHome < $adjustedAway) {
            return $isAway ? 'win' : 'loss';
        }

        // Dead heat on a whole number = void (push)
        return 'void';
    }

    // ══════════════════════════════════════════════════════════════════
    //  Profit / Loss
    // ══════════════════════════════════════════════════════════════════

    /**
     * Calculate net profit/loss per 1 unit stake.
     *  Win  → odds - 1  (e.g. bet at 2.50 → profit +1.50)
     *  Loss → -1.00
     *  Void → 0.00
     */
    private function calculateProfitLoss(string $outcome, ?float $odds): float
    {
        return match ($outcome) {
            'win'  => round(($odds ?? 2.00) - 1, 4),
            'loss' => -1.00,
            'void' => 0.00,
            default => 0.00,
        };
    }
}
