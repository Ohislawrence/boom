<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\League;
use App\Models\Tip;
use App\Services\DeepSeekService;
use App\Services\FootballApiService;
use App\Services\MatchAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_daily_batch_fetch_only_stores_fixtures()
    {
        $league = League::create([
            'api_football_id' => 1,
            'name'            => 'Test League',
            'country'         => 'Testland',
            'season'          => 2025,
            'is_active'       => true,
        ]);

        $footballApi = new class extends FootballApiService {
            public function __construct() {}

            public function getFixturesByDate(string $date, array $leagueIds = [], array $leagueSeasons = []): array
            {
                return [
                    [
                        'league' => [
                            'id'      => 1,
                            'name'    => 'Test League',
                            'country' => 'Testland',
                            'logo'    => null,
                            'season'  => 2025,
                            'round'   => '1',
                        ],
                        'fixture' => [
                            'id'     => 100,
                            'date'   => '2026-04-30 15:00:00',
                            'venue'  => ['name' => 'Test Stadium', 'city' => 'Test City'],
                            'referee'=> 'Referee',
                            'status' => ['short' => 'NS'],
                        ],
                        'teams' => [
                            'home' => ['id' => 10, 'name' => 'Team A', 'logo' => 'home.svg'],
                            'away' => ['id' => 20, 'name' => 'Team B', 'logo' => 'away.svg'],
                        ],
                        'score' => ['halftime' => ['home' => null, 'away' => null]],
                    ],
                ];
            }
        };

        $deepSeek = new class extends DeepSeekService {
            public function __construct() {}
            protected function callApi(string $systemPrompt, string $userPrompt): string
            {
                return '[]';
            }
        };

        $service = new MatchAnalysisService($footballApi, $deepSeek);
        $result  = $service->runDailyBatch('2026-04-30', fetchOnly: true);

        $this->assertSame(1, $result['fixtures']);
        $this->assertSame(0, $result['tips']);
        $this->assertSame(0, $result['errors']);
        $this->assertDatabaseCount('fixtures', 1);
        $this->assertDatabaseHas('fixtures', [
            'api_football_id' => 100,
            'home_team'       => 'Team A',
            'away_team'       => 'Team B',
        ]);
    }

    public function test_run_daily_batch_analyse_only_analyses_existing_fixtures()
    {
        $league = League::create([
            'api_football_id' => 1,
            'name'            => 'Test League',
            'country'         => 'Testland',
            'season'          => 2025,
            'is_active'       => true,
        ]);

        $fixture = Fixture::create([
            'api_football_id'  => 100,
            'league_id'        => $league->id,
            'home_team'        => 'Team A',
            'away_team'        => 'Team B',
            'home_team_api_id' => 10,
            'away_team_api_id' => 20,
            'home_logo'        => 'home.svg',
            'away_logo'        => 'away.svg',
            'match_date'       => '2026-04-30 15:00:00',
            'venue'            => 'Test Stadium',
            'season'           => 2025,
            'status'           => 'NS',
        ]);

        $footballApi = new class extends FootballApiService {
            public function __construct() {}

            public function assembleMatchData(int $fixtureId): array
            {
                return [
                    'home_team'   => 'Team A',
                    'away_team'   => 'Team B',
                    'competition' => 'Test League',
                    'match_date'  => '2026-04-30 15:00:00',
                    'venue'       => 'Test Stadium',
                    'season'      => '2025/26',
                    'odds'        => [],
                    'predictions' => [],
                ];
            }
        };

        $deepSeek = new class extends DeepSeekService {
            public function __construct() {}

            public function analyseMatch(array $matchData): array
            {
                return [
                    [
                        'market'         => 'Over/Under Goals',
                        'selection'      => 'Over 2.5',
                        'confidence'     => 80,
                        'odds'           => 2.1,
                        'value_bet'      => true,
                        'reasoning'      => 'Strong attacking form.',
                        'assessed_probability' => 78,
                        'implied_probability' => 47,
                        'signals'        => ['form', 'stats'],
                    ],
                ];
            }

            protected function callApi(string $systemPrompt, string $userPrompt): string
            {
                return '[]';
            }
        };

        config(['cache.default' => 'file']);

        $service = new MatchAnalysisService($footballApi, $deepSeek);
        $result  = $service->runDailyBatch('2026-04-30', fetchOnly: false, analyseOnly: true, force: true);

        $this->assertSame(1, $result['fixtures']);
        $this->assertSame(1, $result['tips']);
        $this->assertSame(0, $result['errors']);
        $this->assertDatabaseCount('tips', 1);
        $this->assertDatabaseHas('tips', ['market' => 'Over/Under Goals']);
        $this->assertNotNull(Fixture::first()->analysis_run_at);
    }
}
