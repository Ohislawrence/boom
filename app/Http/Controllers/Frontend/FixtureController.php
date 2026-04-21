<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Tip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        // Fixtures that have at least one published tip on the selected date
        $fixtures = Fixture::with(['league.country', 'tips' => fn ($q) => $q->published()->orderByDesc('confidence')])
            ->whereDate('match_date', $date)
            ->whereHas('tips', fn ($q) => $q->published())
            ->orderBy('match_date')
            ->get()
            ->groupBy('league_id');

        $bookmakers = Bookmaker::active()->take(4)->get();

        return view('frontend.fixtures.index', compact('fixtures', 'date', 'bookmakers'));
    }

    public function bettingTips(Fixture $fixture)
    {
        $fixture->load(['league.country']);

        $tips = Tip::published()
            ->where('fixture_id', $fixture->id)
            ->orderByDesc('confidence')
            ->get();

        $bookmakers = Bookmaker::active()->take(4)->get();

        return view('frontend.fixtures.betting-tips', compact('fixture', 'tips', 'bookmakers'));
    }
}
