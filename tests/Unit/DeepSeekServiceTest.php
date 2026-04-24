<?php

namespace Tests\Unit;

use App\Services\DeepSeekService;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class DeepSeekServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Fake HTTP for all tests
        Http::fake();
        // Optionally: set config for DeepSeek
        config(['services.deepseek' => [
            'key' => 'test-key',
            'url' => 'https://api.deepseek.com/v1/chat/completions',
            'model' => 'deepseek-chat',
            'confidence_threshold' => 75,
        ]]);
    }

    public function test_analyse_match_returns_filtered_and_sorted_markets()
    {
        // Arrange: fake DeepSeek API response
        $fakeResponse = [
            'choices' => [[
                'message' => [
                    'content' => json_encode([
                        [
                            'market' => 'Over/Under Goals',
                            'selection' => 'Over 2.5',
                            'confidence' => 80,
                            'odds' => 2.1,
                            'value_bet' => true,
                            'assessed_probability' => 78,
                            'implied_probability' => 47,
                            'signals' => ['form', 'stats'],
                            'reasoning' => 'Strong attacking form.'
                        ],
                        [
                            'market' => 'BTTS',
                            'selection' => 'Yes',
                            'confidence' => 70,
                            'odds' => 1.8,
                            'value_bet' => false,
                            'assessed_probability' => 68,
                            'implied_probability' => 55,
                            'signals' => ['recent matches'],
                            'reasoning' => 'Both teams score often.'
                        ]
                    ])
                ]
            ]]
        ];
        Http::fake([
            'api.deepseek.com/*' => Http::response($fakeResponse, 200)
        ]);

        $service = app(DeepSeekService::class);
        $matchData = [
            'home_team' => 'Team A',
            'away_team' => 'Team B',
            'competition' => 'Premier League',
            'match_date' => '2026-04-24',
            'venue' => 'Stadium',
            'season' => '2025/26',
            // ...other keys as needed
        ];

        // Act
        $markets = $service->analyseMatch($matchData);

        // Assert
        $this->assertCount(1, $markets, 'Only markets above threshold should be returned');
        $this->assertEquals('Over/Under Goals', $markets[0]['market']);
        $this->assertGreaterThanOrEqual(75, $markets[0]['confidence']);
    }
}
