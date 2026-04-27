<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Tip;
use App\Services\GeoLocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    public function index(Request $request, GeoLocationService $geo)
    {
        $date = $request->filled('date')
            ? $geo->localDate($request->date)
            : $geo->localDate();

        $range = $geo->localDateRange($date->toDateString());

        $fixtures = Fixture::with(['league.country', 'tips' => fn ($q) => $q->published()->orderByDesc('confidence')])
            ->whereBetween('match_date', [$range['start'], $range['end']])
            ->whereHas('tips', fn ($q) => $q->published())
            ->orderBy('match_date')
            ->get()
            ->groupBy('league_id');

        $bookmakers = Bookmaker::active()->forCountry($geo->currentCountryCode())->take(4)->get();

        return view('frontend.fixtures.index', compact('fixtures', 'date', 'bookmakers'));
    }

    public function bettingTips(Fixture $fixture, GeoLocationService $geo)
    {
        $fixture->load(['league.country']);

        $tips = Tip::published()
            ->where('fixture_id', $fixture->id)
            ->orderByDesc('confidence')
            ->get();

        $bookmakers = Bookmaker::active()->forCountry($geo->currentCountryCode())->take(4)->get();

        return view('frontend.fixtures.betting-tips', compact('fixture', 'tips', 'bookmakers'));
    }
}
