<?php

namespace Tests\Unit;


use App\Services\DeepSeekService;
use PHPUnit\Framework\TestCase;


class DeepSeekServiceTest extends TestCase
{
    // Test double to override callApi
    private function makeServiceWithFakeApi(string $fakeContent): DeepSeekService
    {
        // Anonymous class extending DeepSeekService
        return new class($fakeContent) extends DeepSeekService {
            private $fakeContent;
            public function __construct($fakeContent) {
                // Do NOT call parent constructor (bypasses config())
                $this->fakeContent = $fakeContent;
                $this->apiKey = 'test-key';
                $this->apiUrl = 'https://api.deepseek.com/v1/chat/completions';
                $this->model = 'deepseek-chat';
                $this->confidenceThreshold = 75;
            }
            // Override callApi to return fake content
            protected function callApi(string $systemPrompt, string $userPrompt): string {
                return $this->fakeContent;
            }
        };
    }

    public function test_analyse_match_returns_filtered_and_sorted_markets()
    {
        // Arrange: fake DeepSeek API response as JSON string
        $fakeContent = json_encode([
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
        ]);

        $service = $this->makeServiceWithFakeApi($fakeContent);
        $matchData = [
            'home_team' => 'Team A',
            'away_team' => 'Team B',
            'competition' => 'Premier League',
            'match_date' => '2026-04-24',
            'venue' => 'Stadium',
            'season' => '2025/26',
        ];

        // Act
        $markets = $service->analyseMatch($matchData);

        // Assert
        $this->assertCount(1, $markets, 'Only markets above threshold should be returned');
        $this->assertEquals('Over/Under Goals', $markets[0]['market']);
        $this->assertGreaterThanOrEqual(75, $markets[0]['confidence']);
    }
}
