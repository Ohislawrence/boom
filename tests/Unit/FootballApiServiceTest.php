<?php

namespace Tests\Unit;

use App\Services\FootballApiService;
use Tests\TestCase;

class FootballApiServiceTest extends TestCase
{
    public function test_get_fixtures_by_date_adds_season_for_each_league()
    {
        $service = new class extends FootballApiService {
            public array $calls = [];

            public function __construct()
            {
            }

            protected function get(string $endpoint, array $params = []): array
            {
                $this->calls[] = compact('endpoint', 'params');
                return [];
            }
        };

        $service->getFixturesByDate('2026-04-30', [100, 200], [100 => 2025, 200 => 2026]);

        $this->assertCount(2, $service->calls);
        $this->assertSame('/fixtures', $service->calls[0]['endpoint']);
        $this->assertSame(['date' => '2026-04-30', 'league' => 100, 'status' => 'NS', 'season' => 2025], $service->calls[0]['params']);
        $this->assertSame('/fixtures', $service->calls[1]['endpoint']);
        $this->assertSame(['date' => '2026-04-30', 'league' => 200, 'status' => 'NS', 'season' => 2026], $service->calls[1]['params']);
    }
}
