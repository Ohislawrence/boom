<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\VirtualGame;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function howItWorks()
    {
        return view('frontend.pages.how-it-works');
    }

    public function editorialPolicy()
    {
        return view('frontend.pages.editorial-policy');
    }

    public function privacyNotice()
    {
        return view('frontend.pages.privacy-notice');
    }

    public function responsibleGambling()
    {
        return view('frontend.pages.responsible-gambling');
    }

    public function virtualGames()
    {
        $games = VirtualGame::active()
            ->orderBy('sort_order')
            ->get()
            ->map(function (VirtualGame $game) {
                return array_merge($game->toArray(), ['script_url' => $game->script_url]);
            });

        if ($games->isEmpty()) {
            $games = collect($this->sampleGames());
        }

        $popularGames = $games->slice(0, 4)->all();
        $newGames = $games->slice(4, 3)->all();

        return view('frontend.pages.virtual-games', compact('games', 'popularGames', 'newGames'));
    }

    public function virtualGame(string $slug)
    {
        $game = VirtualGame::active()->where('slug', $slug)->first();

        if ($game) {
            $game = array_merge($game->toArray(), ['script_url' => $game->script_url]);
            $related = VirtualGame::active()
                ->where('slug', '!=', $slug)
                ->orderBy('sort_order')
                ->take(4)
                ->get()
                ->map(function (VirtualGame $item) {
                    return array_merge($item->toArray(), ['script_url' => $item->script_url]);
                })
                ->all();
        } else {
            $games = collect($this->sampleGames());
            $game = $games->firstWhere('slug', $slug);
            if (!$game) {
                abort(404);
            }
            $related = $games->reject(fn ($item) => $item['slug'] === $slug)->take(4)->all();
        }

        return view('frontend.pages.virtual-game', compact('game', 'related'));
    }

    private function sampleGames(): array
    {
        return [
            [
                'slug' => 'sample-arcade',
                'name' => 'Sample Arcade',
                'tagline' => 'A simple sample game to demonstrate canvas play.',
                'provider' => 'Built-in Demo',
                'volatility' => 'Low',
                'rtp' => '100%',
                'icon' => '🕹️',
                'color' => 'linear-gradient(135deg, #2563eb, #14b8a6)',
                'description' => 'Collect the stars and avoid the red blocks. This built-in sample demonstrates the virtual game canvas and game loader.',
                'features' => ['Keyboard controls', 'Score tracking', 'Simple collision', 'Built-in demo asset'],
                'script_path' => null,
                'script_url' => asset('js/virtual-games/sample-arcade.js'),
            ],
        ];
    }
}
