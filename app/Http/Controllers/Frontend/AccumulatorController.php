<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BetMarket;
use App\Models\League;
use App\Models\Tip;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;

class AccumulatorController extends Controller
{
    public function index(Request $request, GeoLocationService $geo)
    {
        $date = $request->filled('date')
            ? $geo->localDate($request->date)
            : $geo->localDate()->addDay();

        $range = $geo->localDateRange($date->toDateString());

        $query = Tip::published()
            ->with(['fixture.league', 'betMarket'])
            ->whereHas('fixture', fn ($q) => $q->whereBetween('match_date', [$range['start'], $range['end']]));

        if ($request->filled('market')) {
            $query->where('bet_market_id', $request->market);
        }

        if ($request->filled('league')) {
            $query->whereHas('fixture', fn ($q) => $q->where('league_id', $request->league));
        }

        if ($request->filled('country')) {
            $query->whereHas('fixture.league', fn ($q) => $q->where('country', $request->country));
        }

        if ($request->filled('min_confidence')) {
            $query->where('confidence', '>=', (int) $request->min_confidence);
        }

        $tips = $query->orderByDesc('confidence')->get();

        // Filter options — only leagues that have published tips on the chosen date
        $markets = BetMarket::active()->get();

        $availableLeagues = League::whereHas('fixtures', fn ($q) =>
            $q->whereBetween('match_date', [$range['start'], $range['end']])
              ->whereHas('tips', fn ($q2) => $q2->published())
        )->orderBy('country')->orderBy('name')->get();

        $countries = $availableLeagues->pluck('country')->unique()->filter()->sort()->values();

        return view('frontend.accumulator.index', compact(
            'tips', 'date', 'markets', 'availableLeagues', 'countries'
        ));
    }
}
