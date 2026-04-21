<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class DeepSeekService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private int $confidenceThreshold;

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

REASONING PROCESS (follow in order):
Step 1 — TEAM PROFILES: Assess each team's attacking strength, defensive solidity, and home/away tendencies from the stats. Compute implied scoring rates.
Step 2 — MATCH CONTEXT: Factor in H2H patterns, current form trajectory (is a team improving or declining?), injuries to key positions (GK, CB, striker), and match stakes.
Step 3 — MARKET EVALUATION: For each candidate market, compute:
  - Your assessed probability (%)
  - Implied probability from odds (1/odds × 100)
  - Edge = assessed% minus implied% (positive = value)
Step 4 — FILTER: Only output markets where confidence >= {$threshold}% AND your assessed probability is clearly supported by at least 2 independent data signals.

MARKET PRIORITY ORDER (evaluate these in sequence):
1. Over/Under Goals (most data-driven market)
2. BTTS Yes/No
3. 1X2 / Double Chance
4. Asian Handicap
5. Half-Time Result
6. Clean Sheet

CONFIDENCE CALIBRATION:
- 75–79%: One strong signal + one supporting signal
- 80–84%: Two strong signals + stats alignment
- 85–89%: Three or more signals, clear historical pattern
- 90%+: Reserve for near-certainty (e.g. dominant home team vs bottom side with no away wins)
- NEVER inflate confidence. Overconfident predictions destroy bankrolls.

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

        $lines   = ['=== CONFIRMED LINEUPS ==='];

        foreach (['home', 'away'] as $side) {
            $team = $lineups[$side] ?? [];
            if (empty($team['team'])) {
                continue;
            }

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

        $homeWins = $awayWins = $draws = $totalGoals = $over25Count = $count = 0;

        foreach ($h2h as $g) {
            $parts = explode('-', str_replace(' ', '', $g['score'] ?? '0-0'));
            $hg    = (int) ($parts[0] ?? 0);
            $ag    = (int) ($parts[1] ?? 0);
            $totalGoals += $hg + $ag;
            $count++;

            if ($hg + $ag > 2) {
                $over25Count++;
            }

            $isHome = stripos($g['home_team'] ?? '', $home) !== false;
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

        return "--- H2H SUMMARY (last {$count} meetings) ---\n"
            . "{$home} wins: {$homeWins} | Draws: {$draws} | {$away} wins: {$awayWins}\n"
            . "Avg goals per meeting: {$avgGoals}\n"
            . "Over 2.5 in {$over25Count} of {$count} meetings ({$over25Pct}%)";
    }

    private function describeFormTrend(array $form): string
    {
        if (count($form) < 3) {
            return empty($form)
                ? 'Not provided'
                : implode(', ', $form) . ' | Insufficient data for trend';
        }

        $points = array_map(fn ($r) => match ($r) { 'W' => 3, 'D' => 1, default => 0 }, $form);
        $recent = array_sum(array_slice($points, 0, 3));
        $older  = array_sum(array_slice($points, 3));

        $trend = match (true) {
            $recent > $older + 2 => 'IMPROVING (upward momentum)',
            $older > $recent + 2 => 'DECLINING (form dropping off)',
            default              => 'CONSISTENT',
        };

        return implode(', ', $form) . ' | ' . array_sum($points) . '/15 pts | Trend: ' . $trend;
    }

    private function formatAbsences(string $absences, string $teamName): string
    {
        if (!$absences || $absences === 'None reported') {
            return "{$teamName}: Full squad available ✓";
        }

        $critical   = ['goalkeeper', 'gk', 'striker', 'centre-back', 'cb', 'captain'];
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

    private function callApi(string $systemPrompt, string $userPrompt): string
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
                'temperature' => 0.2,
                'max_tokens'  => 1800,
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
        // Strip markdown code fences if DeepSeek wraps in ```json ... ```
        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($raw));
        $clean = preg_replace('/\s*```$/', '', $clean);

        $markets = json_decode($clean, true);

        if (!is_array($markets)) {
            throw new RuntimeException(
                'DeepSeek returned invalid JSON: ' . substr($raw, 0, 400)
            );
        }

        return $markets;
    }
}
