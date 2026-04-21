<?php

namespace Database\Seeders;

use App\Models\BetMarket;
use Illuminate\Database\Seeder;

class BetMarketSeeder extends Seeder
{
    public function run(): void
    {
        $markets = [
            // Result
            ['name' => 'Match Winner (1X2)',    'slug' => '1x2',              'category' => 'Result',   'sort_order' => 1,  'description' => 'Predict whether the home team wins (1), the match ends in a draw (X), or the away team wins (2).'],
            ['name' => 'Double Chance',          'slug' => 'double-chance',    'category' => 'Result',   'sort_order' => 2,  'description' => 'Cover two of the three possible outcomes: 1X (home or draw), X2 (draw or away), or 12 (home or away).'],
            ['name' => 'Draw No Bet',            'slug' => 'draw-no-bet',      'category' => 'Result',   'sort_order' => 3,  'description' => 'Bet on either team to win — stake is refunded if the match ends in a draw.'],
            ['name' => 'Half Time Result',       'slug' => 'ht-result',        'category' => 'Result',   'sort_order' => 4,  'description' => 'Predict the result at the end of the first half.'],
            ['name' => 'Half Time / Full Time',  'slug' => 'ht-ft',            'category' => 'Result',   'sort_order' => 5,  'description' => 'Predict both the half-time and full-time results in a single bet.'],

            // Goals
            ['name' => 'Over/Under 1.5 Goals',  'slug' => 'over-under-1-5',   'category' => 'Goals',    'sort_order' => 10, 'description' => 'Bet on whether the total goals in the match will be over or under 1.5.'],
            ['name' => 'Over/Under 2.5 Goals',  'slug' => 'over-under-2-5',   'category' => 'Goals',    'sort_order' => 11, 'description' => 'Bet on whether the total goals in the match will be over or under 2.5.'],
            ['name' => 'Over/Under 3.5 Goals',  'slug' => 'over-under-3-5',   'category' => 'Goals',    'sort_order' => 12, 'description' => 'Bet on whether the total goals in the match will be over or under 3.5.'],
            ['name' => 'Both Teams to Score',   'slug' => 'btts',             'category' => 'Goals',    'sort_order' => 13, 'description' => 'Predict whether both teams will score at least one goal each during the match.'],
            ['name' => 'Correct Score',         'slug' => 'correct-score',    'category' => 'Goals',    'sort_order' => 14, 'description' => 'Predict the exact final score of the match.'],
            ['name' => 'First Team to Score',   'slug' => 'first-team-score', 'category' => 'Goals',    'sort_order' => 15, 'description' => 'Predict which team will score the first goal of the match.'],

            // Handicap
            ['name' => 'Asian Handicap',        'slug' => 'asian-handicap',   'category' => 'Handicap', 'sort_order' => 20, 'description' => 'Level the playing field by applying a goal handicap to both teams before the match starts.'],
            ['name' => 'European Handicap',     'slug' => 'euro-handicap',    'category' => 'Handicap', 'sort_order' => 21, 'description' => 'Similar to Asian handicap but with three possible outcomes including the draw.'],

            // Player
            ['name' => 'Anytime Goal Scorer',   'slug' => 'anytime-scorer',   'category' => 'Player',   'sort_order' => 30, 'description' => 'Bet on a player to score at any point during the match.'],
            ['name' => 'First Goal Scorer',     'slug' => 'first-scorer',     'category' => 'Player',   'sort_order' => 31, 'description' => 'Bet on a player to score the very first goal of the match.'],

            // Specials
            ['name' => 'Total Corners',         'slug' => 'total-corners',    'category' => 'Specials', 'sort_order' => 40, 'description' => 'Bet on the total number of corners taken during the match.'],
            ['name' => 'Total Cards',           'slug' => 'total-cards',      'category' => 'Specials', 'sort_order' => 41, 'description' => 'Bet on the total number of yellow and red cards shown during the match.'],
            ['name' => 'Clean Sheet',           'slug' => 'clean-sheet',      'category' => 'Specials', 'sort_order' => 42, 'description' => 'Bet on whether a team will keep a clean sheet (concede no goals).'],
            ['name' => 'Win to Nil',            'slug' => 'win-to-nil',       'category' => 'Specials', 'sort_order' => 43, 'description' => 'Bet on a team to win the match without conceding any goals.'],
        ];

        foreach ($markets as $data) {
            BetMarket::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
