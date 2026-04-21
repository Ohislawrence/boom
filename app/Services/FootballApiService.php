<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FootballApiService
{
    private PendingRequest $http;
    private int $h2hLimit;
    private int $formMatches;

    public function __construct()
    {
        $config = config('services.football_api');

        $this->h2hLimit    = $config['h2h_limit'];
        $this->formMatches = $config['form_matches'];

        $apiKey = $config['key'] ?? throw new \RuntimeException('FOOTBALL_API_KEY is not set in .env');

        $this->http = Http::baseUrl($config['base_url'])
            ->withHeaders([
                'x-rapidapi-key'  => $apiKey,
                'x-rapidapi-host' => 'v3.football.api-sports.io',
            ])
            ->timeout(20)
            ->throw(); // throws on 4xx/5xx
    }

    // ══════════════════════════════════════════════════════════════════
    //  Core request
    // ══════════════════════════════════════════════════════════════════

    private function get(string $endpoint, array $params = []): array
    {
        $response = $this->http->get($endpoint, $params);
        $data     = $response->json();

        if (!empty($data['errors'])) {
            $msg = is_array($data['errors'])
                ? implode(', ', array_filter($data['errors']))
                : json_encode($data['errors']);

            throw new RuntimeException("API-Football error on {$endpoint}: {$msg}");
        }

        return $data['response'] ?? [];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Fixture lookups
    // ══════════════════════════════════════════════════════════════════

    public function getFixture(int $fixtureId): array
    {
        $response = $this->get('/fixtures', ['id' => $fixtureId]);

        if (empty($response)) {
            throw new RuntimeException("Fixture #{$fixtureId} not found.");
        }

        return $response[0];
    }

    /**
     * Fetch all NS fixtures for a specific date across given league IDs.
     * If no league IDs provided, fetches all available fixtures for the date.
     */
    public function getFixturesByDate(string $date, array $leagueIds = []): array
    {
        if (!empty($leagueIds)) {
            $fixtures = [];
            foreach ($leagueIds as $leagueId) {
                $results  = $this->get('/fixtures', ['date' => $date, 'league' => $leagueId, 'status' => 'NS']);
                $fixtures = array_merge($fixtures, $results);
            }
            return $fixtures;
        }

        return $this->get('/fixtures', ['date' => $date, 'status' => 'NS']);
    }

    /**
     * Search upcoming fixtures by team name (next 10 for each matching team).
     */
    public function searchFixtures(string $query, string $date = ''): array
    {
        $query = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $query));
        if (!$query) {
            throw new RuntimeException('Search term must contain letters or numbers.');
        }

        $teams   = $this->get('/teams', ['search' => $query]);
        $teamIds = array_slice(
            array_column(array_column($teams, 'team'), 'id'),
            0, 3
        );

        $fixtures = [];
        $seen     = [];

        foreach ($teamIds as $teamId) {
            $params = $date
                ? ['team' => $teamId, 'date' => $date, 'season' => substr($date, 0, 4)]
                : ['team' => $teamId, 'next' => 10, 'status' => 'NS'];

            foreach ($this->get('/fixtures', $params) as $f) {
                $fid = $f['fixture']['id'];
                if (isset($seen[$fid])) {
                    continue;
                }
                $seen[$fid] = true;

                $fixtures[] = [
                    'fixture_id'  => $fid,
                    'date'        => $f['fixture']['date'],
                    'home_team'   => $f['teams']['home']['name'],
                    'home_logo'   => $f['teams']['home']['logo'],
                    'away_team'   => $f['teams']['away']['name'],
                    'away_logo'   => $f['teams']['away']['logo'],
                    'competition' => $f['league']['name'],
                    'country'     => $f['league']['country'],
                    'venue'       => $f['fixture']['venue']['name'] ?? '',
                    'status'      => $f['fixture']['status']['long'],
                ];
            }
        }

        usort($fixtures, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return array_slice($fixtures, 0, 20);
    }

    // ══════════════════════════════════════════════════════════════════
    //  H2H
    // ══════════════════════════════════════════════════════════════════

    public function getH2H(int $homeId, int $awayId): array
    {
        $raw = $this->get('/fixtures/headtohead', [
            'h2h'  => "{$homeId}-{$awayId}",
            'last' => $this->h2hLimit,
        ]);

        return array_map(fn ($f) => [
            'date'      => substr($f['fixture']['date'], 0, 10),
            'home_team' => $f['teams']['home']['name'],
            'away_team' => $f['teams']['away']['name'],
            'score'     => ($f['goals']['home'] ?? '?') . ' - ' . ($f['goals']['away'] ?? '?'),
            'status'    => $f['fixture']['status']['short'],
        ], $raw);
    }

    // ══════════════════════════════════════════════════════════════════
    //  Form (last N completed matches)
    // ══════════════════════════════════════════════════════════════════

    public function getForm(int $teamId, int $season): array
    {
        $fixtures = $this->get('/fixtures', [
            'team'   => $teamId,
            'last'   => $this->formMatches,
            'season' => $season,
        ]);

        $form = [];
        foreach (array_reverse($fixtures) as $f) {
            $isHome = $f['teams']['home']['id'] === $teamId;
            $hg     = $f['goals']['home'] ?? null;
            $ag     = $f['goals']['away'] ?? null;
            if ($hg === null || $ag === null) {
                continue;
            }
            $form[] = $isHome
                ? ($hg > $ag ? 'W' : ($hg < $ag ? 'L' : 'D'))
                : ($ag > $hg ? 'W' : ($ag < $hg ? 'L' : 'D'));
        }

        return array_reverse($form); // most recent first
    }

    // ══════════════════════════════════════════════════════════════════
    //  Standings
    // ══════════════════════════════════════════════════════════════════

    // ══════════════════════════════════════════════════════════════════
    //  Finished fixture result (for tip resolution)
    // ══════════════════════════════════════════════════════════════════

    /**
     * Fetch a finished fixture's final score from API-Football.
     * Returns null if the match is not yet finished or has no score.
     *
     * @return array{status:string, score_home:int, score_away:int}|null
     */
    public function getFixtureResult(int $apiFootballId): ?array
    {
        $data = $this->get('/fixtures', ['id' => $apiFootballId]);

        if (empty($data)) {
            return null;
        }

        $f          = $data[0];
        $statusShort = $f['fixture']['status']['short'] ?? '';
        $home        = $f['goals']['home'] ?? null;
        $away        = $f['goals']['away'] ?? null;

        if ($home === null || $away === null) {
            return null;
        }

        return [
            'status'     => $statusShort,
            'score_home' => (int) $home,
            'score_away' => (int) $away,
        ];
    }

    public function getStandings(int $leagueId, int $season): array
    {
        return $this->get('/standings', ['league' => $leagueId, 'season' => $season]);
    }

    public function extractStandingStats(array $standings, int $teamId, string $venue): array
    {
        foreach ($standings as $group) {
            if (!isset($group['league']['standings'])) {
                continue;
            }
            foreach ($group['league']['standings'] as $table) {
                foreach ($table as $row) {
                    if (($row['team']['id'] ?? 0) !== $teamId) {
                        continue;
                    }
                    $all        = $row['all']  ?? [];
                    $venueData  = ($venue === 'home' ? $row['home'] : $row['away']) ?? [];

                    return [
                        'position'        => $row['rank']              ?? null,
                        'played'          => $all['played']            ?? null,
                        'wins'            => $all['win']               ?? null,
                        'draws'           => $all['draw']              ?? null,
                        'losses'          => $all['lose']              ?? null,
                        'goals_scored'    => $all['goals']['for']      ?? null,
                        'goals_conceded'  => $all['goals']['against']  ?? null,
                        'venue_wins'      => $venueData['win']         ?? null,
                        'venue_draws'     => $venueData['draw']        ?? null,
                        'venue_losses'    => $venueData['lose']        ?? null,
                        'points'          => $row['points']            ?? null,
                        'avg_goals_venue' => null,
                        'clean_sheets'    => null,
                        'btts_count'      => null,
                        'over25_count'    => null,
                    ];
                }
            }
        }

        return [];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Team season statistics (enriches standing stats)
    // ══════════════════════════════════════════════════════════════════

    public function getTeamStats(int $teamId, int $leagueId, int $season): array
    {
        $result = $this->get('/teams/statistics', [
            'team'   => $teamId,
            'league' => $leagueId,
            'season' => $season,
        ]);

        return $result[0] ?? [];
    }

    public function enrichStatsFromTeamStats(array &$stats, array $ts, string $venue): void
    {
        if (empty($ts)) {
            return;
        }

        $fixtures  = $ts['fixtures']    ?? [];
        $goals     = $ts['goals']       ?? [];
        $cleanSh   = $ts['clean_sheet'] ?? [];

        $vPlayed  = $fixtures[$venue]['played']          ?? 0;
        $vFor     = $goals['for']['total'][$venue]       ?? null;
        $vAgainst = $goals['against']['total'][$venue]   ?? null;
        $vCs      = $cleanSh[$venue]                     ?? null;

        if ($vPlayed > 0 && $vFor !== null) {
            $stats['avg_goals_venue'] = round($vFor / $vPlayed, 2);
        }

        if ($vCs !== null) {
            $stats['clean_sheets'] = $vCs;
        }

        if ($vPlayed > 0 && $vFor !== null && $vAgainst !== null) {
            $avgTotal             = ($vFor + $vAgainst) / $vPlayed;
            $stats['over25_count'] = (int) round($vPlayed * min(1, max(0, ($avgTotal - 1.5) / 2)));
            $stats['btts_count']   = $vPlayed - ($stats['clean_sheets'] ?? 0);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    //  Pre-match predictions
    // ══════════════════════════════════════════════════════════════════

    /**
     * Fetch API-Football /predictions for a fixture.
     * Returns structured prediction data including win %, advice, comparison.
     */
    public function getPredictions(int $fixtureId): array
    {
        try {
            $raw = $this->get('/predictions', ['fixture' => $fixtureId]);
        } catch (\Throwable) {
            return [];
        }

        if (empty($raw[0])) {
            return [];
        }

        $p   = $raw[0]['predictions'] ?? [];
        $cmp = $raw[0]['comparison']  ?? [];

        $parsePercent = fn (?string $s) => $s ? (int) rtrim($s, '%') : null;

        return [
            'winner'        => $p['winner']['name']        ?? null,
            'winner_comment'=> $p['winner']['comment']     ?? null,
            'win_or_draw'   => $p['win_or_draw']           ?? null,
            'under_over'    => $p['under_over']            ?? null,
            'goals_home'    => $p['goals']['home']         ?? null,
            'goals_away'    => $p['goals']['away']         ?? null,
            'advice'        => $p['advice']                ?? null,
            'percent_home'  => $parsePercent($p['percent']['home'] ?? null),
            'percent_draw'  => $parsePercent($p['percent']['draw'] ?? null),
            'percent_away'  => $parsePercent($p['percent']['away'] ?? null),
            'comparison'    => [
                'form'   => $cmp['form']    ?? [],
                'att'    => $cmp['att']     ?? [],
                'def'    => $cmp['def']     ?? [],
                'h2h'    => $cmp['h2h']     ?? [],
                'goals'  => $cmp['goals']   ?? [],
                'total'  => $cmp['total']   ?? [],
            ],
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Lineups
    // ══════════════════════════════════════════════════════════════════

    /**
     * Fetch confirmed lineups from /fixtures/lineups.
     * Returns [home => [...], away => [...]] or empty array if not available.
     */
    public function getLineups(int $fixtureId): array
    {
        try {
            $raw = $this->get('/fixtures/lineups', ['fixture' => $fixtureId]);
        } catch (\Throwable) {
            return [];
        }

        if (count($raw) < 2) {
            return [];
        }

        $mapTeam = function (array $team): array {
            return [
                'team'      => $team['team']['name']  ?? '',
                'formation' => $team['formation']     ?? null,
                'start_xi'  => array_map(fn ($p) => [
                    'name'   => $p['player']['name']   ?? '',
                    'number' => $p['player']['number'] ?? '',
                    'pos'    => $p['player']['pos']    ?? '',
                    'grid'   => $p['player']['grid']   ?? null,
                ], $team['startXI'] ?? []),
                'substitutes' => array_map(fn ($p) => [
                    'name'   => $p['player']['name']   ?? '',
                    'number' => $p['player']['number'] ?? '',
                    'pos'    => $p['player']['pos']    ?? '',
                ], $team['substitutes'] ?? []),
            ];
        };

        return [
            'home' => $mapTeam($raw[0]),
            'away' => $mapTeam($raw[1]),
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Injuries
    // ══════════════════════════════════════════════════════════════════

    public function getInjuries(int $teamId, int $fixtureId): string
    {
        $injuries = $this->get('/injuries', ['team' => $teamId, 'fixture' => $fixtureId]);

        if (empty($injuries)) {
            return 'None reported';
        }

        $list = array_map(fn ($inj) => sprintf(
            '%s (%s)',
            $inj['player']['name'] ?? 'Unknown',
            $inj['player']['reason'] ?? ($inj['player']['type'] ?? 'Unavailable')
        ), $injuries);

        return implode(', ', $list);
    }

    // ══════════════════════════════════════════════════════════════════
    //  Odds
    // ══════════════════════════════════════════════════════════════════

    /**
     * @param  int  $bookmakerId  8 = Bet365 (default)
     */
    public function getOdds(int $fixtureId, int $bookmakerId = 8): array
    {
        $raw = $this->get('/odds', ['fixture' => $fixtureId, 'bookmaker' => $bookmakerId]);

        if (empty($raw)) {
            return [];
        }

        $odds = [];
        $bets = $raw[0]['bookmakers'][0]['bets'] ?? [];

        foreach ($bets as $bet) {
            $name   = strtolower($bet['name'] ?? '');
            $values = $bet['values'] ?? [];

            // 1X2
            if (str_contains($name, 'match winner') || $name === '1x2') {
                foreach ($values as $v) {
                    $val = strtolower($v['value'] ?? '');
                    if ($val === 'home')     $odds['home_win'] = (float) $v['odd'];
                    elseif ($val === 'draw') $odds['draw']     = (float) $v['odd'];
                    elseif ($val === 'away') $odds['away_win'] = (float) $v['odd'];
                }
            }

            // BTTS
            if (str_contains($name, 'both teams') || str_contains($name, 'btts')) {
                foreach ($values as $v) {
                    $val = strtolower($v['value'] ?? '');
                    if ($val === 'yes') $odds['btts_yes'] = (float) $v['odd'];
                    if ($val === 'no')  $odds['btts_no']  = (float) $v['odd'];
                }
            }

            // Goals over/under
            if (str_contains($name, 'goals over/under') || str_contains($name, 'total goals')) {
                $oddsMap = [
                    'over 1.5' => 'over15',  'under 1.5' => 'under15',
                    'over 2.5' => 'over25',  'under 2.5' => 'under25',
                    'over 3.5' => 'over35',  'under 3.5' => 'under35',
                    'over 4.5' => 'over45',  'under 4.5' => 'under45',
                ];
                foreach ($values as $v) {
                    $val = strtolower(trim($v['value'] ?? ''));
                    if (isset($oddsMap[$val])) {
                        $odds[$oddsMap[$val]] = (float) $v['odd'];
                    }
                }
            }

            // Double Chance
            if (str_contains($name, 'double chance')) {
                foreach ($values as $v) {
                    $val = strtolower(trim($v['value'] ?? ''));
                    if (str_contains($val, 'home/draw') || $val === '1x') $odds['dc_home_draw'] = (float) $v['odd'];
                    if (str_contains($val, 'draw/away') || $val === 'x2') $odds['dc_away_draw'] = (float) $v['odd'];
                    if (str_contains($val, 'home/away') || $val === '12') $odds['dc_home_away'] = (float) $v['odd'];
                }
            }
        }

        return $odds;
    }

    // ══════════════════════════════════════════════════════════════════
    //  Full match data bundle (used by MatchAnalysisService)
    // ══════════════════════════════════════════════════════════════════

    public function assembleMatchData(int $fixtureId): array
    {
        $fix      = $this->getFixture($fixtureId);
        $homeId   = $fix['teams']['home']['id'];
        $awayId   = $fix['teams']['away']['id'];
        $leagueId = $fix['league']['id'];
        $season   = $fix['league']['season'];

        $standings = $this->getStandings($leagueId, $season);
        $homeStats = $this->extractStandingStats($standings, $homeId, 'home');
        $awayStats = $this->extractStandingStats($standings, $awayId, 'away');

        $homeTeamStats = $this->getTeamStats($homeId, $leagueId, $season);
        $awayTeamStats = $this->getTeamStats($awayId, $leagueId, $season);

        $this->enrichStatsFromTeamStats($homeStats, $homeTeamStats, 'home');
        $this->enrichStatsFromTeamStats($awayStats, $awayTeamStats, 'away');

        $odds        = $this->getOdds($fixtureId);
        $predictions = $this->getPredictions($fixtureId);
        $lineups     = $this->getLineups($fixtureId);

        return [
            'fixture_id'      => $fixtureId,
            'home_team'       => $fix['teams']['home']['name'],
            'away_team'       => $fix['teams']['away']['name'],
            'home_logo'       => $fix['teams']['home']['logo'],
            'away_logo'       => $fix['teams']['away']['logo'],
            'home_team_id'    => $homeId,
            'away_team_id'    => $awayId,
            'league_id'       => $leagueId,
            'competition'     => $fix['league']['name'],
            'country'         => $fix['league']['country'],
            'round'           => $fix['league']['round']        ?? null,
            'match_date'      => $fix['fixture']['date'],
            'venue'           => $fix['fixture']['venue']['name'] ?? '',
            'venue_city'      => $fix['fixture']['venue']['city'] ?? null,
            'referee'         => $fix['fixture']['referee']       ?? null,
            'season'          => $season,
            'h2h'             => $this->getH2H($homeId, $awayId),
            'home_form'       => $this->getForm($homeId, $season),
            'away_form'       => $this->getForm($awayId, $season),
            'home_stats'      => $homeStats,
            'away_stats'      => $awayStats,
            'odds'            => $odds,
            'predictions'     => $predictions,
            'lineups'         => $lineups,
            'home_absences'   => $this->getInjuries($homeId, $fixtureId),
            'away_absences'   => $this->getInjuries($awayId, $fixtureId),
        ];
    }
}
