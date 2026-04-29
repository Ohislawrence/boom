<?php

namespace App\Services;

use App\Models\BetMarket;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Tip;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

class MatchAnalysisService
{
    public function __construct(
        private readonly FootballApiService $footballApi,
        private readonly DeepSeekService    $deepSeek,
    ) {}

    // ══════════════════════════════════════════════════════════════════
    //  Analyse a single fixture and persist tips
    // ══════════════════════════════════════════════════════════════════

    /**
     * Run full analysis for one fixture.
     *
     * @return array{tips_saved: int, fixture_id: int, markets: array, skipped: bool}
     */
    public function analyseFixture(Fixture $fixture): array
    {
        $matchData = $this->footballApi->assembleMatchData($fixture->api_football_id);

        // Flatten odds/predictions into dedicated columns for fast reads
        $odds  = $matchData['odds']        ?? [];
        $pred  = $matchData['predictions'] ?? [];

        // Update fixture with fresh raw data and extracted columns
        $fixture->update([
            'raw_data'                  => $matchData,
            'analysis_run_at'           => now(),
            'referee'                   => $matchData['referee']    ?? $fixture->referee,
            'venue_city'                => $matchData['venue_city'] ?? $fixture->venue_city,
            'round'                     => $matchData['round']      ?? $fixture->round,
            'home_odds'                 => $odds['home_win']        ?? null,
            'draw_odds'                 => $odds['draw']            ?? null,
            'away_odds'                 => $odds['away_win']        ?? null,
            'over25_odds'               => $odds['over25']          ?? null,
            'under25_odds'              => $odds['under25']         ?? null,
            'btts_yes_odds'             => $odds['btts_yes']        ?? null,
            'btts_no_odds'              => $odds['btts_no']         ?? null,
            'prediction_winner'         => $pred['winner']          ?? null,
            'prediction_percent_home'   => $pred['percent_home']    ?? null,
            'prediction_percent_draw'   => $pred['percent_draw']    ?? null,
            'prediction_percent_away'   => $pred['percent_away']    ?? null,
            'prediction_advice'         => $pred['advice']          ?? null,
            'prediction_under_over'     => $pred['under_over']      ?? null,
        ]);


        $markets = $this->deepSeek->analyseMatch($matchData);

        if (empty($markets)) {
            return ['tips_saved' => 0, 'fixture_id' => $fixture->id, 'markets' => [], 'skipped' => false];
        }

        $required = ['market', 'selection', 'confidence'];
        $markets = array_values(array_filter($markets, fn ($m) =>
            count(array_intersect_key(array_flip($required), $m)) === count($required)
            && is_numeric($m['confidence'])
        ));

        if (empty($markets)) {
            return ['tips_saved' => 0, 'fixture_id' => $fixture->id, 'markets' => [], 'skipped' => false];
        }

        usort($markets, fn ($a, $b) => ($b['confidence'] ?? 0) <=> ($a['confidence'] ?? 0));
        $markets = array_slice($markets, 0, 3);

        $saved    = 0;
        $savedIds = [];

        DB::transaction(function () use ($fixture, $markets, &$saved, &$savedIds) {
            // Delete stale AI tips before re-inserting — never overwrite settled tips
            Tip::where('fixture_id', $fixture->id)
                ->where('is_ai_generated', true)
                ->where('result', 'pending')
                ->delete();

            foreach ($markets as $market) {
                $betMarket = $this->resolveBetMarket($market['market'] ?? '');

                $tip = Tip::create([
                    'fixture_id'      => $fixture->id,
                    'bet_market_id'   => $betMarket?->id,
                    'submitted_by'    => null,
                    'market'          => $market['market']     ?? 'Unknown',
                    'selection'       => $market['selection']  ?? '',
                    'odds'            => is_numeric($market['odds']) ? (float) $market['odds'] : null,
                    'confidence'      => (int) $market['confidence'],
                    'is_value_bet'    => (bool) ($market['value_bet'] ?? false),
                    'is_ai_generated' => true,
                    'reasoning'       => $market['reasoning']  ?? null,
                    'status'          => 'published',
                    'result'          => 'pending',
                ]);

                $savedIds[] = $tip->id;
                $saved++;
            }
        });

        Log::info('MatchAnalysisService: tips saved', [
            'fixture_id' => $fixture->id,
            'tip_ids'    => $savedIds,
            'count'      => $saved,
        ]);

        return [
            'tips_saved' => $saved,
            'fixture_id' => $fixture->id,
            'markets'    => $markets,
            'skipped'    => false,
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Upsert fixture from API-Football raw data
    // ══════════════════════════════════════════════════════════════════

    /**
     * Persist or update a fixture from a raw API-Football fixture response.
     * Returns the Fixture model.
     */
    public function upsertFixture(array $rawFixture): Fixture
    {
        // Auto-create the league if it doesn't exist yet; never overwrite admin-managed fields
        $league = League::firstOrCreate(
            ['api_football_id' => $rawFixture['league']['id']],
            [
                'name'      => $rawFixture['league']['name'],
                'country'   => $rawFixture['league']['country'],
                'logo_url'  => $rawFixture['league']['logo'] ?? null,
                'season'    => $rawFixture['league']['season'],
                'is_active' => false, // admin must activate to include in analysis
            ]
        );

        return Fixture::updateOrCreate(
            ['api_football_id' => $rawFixture['fixture']['id']],
            [
                'league_id'        => $league?->id,
                'home_team'        => $rawFixture['teams']['home']['name'],
                'away_team'        => $rawFixture['teams']['away']['name'],
                'home_team_api_id' => $rawFixture['teams']['home']['id'],
                'away_team_api_id' => $rawFixture['teams']['away']['id'],
                'home_logo'        => $rawFixture['teams']['home']['logo'],
                'away_logo'        => $rawFixture['teams']['away']['logo'],
                'match_date'       => $rawFixture['fixture']['date'],
                'venue'            => $rawFixture['fixture']['venue']['name'] ?? null,
                'venue_city'       => $rawFixture['fixture']['venue']['city'] ?? null,
                'referee'          => $rawFixture['fixture']['referee'] ?? null,
                'round'            => $rawFixture['league']['round'] ?? null,
                'season'           => $rawFixture['league']['season'],
                'status'           => $rawFixture['fixture']['status']['short'] ?? 'NS',
                // Halftime scores (populated when match is finished)
                'halftime_home'    => $rawFixture['score']['halftime']['home'] ?? null,
                'halftime_away'    => $rawFixture['score']['halftime']['away'] ?? null,
            ]
        );
    }

    // ══════════════════════════════════════════════════════════════════
    //  Daily batch — called by the scheduler command
    // ══════════════════════════════════════════════════════════════════

    /**
     * Fetch all fixtures for a date, upsert them, and optionally analyse each.
     *
     * @param  string  $date         Y-m-d format; defaults to tomorrow
     * @param  bool    $fetchOnly    Upsert fixtures but skip AI analysis
     * @param  bool    $analyseOnly  Skip API fetch — analyse fixtures already stored in DB
     * @return array{fixtures: int, tips: int, errors: int}
     */
    public function runDailyBatch(string $date = '', bool $fetchOnly = false, bool $analyseOnly = false, bool $force = false): array
    {
        $date        = $date ?: now()->addDay()->toDateString();
        $activeLeagues = League::active()->get(['api_football_id', 'season']);
        $leagues       = $activeLeagues->pluck('api_football_id')->all();
        $leagueSeasons = $activeLeagues->pluck('season', 'api_football_id')->all();

        $fixtureCount = 0;
        $tipCount     = 0;
        $errorCount   = 0;

        if ($analyseOnly) {
            // Load fixtures for this date, skipping already-analysed ones (unless forced)
            $fixtures = Fixture::whereDate('match_date', $date)
                ->when(!$force, function ($q) use ($date) {
                    $q->where(function ($inner) use ($date) {
                        $inner->whereNull('analysis_run_at')
                              ->orWhereDate('analysis_run_at', '!=', $date);
                    })->orWhereDoesntHave('tips', fn ($t) => $t->where('is_ai_generated', true));
                })
                ->get();

            foreach ($fixtures as $index => $fixture) {
                try {
                    if ($index > 0) {
                        usleep($this->getAnalysisBackoffMicroseconds($index));
                    }

                    $result = Cache::lock("analysis:{$fixture->id}", 120)->get(function () use ($fixture, $date) {
                        // Re-check the fixture inside the lock to avoid overlapping workers.
                        if ($fixture->analysis_run_at?->toDateString() === $date) {
                            return null;
                        }
                        return $this->analyseFixture($fixture);
                    });

                    if ($result === false) {
                        Log::info('MatchAnalysisService: fixture analysis lock busy, skipping', ['fixture_id' => $fixture->id]);
                        continue;
                    }

                    if ($result !== null) {
                        $fixtureCount++;
                        $tipCount += $result['tips_saved'];
                    }
                } catch (Throwable $e) {
                    $errorCount++;
                    Log::error('MatchAnalysisService: analyse-only failed', [
                        'fixture_id' => $fixture->id,
                        'error'      => $e->getMessage(),
                    ]);
                }
            }

            return ['fixtures' => $fixtureCount, 'tips' => $tipCount, 'errors' => $errorCount];
        }

        // Fetch from API-Football
        $rawFixtures = $this->footballApi->getFixturesByDate($date, $leagues, $leagueSeasons);

        foreach ($rawFixtures as $index => $raw) {
            $fixture = null; // reset per iteration — prevents stale reference in catch block
            try {
                $fixture = $this->upsertFixture($raw);
                $fixtureCount++;

                if ($fetchOnly) {
                    continue; // stop here — just store the fixture
                }

                // Skip if already analysed for this target date (unless forced)
                $result = Cache::lock("analysis:{$fixture->id}", 120)->get(function () use ($fixture, $force, $date) {
                    if (!$force && $fixture->analysis_run_at?->toDateString() === $date) {
                        return null;
                    }
                    return $this->analyseFixture($fixture);
                });

                if ($result === false) {
                    Log::info('MatchAnalysisService: fixture analysis lock busy, skipping', ['fixture_id' => $fixture->id]);
                    continue;
                }

                if ($result !== null) {
                    if ($index > 0) {
                        usleep($this->getAnalysisBackoffMicroseconds($index));
                    }

                    $tipCount += $result['tips_saved'];
                }

            } catch (\Illuminate\Http\Client\RequestException $e) {
                // HTTP 429 — back off and retry once, but only if we have a fixture to analyse
                if ($e->response->status() === 429) {
                    Log::warning('MatchAnalysisService: rate limited — backing off 30s', ['index' => $index]);
                    sleep(30);
                    if ($fixture !== null) {
                        try {
                            $result    = $this->analyseFixture($fixture);
                            $tipCount += $result['tips_saved'];
                        } catch (Throwable $retry) {
                            $errorCount++;
                            Log::error('MatchAnalysisService: retry failed after 429', [
                                'error' => $retry->getMessage(),
                            ]);
                        }
                    } else {
                        // 429 hit during upsertFixture itself — fixture was never stored
                        $errorCount++;
                        Log::error('MatchAnalysisService: 429 during upsert, fixture not stored', [
                            'fixture_api_id' => $raw['fixture']['id'] ?? null,
                        ]);
                    }
                } else {
                    $errorCount++;
                    Log::error('MatchAnalysisService: fixture analysis failed', [
                        'fixture_api_id' => $raw['fixture']['id'] ?? null,
                        'error'          => $e->getMessage(),
                    ]);
                }
            } catch (Throwable $e) {
                $errorCount++;
                Log::error('MatchAnalysisService: fixture analysis failed', [
                    'fixture_api_id' => $raw['fixture']['id'] ?? null,
                    'error'          => $e->getMessage(),
                ]);
            }
        }

        return [
            'fixtures' => $fixtureCount,
            'tips'     => $tipCount,
            'errors'   => $errorCount,
        ];
    }

    private function getAnalysisBackoffMicroseconds(int $iteration): int
    {
        $maxPower = 5; // cap at 8 seconds
        $power = min(max($iteration, 1), $maxPower);
        $base   = 500_000; // 0.5 seconds
        $delay  = $base * (1 << ($power - 1));
        $jitter = random_int(0, 100_000);

        return $delay + $jitter;
    }

    // ══════════════════════════════════════════════════════════════════
    //  Helpers
    // ══════════════════════════════════════════════════════════════════

    /**
     * Attempt to resolve a BetMarket row from the AI-returned market string.
     * 1. Exact slug match
     * 2. Exact case-insensitive name match
     * 3. Partial LIKE fallback (logs a warning on miss so missing markets can be seeded)
     */
    /**
     * Map of AI-generated market name variants → canonical BetMarket slug.
     * Keys are lowercased; values are the slug in the bet_markets table.
     */
    private const MARKET_ALIASES = [
        // 1X2 variants
        '1x2'                          => '1x2',
        'match winner'                 => '1x2',
        'match winner (1x2)'           => '1x2',
        'match result'                 => '1x2',
        'full time result'             => '1x2',
        'full-time result'             => '1x2',
        'win/draw/win'                 => '1x2',
        '1x2 / double chance'          => '1x2',
        // Double Chance
        'double chance'                => 'double-chance',
        // Draw No Bet
        'draw no bet'                  => 'draw-no-bet',
        'dnb'                          => 'draw-no-bet',
        // BTTS
        'btts'                         => 'btts',
        'both teams to score'          => 'btts',
        'gg'                           => 'btts',
        'gg/ng'                        => 'btts',
        // Over/Under — generic defaults to 2.5
        'over/under goals'             => 'over-under-2-5',
        'total goals'                  => 'over-under-2-5',
        'over/under'                   => 'over-under-2-5',
        'goals over/under'             => 'over-under-2-5',
        // Specific thresholds
        'over/under 1.5'               => 'over-under-1-5',
        'over/under 1.5 goals'         => 'over-under-1-5',
        'over/under 2.5'               => 'over-under-2-5',
        'over/under 2.5 goals'         => 'over-under-2-5',
        'over/under 3.5'               => 'over-under-3-5',
        'over/under 3.5 goals'         => 'over-under-3-5',
        // Asian Handicap
        'asian handicap'               => 'asian-handicap',
        'ah'                           => 'asian-handicap',
        'european handicap'            => 'euro-handicap',
        'euro handicap'                => 'euro-handicap',
        // HT/FT
        'half time / full time'        => 'ht-ft',
        'ht/ft'                        => 'ht-ft',
        'half time result'             => 'ht-result',
        'half-time result'             => 'ht-result',
        // Correct Score
        'correct score'                => 'correct-score',
        'exact score'                  => 'correct-score',
        // Scorers
        'anytime goalscorer'           => 'anytime-scorer',
        'anytime scorer'               => 'anytime-scorer',
        'anytime goal scorer'          => 'anytime-scorer',
        'first goalscorer'             => 'first-scorer',
        'first goal scorer'            => 'first-scorer',
        'first scorer'                 => 'first-scorer',
        'first team to score'          => 'first-team-score',
        // Specials
        'total corners'                => 'total-corners',
        'corners'                      => 'total-corners',
        'total cards'                  => 'total-cards',
        'cards'                        => 'total-cards',
        'clean sheet'                  => 'clean-sheet',
        'win to nil'                   => 'win-to-nil',
        'win and clean sheet'          => 'win-to-nil',
    ];

    private function resolveBetMarket(string $marketName): ?BetMarket
    {
        if (!$marketName) {
            return null;
        }

        // 0. Static alias map — catches common AI naming variants before any DB hit
        $normalized = strtolower(trim($marketName));
        if (isset(self::MARKET_ALIASES[$normalized])) {
            $market = BetMarket::where('slug', self::MARKET_ALIASES[$normalized])->first();
            if ($market) {
                return $market;
            }
        }

        $slug = \Illuminate\Support\Str::slug($marketName);

        // Exact slug match first
        $market = BetMarket::where('slug', $slug)->first();
        if ($market) {
            return $market;
        }

        // Exact name match (case-insensitive)
        $market = BetMarket::whereRaw('LOWER(name) = ?', [strtolower($marketName)])->first();
        if ($market) {
            return $market;
        }

        // Fuzzy fallback — only if the marketName is fully contained in the stored name
        $market = BetMarket::whereRaw(
            'LOWER(name) LIKE ?',
            ['%' . strtolower($marketName) . '%']
        )->first();

        if (!$market) {
            Log::warning('MatchAnalysisService: unresolved BetMarket', [
                'market_name' => $marketName,
                'slug'        => $slug,
            ]);
        }

        return $market;
    }

    /**
     * Auto-create BetMarket rows for any markets returned by the AI that are not yet seeded.
     * Safe to call after a batch run — uses firstOrCreate so it's idempotent.
     */
    public function seedMissingBetMarkets(array $markets): void
    {
        foreach ($markets as $market) {
            $name = $market['market'] ?? '';
            if (!$name) {
                continue;
            }

            $slug = \Illuminate\Support\Str::slug($name);

            BetMarket::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'category' => $this->inferCategory($name)]
            );
        }
    }

    private function inferCategory(string $name): string
    {
        return match (true) {
            str_contains($name, 'Goal') || str_contains($name, 'Over') || str_contains($name, 'Under') => 'goals',
            str_contains($name, 'BTTS') || str_contains($name, 'Both')  => 'goals',
            str_contains($name, 'Winner') || str_contains($name, '1X2') => 'result',
            str_contains($name, 'Handicap')                             => 'handicap',
            str_contains($name, 'Half')                                 => 'half-time',
            str_contains($name, 'Clean')                                => 'specials',
            str_contains($name, 'Scorer') || str_contains($name, 'First Team to Score') => 'player',
            str_contains($name, 'Corners') || str_contains($name, 'Cards') || str_contains($name, 'Win to Nil') => 'specials',
            default                                                      => 'other',
        };
    }
}
