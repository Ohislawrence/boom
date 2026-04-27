<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Tip;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TipController extends Controller
{
    public function index(Request $request, GeoLocationService $geo)
    {
        $date     = $request->date ? $geo->localDate($request->date) : $geo->localDate()->addDay();
        $leagueId = $request->league;
        $minConf  = (int) ($request->confidence ?? 0);
        $range    = $geo->localDateRange($date->toDateString());

        $fixtures = Fixture::with([
                'league',
                'tips' => function ($q) use ($minConf) {
                    $q->published()
                      ->when($minConf > 0, fn ($q) => $q->where('confidence', '>=', $minConf))
                      ->orderByDesc('confidence');
                },
            ])
            ->whereHas('tips', function ($q) use ($minConf) {
                $q->published()
                  ->when($minConf > 0, fn ($q) => $q->where('confidence', '>=', $minConf));
            })
            ->whereBetween('match_date', [$range['start'], $range['end']])
            ->when($leagueId, fn ($q) => $q->where('league_id', $leagueId))
            ->orderBy('match_date')
            ->paginate(20);

        $leagues = League::active()->orderBy('name')->get();

        return view('frontend.tips.index', compact('fixtures', 'date', 'leagues'));
    }

    public function show(Tip $tip, GeoLocationService $geo)
    {
        abort_unless($tip->status === 'published', 404);

        $tip->load(['fixture.league.country', 'betMarket', 'tipResult', 'submittedBy']);

        $allMatchTips = Tip::with('betMarket')
            ->published()
            ->where('fixture_id', $tip->fixture_id)
            ->orderByDesc('confidence')
            ->get();

        $bookmakers = Bookmaker::active()->forCountry($geo->currentCountryCode())->take(4)->get();

        return view('frontend.tips.show', compact('tip', 'allMatchTips', 'bookmakers'));
    }
}
