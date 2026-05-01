<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class DeepSeekService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;
    protected int $confidenceThreshold;

    public function __construct()
    {
        $config = config('services.deepseek');

        $this->apiKey              = $config['key'] ?? throw new \RuntimeException('DEEPSEEK_API_KEY is not set in .env');
        $this->apiUrl              = $config['url'];
        $this->model               = $config['model'];
        $this->confidenceThreshold = $config['confidence_threshold'];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Primary entry point — returns parsed markets array
    // ══════════════════════════════════════════════════════════════════

    /**
     * Analyse assembled match data and return high-confidence markets.
     *
     * @param  array  $matchData  Output of FootballApiService::assembleMatchData()
     * @return array<int, array{market: string, selection: string, confidence: int, odds: float|null, value_bet: bool, reasoning: string}>
     */
    public function analyseMatch(array $matchData): array
    {
        $system = $this->buildSystemPrompt();
        $user   = $this->buildUserPrompt($matchData);
        $raw    = $this->callApi($system, $user);

        $markets = $this->parseResponse($raw);

        // Filter by threshold and sort by confidence descending
            $markets = array_values(array_filter(
                $markets,
                fn ($m) => ($m['confidence'] ?? 0) >= $this->confidenceThreshold
            ));

            usort($markets, fn ($a, $b) => $b['confidence'] <=> $a['confidence']);

            return $markets;
    }

    // ══════════════════════════════════════════════════════════════════
    //  Prompt builders
    // ══════════════════════════════════════════════════════════════════

    private function buildSystemPrompt(): string
    {
        $threshold = $this->confidenceThreshold;

        return <<<SYSTEM
You are an elite sports betting analyst with 15+ years of experience in statistical modelling, football analytics, and value betting. You use a structured reasoning process before reaching conclusions.

CRITICAL: Avoid anchoring bias. Modern football produces high-scoring matches regularly. Do NOT default to under 2.5 predictions without strong defensive evidence. Evaluate BOTH over and under markets objectively using the same rigorous standards.

REASONING PROCESS (follow in order):
Step 1 — TEAM PROFILES: Assess BOTH attacking strength AND defensive solidity with equal weight. Compute implied scoring rates for both teams. Consider:
  - Attacking potency: goals scored, avg goals per game, shots on target
  - Defensive vulnerabilities: goals conceded, clean sheet rate, defensive injuries
  - BALANCED ASSESSMENT: Don't favor defensive metrics over offensive ones

Step 2 — MATCH CONTEXT: Factor in H2H patterns, current form trajectory (is a team improving or declining?), injuries to key positions (GK, CB, striker), and match stakes.

Step 3 — GOALS MARKET CALIBRATION: Before evaluating any market, calculate:
  - Combined avg goals per game for both teams (home + away rates)
  - H2H average goals and over 2.5 frequency
  - BTTS rate (indicates open, attacking football)
  - If combined average >= 2.6 goals AND H2H over 2.5 rate > 50%, OVER should be baseline consideration
  - If combined average <= 2.2 goals AND clean sheet rate > 40%, UNDER should be baseline consideration
  - Between 2.2-2.6: Neutral territory, let other signals decide

Step 4 — MARKET EVALUATION: For each candidate market, compute:
  - Your assessed probability (%)
  - Implied probability from odds (1/odds × 100)
  - Edge = assessed% minus implied% (positive = value)

Step 5 — FILTER: Only output markets where confidence >= {$threshold}% AND your assessed probability is clearly supported by at least 2 independent data signals.

MARKET PRIORITY ORDER (evaluate these in sequence):
1. Over/Under Goals (most data-driven market) — Consider BOTH over and under with equal objectivity
2. BTTS Yes/No — High BTTS rate suggests attacking football (supports over markets)
3. 1X2 / Double Chance / Draw No Bet
4. Asian Handicap / European Handicap
5. Half-Time Result / HT/FT
6. First Goal Scorer / Anytime Goal Scorer / First Team to Score
7. Total Corners / Total Cards
8. Win to Nil / Clean Sheet

OVER/UNDER EVALUATION PROTOCOL:
- OVER 2.5 signals: Combined avg > 2.6, BTTS > 60%, H2H over 2.5 > 60%, both teams scoring form, attacking injuries minimal, high-tempo leagues
- UNDER 2.5 signals: Combined avg < 2.2, Clean sheets > 40%, H2H over 2.5 < 35%, defensive form, GK/CB strength, low-tempo tactical matches
- REJECT market if signals are mixed or marginal — require CLEAR directional evidence
- DO NOT assume under 2.5 is "safer" — it's equally risky if data doesn't support it

SPORTYBET-SPECIFIC MARKETS: Consider SportyBet-style market names such as Draw No Bet, First Goal Scorer, Win to Nil, and Anytime Goal Scorer when these are the strongest signals.

CONFIDENCE CALIBRATION:
- 75–79%: One strong signal + one supporting signal
- 80–84%: Two strong signals + stats alignment
- 85–89%: Three or more signals, clear historical pattern
- 90%+: Reserve for near-certainty (e.g. dominant home team vs bottom side with no away wins)
- NEVER inflate confidence. Overconfident predictions destroy bankrolls.
- HOWEVER: Don't artificially deflate confidence on OVER markets just because they feel "riskier" — use the same evidence standards for both directions

BIAS CHECK: Before finalizing predictions, ask yourself:
- Am I gravitating toward under 2.5 without sufficient defensive evidence?
- Have I given equal analytical weight to attacking metrics (goals scored, BTTS rate) vs defensive metrics (goals conceded, clean sheets)?
- Would I recommend this under 2.5 if the odds were reversed?

VALUE BET DEFINITION: Mark value_bet=true only when your assessed probability exceeds implied probability by >= 5 percentage points.

Respond ONLY with a valid JSON array. No markdown, no preamble, no text outside JSON.

JSON format:
[
  {
    "market": "Market name",
    "selection": "Specific selection",
    "confidence": <integer 0-100>,
    "odds": <float|null>,
    "value_bet": <bool>,
    "assessed_probability": <integer 0-100>,
    "implied_probability": <integer 0-100>,
    "signals": ["signal 1", "signal 2", "signal 3"],
    "reasoning": "2-4 sentences citing specific numbers from the data."
  }
]

If no markets meet the threshold, return: []
SYSTEM;
    }

    private function buildUserPrompt(array $m): string
    {
        $threshold = $this->confidenceThreshold;

        $homeName = $m['home_team']   ?? 'Home';
        $awayName = $m['away_team']   ?? 'Away';
        $comp     = $m['competition'] ?? 'Unknown';
        $date     = $m['match_date']  ?? 'Unknown';
        $venue    = $m['venue']       ?? 'Unknown';
        $season   = $m['season']      ?? 'Unknown';

        $h2hSummary   = $this->summariseH2H($m['h2h'] ?? [], $homeName, $awayName);
        $homeFormLine = $this->describeFormTrend($m['home_form'] ?? []);
        $awayFormLine = $this->describeFormTrend($m['away_form'] ?? []);
        $hs           = $this->formatStats($m['home_stats'] ?? [], $homeName, 'Home');
        $as           = $this->formatStats($m['away_stats'] ?? [], $awayName, 'Away');
        $homeDerived  = $this->deriveMetrics($m['home_stats'] ?? [], 'home');
        $awayDerived  = $this->deriveMetrics($m['away_stats'] ?? [], 'away');
        $od           = $this->formatOdds($m['odds'] ?? []);
        $homeAbs      = $this->formatAbsences($m['home_absences'] ?? 'None reported', $homeName);
        $awayAbs      = $this->formatAbsences($m['away_absences'] ?? 'None reported', $awayName);
        $predSection  = $this->formatPredictions($m['predictions'] ?? [], $homeName, $awayName);
        $lineupSection= $this->formatLineups($m['lineups'] ?? []);

        $referee   = $m['referee']    ?? null;
        $venueCity = $m['venue_city'] ?? null;
        $round     = $m['round']      ?? null;

        $contextExtras = implode(' | ', array_filter([
            $round    ? "Round: {$round}"       : null,
            $venueCity? "City: {$venueCity}"    : null,
            $referee  ? "Referee: {$referee}"   : null,
        ]));

        return <<<DATA
Match: {$homeName} vs {$awayName}
Competition: {$comp} | Season: {$season}
Date: {$date} | Venue: {$venue}
{$contextExtras}

=== HEAD TO HEAD ===
{$h2hSummary}

=== RECENT FORM (last 5, most recent first) ===
{$homeName}: {$homeFormLine}
{$awayName}: {$awayFormLine}

{$hs}

{$homeDerived}

{$as}

{$awayDerived}

=== BOOKMAKER ODDS (Bet365) ===
{$od}

{$predSection}

{$lineupSection}

=== INJURIES & SUSPENSIONS ===
{$homeAbs}
{$awayAbs}

=== TASK ===
Follow the structured reasoning process in your system instructions.
Only return markets with confidence >= {$threshold}%.
Return strict JSON array only — zero text outside JSON.
DATA;
    }

    private function formatPredictions(array $p, string $home, string $away): string
    {
        // NOTE: We intentionally omit win %, predicted winner, predicted score and
        // advice to prevent the AI anchoring on another model's conclusions.
        // Only neutral comparative data is passed so DeepSeek reasons independently.

        $cmp = $p['comparison'] ?? [];
        $cmpLabels = ['form' => 'Form', 'att' => 'Attack', 'def' => 'Defence', 'h2h' => 'H2H', 'total' => 'Overall'];

        $cmpRows = [];
        foreach ($cmpLabels as $key => $label) {
            if (!empty($cmp[$key]['home']) && !empty($cmp[$key]['away'])) {
                $cmpRows[] = "{$label}: {$home} {$cmp[$key]['home']} vs {$away} {$cmp[$key]['away']}";
            }
        }

        // Goals market signal only — directional hint without anchoring a winner
        $lines = ['=== STATISTICAL COMPARISON (API-Football) ==='];

        if (!empty($p['under_over'])) {
            $lines[] = "Goals market signal: {$p['under_over']}";
        }

        if ($cmpRows) {
            $lines[] = 'Team comparison (% advantage per category):';
            foreach ($cmpRows as $row) {
                $lines[] = "  {$row}";
            }
        }

        if (count($lines) === 1) {
            return '';
        }

        return implode("\n", $lines);
    }

    private function formatLineups(array $lineups): string
    {
        if (empty($lineups['home']['start_xi']) && empty($lineups['away']['start_xi'])) {
            return '=== LINEUPS ===\nNot confirmed yet';
        }

        $lines = [];
        foreach (['home', 'away'] as $side) {
            if (empty($lineups[$side]['team'])) {
                continue;
            }
            $team = $lineups[$side];
            $formation = $team['formation'] ? " ({$team['formation']})" : '';
            $lines[]   = "{$team['team']}{$formation}:";

            $xi = $team['start_xi'] ?? [];
            // Group by grid row if available; otherwise just list
            $players = array_map(fn ($p) => "{$p['number']}.{$p['name']}[{$p['pos']}]", $xi);
            $lines[] = '  XI: ' . implode(', ', $players);

            $subs = $team['substitutes'] ?? [];
            if ($subs) {
                $subNames = array_map(fn ($p) => "{$p['name']}[{$p['pos']}]", array_slice($subs, 0, 7));
                $lines[]  = '  Subs: ' . implode(', ', $subNames);
            }
        }

        return implode("\n", $lines);
    }

    private function formatStats(array $s, string $label, string $venue): string
    {
        if (empty(array_filter($s))) {
            return "=== SEASON STATS — {$label} ({$venue}) ===\nNot provided";
        }

        $n = fn ($k) => $s[$k] ?? 'N/A';

        return "=== SEASON STATS — {$label} ({$venue}) ===\n"
            . "Position: {$n('position')} | Points: {$n('points')} | Played: {$n('played')}\n"
            . "Overall: W{$n('wins')} D{$n('draws')} L{$n('losses')}\n"
            . "Goals Scored: {$n('goals_scored')} | Goals Conceded: {$n('goals_conceded')}\n"
            . "{$venue} Record: W{$n('venue_wins')} D{$n('venue_draws')} L{$n('venue_losses')}\n"
            . "Avg Goals ({$venue}): {$n('avg_goals_venue')} | Clean Sheets ({$venue}): {$n('clean_sheets')}\n"
            . "BTTS ({$venue} games): {$n('btts_count')} | Over 2.5 ({$venue} games): {$n('over25_count')}";
    }

    private function formatOdds(array $o): string
    {
        if (empty(array_filter($o))) {
            return 'Not provided';
        }

        $labels = [
            'home_win'     => 'Home Win', 'draw'         => 'Draw',    'away_win'     => 'Away Win',
            'btts_yes'     => 'BTTS Yes', 'btts_no'      => 'BTTS No',
            'over15'       => 'Over 1.5', 'under15'      => 'Under 1.5',
            'over25'       => 'Over 2.5', 'under25'      => 'Under 2.5',
            'over35'       => 'Over 3.5', 'under35'      => 'Under 3.5',
            'dc_home_draw' => 'DC 1X',    'dc_away_draw' => 'DC X2',   'dc_home_away' => 'DC 12',
        ];

        $parts = [];
        foreach ($labels as $key => $label) {
            if (!empty($o[$key])) {
                $odd     = (float) $o[$key];
                $implied = $odd > 0 ? round((1 / $odd) * 100) : 0;
                $parts[] = "{$label}: {$odd} (impl. {$implied}%)";
            }
        }

        return implode(' | ', $parts) ?: 'Not provided';
    }

    private function deriveMetrics(array $s, string $venue): string
    {
        if (empty(array_filter($s))) {
            return '--- DERIVED METRICS (' . ucfirst($venue) . ") ---\nInsufficient data";
        }

        $played      = $s['played']         ?? 0;
        $vWins       = $s['venue_wins']      ?? 0;
        $vDraws      = $s['venue_draws']     ?? 0;
        $vLosses     = $s['venue_losses']    ?? 0;
        $vPlayed     = $vWins + $vDraws + $vLosses;
        $gScored     = $s['goals_scored']    ?? 0;
        $gConceded   = $s['goals_conceded']  ?? 0;
        $cleanSheets = $s['clean_sheets']    ?? 0;
        $btts        = $s['btts_count']      ?? 0;
        $over25      = $s['over25_count']    ?? 0;
        $avgFor      = $s['avg_goals_venue'] ?? 0;

        $winPct     = $vPlayed > 0 ? round(($vWins / $vPlayed) * 100) : 0;
        $avgAgainst = $played  > 0 ? round($gConceded / $played, 2)   : 0;
        $csPct      = $vPlayed > 0 ? round(($cleanSheets / $vPlayed) * 100) : 0;
        $bttsPct    = $vPlayed > 0 ? round(($btts / $vPlayed) * 100)  : 0;
        $over25Pct  = $vPlayed > 0 ? round(($over25 / $vPlayed) * 100) : 0;
        $goalDiff   = $gScored - $gConceded;
        $label      = ucfirst($venue);

        return <<<METRICS
--- DERIVED METRICS ({$label}) ---
{$label} Win Rate: {$winPct}% ({$vWins}W/{$vDraws}D/{$vLosses}L in {$vPlayed} {$venue} games)
Avg Goals Scored ({$label}): {$avgFor} per game
Avg Goals Conceded (Overall): {$avgAgainst} per game
Goal Difference (Season): {$goalDiff}
Clean Sheet Rate ({$label}): {$csPct}% ({$cleanSheets} in {$vPlayed} games)
BTTS Rate ({$label}): {$bttsPct}% ({$btts} in {$vPlayed} games)
Over 2.5 Rate ({$label}): {$over25Pct}% ({$over25} in {$vPlayed} games)
METRICS;
    }

    private function summariseH2H(array $h2h, string $home, string $away): string
    {
        if (empty($h2h)) {
            return 'No H2H data available';
        }

        $homeWins = $awayWins = $draws = $totalGoals = $over25Count = $bttsCount = $count = 0;

        foreach ($h2h as $g) {
            // Normalise score — handle "2-1", "2 - 1", "2:1" formats
            $scoreRaw = preg_replace('/[^0-9\-]/', '', str_replace([':', ' '], '-', $g['score'] ?? '0-0'));
            $parts    = explode('-', $scoreRaw);
            $hg       = (int) ($parts[0] ?? 0);
            $ag       = (int) ($parts[1] ?? 0);

            $totalGoals += $hg + $ag;
            $count++;

            if ($hg + $ag > 2)  $over25Count++;
            if ($hg > 0 && $ag > 0) $bttsCount++;

            // Robust home team matching — check both directions
            $storedHome = $g['home_team'] ?? '';
            $isHome     = $this->teamsMatch($storedHome, $home);

            // If neither team matches $home, fall back to positional assumption
            // (first team listed = home in most APIs)
            if (!$isHome && !$this->teamsMatch($storedHome, $away)) {
                $isHome = true; // assume positional
            }

            if ($hg > $ag) {
                $isHome ? $homeWins++ : $awayWins++;
            } elseif ($ag > $hg) {
                $isHome ? $awayWins++ : $homeWins++;
            } else {
                $draws++;
            }
        }

        $avgGoals  = $count > 0 ? round($totalGoals / $count, 2) : 0;
        $over25Pct = $count > 0 ? round(($over25Count / $count) * 100) : 0;
        $bttsPct   = $count > 0 ? round(($bttsCount / $count) * 100) : 0;

        return "--- H2H SUMMARY (last {$count} meetings) ---\n"
            . "{$home} wins: {$homeWins} | Draws: {$draws} | {$away} wins: {$awayWins}\n"
            . "Avg goals per meeting: {$avgGoals}\n"
            . "Over 2.5 in {$over25Count} of {$count} meetings ({$over25Pct}%)\n"
            . "BTTS in {$bttsCount} of {$count} meetings ({$bttsPct}%)";
    }

    /**
     * Fuzzy team name match — handles abbreviations, short names, city-only names.
     * e.g. "Man United" matches "Manchester United", "Arsenal" matches "Arsenal FC"
     */
    private function teamsMatch(string $stored, string $expected): bool
    {
        $stored   = strtolower(trim($stored));
        $expected = strtolower(trim($expected));

        // Exact match
        if ($stored === $expected) return true;

        // One contains the other (handles "Arsenal" vs "Arsenal FC")
        if (str_contains($stored, $expected) || str_contains($expected, $stored)) return true;

        // Word-level overlap — at least one significant word matches
        $stopWords    = ['fc', 'cf', 'afc', 'united', 'city', 'the', 'de', 'ac', 'sc', 'us', 'as'];
        $storedWords  = array_diff(explode(' ', $stored), $stopWords);
        $expectedWords = array_diff(explode(' ', $expected), $stopWords);

        $overlap = array_intersect($storedWords, $expectedWords);

        return count($overlap) > 0;
    }

    private function describeFormTrend(array $form): string
    {
        if (count($form) < 3) {
            return empty($form)
                ? 'Not provided'
                : implode(', ', $form) . ' | Insufficient data for trend';
        }

        $points = array_map(fn ($r) => match ($r) {
            'W'     => 3,
            'D'     => 1,
            default => 0,
        }, $form);

        $recentSlice = array_slice($points, 0, 3);
        $olderSlice  = array_slice($points, 3);

        // Normalise to points-per-game so sample sizes don't skew comparison
        $recentPpg = array_sum($recentSlice) / count($recentSlice);
        $olderPpg  = count($olderSlice) > 0
            ? array_sum($olderSlice) / count($olderSlice)
            : $recentPpg; // no older data — treat as consistent

        // 0.5 PPG delta = meaningful shift (1.5pts per 3 games)
        $trend = match (true) {
            $recentPpg > $olderPpg + 0.5 => 'IMPROVING (upward momentum)',
            $olderPpg > $recentPpg + 0.5 => 'DECLINING (form dropping off)',
            default                       => 'CONSISTENT',
        };

        $totalPts = array_sum($points);
        $maxPts   = count($form) * 3;

        return implode(', ', $form)
            . " | {$totalPts}/{$maxPts} pts"
            . " | PPG last 3: " . round($recentPpg, 2)
            . " | Trend: {$trend}";
    }

    private function formatAbsences(string $absences, string $teamName): string
    {
        if (!$absences || $absences === 'None reported') {
            return "{$teamName}: Full squad available ✓";
        }

        $critical = [
            // Goalkeepers
            'goalkeeper', 'gk',
            // Centre-backs / defenders
            'centre-back', 'center-back', 'cb', 'centreback',
            // Full-backs (can be critical depending on system)
            'right-back', 'left-back', 'rb', 'lb', 'wingback', 'wing-back', 'rwb', 'lwb',
            // Defensive midfield
            'defensive mid', 'cdm', 'holding mid', 'pivot',
            // Strikers / forwards
            'striker', 'centre-forward', 'center-forward', 'cf', 'st',
            'number 9', 'no.9', 'no 9',
            // Attacking mid / key creators
            'attacking mid', 'cam', 'playmaker', 'number 10', 'no.10', 'no 10',
            // Leadership
            'captain', 'skipper',
        ];

        $isCritical = false;
        foreach ($critical as $pos) {
            if (stripos($absences, $pos) !== false) {
                $isCritical = true;
                break;
            }
        }

        $flag = $isCritical ? ' ⚠ CRITICAL POSITION MISSING' : '';

        return "{$teamName}: {$absences}{$flag}";
    }

    // ══════════════════════════════════════════════════════════════════
    //  API call
    // ══════════════════════════════════════════════════════════════════

    protected function callApi(string $systemPrompt, string $userPrompt): string
    {
        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout(90)
            ->post($this->apiUrl, [
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'temperature' => 0.0,
                'max_tokens'  => 3000,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                "DeepSeek API error HTTP {$response->status()}: " . substr($response->body(), 0, 300)
            );
        }

        return $response->json('choices.0.message.content', '');
    }

    // ══════════════════════════════════════════════════════════════════
    //  Response parsing
    // ══════════════════════════════════════════════════════════════════

    private function parseResponse(string $raw): array
    {
        $clean = $this->stripFences($raw);

        // Attempt 1 — clean parse
        $markets = json_decode($clean, true);
        if (is_array($markets)) {
            return $markets;
        }

        // Attempt 2 — extract JSON array from anywhere in the string
        // Handles cases where DeepSeek adds preamble/postamble text
        if (preg_match('/\[.*\]/s', $clean, $matches)) {
            $markets = json_decode($matches[0], true);
            if (is_array($markets)) {
                return $markets;
            }
        }

        // Attempt 3 — truncated JSON recovery
        // If token limit cut the response mid-array, close it and retry
        $recovered = $this->attemptTruncationRecovery($clean);
        if ($recovered !== null) {
            return $recovered;
        }

        // All attempts failed — log and return empty rather than crashing
        \Log::warning('DeepSeek: unparseable response', [
            'raw_preview' => substr($raw, 0, 500),
        ]);

        return [];
    }

    private function stripFences(string $raw): string
    {
        $clean = trim($raw);
        // Remove opening fence (```json or ```)
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        // Remove closing fence
        $clean = preg_replace('/\s*```\s*$/i', '', $clean);

        return trim($clean);
    }

    private function attemptTruncationRecovery(string $partial): ?array
    {
        // Find the last complete object by locating the last closing brace
        $lastBrace = strrpos($partial, '}');
        if ($lastBrace === false) {
            return null;
        }

        // Close off the array after the last complete object
        $truncated = substr($partial, 0, $lastBrace + 1) . ']';

        // Find the opening bracket to ensure we have a valid start
        $firstBracket = strpos($truncated, '[');
        if ($firstBracket === false) {
            return null;
        }

        $truncated = substr($truncated, $firstBracket);
        $markets   = json_decode($truncated, true);

        return is_array($markets) ? $markets : null;
    }
}
