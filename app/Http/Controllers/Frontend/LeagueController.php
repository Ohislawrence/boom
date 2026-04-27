<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use App\Services\GeoLocationService;
use Illuminate\Support\Carbon;

class LeagueController extends Controller
{
    public function show(League $league, GeoLocationService $geo)
    {
        $league->load(['country']);

        $date = request('date') ? $geo->localDate(request('date')) : $geo->localDate()->addDay();
        $range = $geo->localDateRange($date->toDateString());

        $fixtures = Fixture::with([
                'tips' => fn ($q) => $q->published()->orderByDesc('confidence'),
            ])
            ->where('league_id', $league->id)
            ->whereBetween('match_date', [$range['start'], $range['end']])
            ->orderBy('match_date')
            ->get();

        $bookmakers = Bookmaker::active()->forCountry($geo->currentCountryCode())->take(4)->get();

        return view('frontend.leagues.show', compact('league', 'fixtures', 'date', 'bookmakers'));
    }
}
