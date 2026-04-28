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

        $todayLeagueIds = $fixtures->keys()->filter()->all();
        $nextDayRange = $geo->localDateRange($date->copy()->addDay()->toDateString());
        $nextWeekRange = $geo->localDateRange($date->copy()->addDays(7)->toDateString());

        $otherLeagues = League::with('country')
            ->whereHas('fixtures', function ($q) use ($todayLeagueIds, $nextDayRange, $nextWeekRange) {
                $q->whereHas('tips', fn ($t) => $t->published())
                  ->whereBetween('match_date', [$nextDayRange['start'], $nextWeekRange['end']])
                  ->when($todayLeagueIds, fn ($q) => $q->whereNotIn('league_id', $todayLeagueIds));
            })
            ->withCount(['fixtures as upcoming_tips_count' => function ($q) use ($nextDayRange, $nextWeekRange) {
                $q->whereHas('tips', fn ($t) => $t->published())
                  ->whereBetween('match_date', [$nextDayRange['start'], $nextWeekRange['end']]);
            }])
            ->orderByDesc('upcoming_tips_count')
            ->take(12)
            ->get();

        $recentSettledTips = Tip::with(['fixture'])
            ->whereIn('result', ['win', 'loss'])
            ->whereNotNull('result')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $featuredTip = Tip::with('fixture.league')
            ->published()
            ->whereHas('fixture', fn ($q) => $q->whereBetween('match_date', [$range['start'], $range['end']]))
            ->orderByDesc('confidence')
            ->first();

        return view('frontend.fixtures.index', compact('fixtures', 'date', 'bookmakers', 'otherLeagues', 'recentSettledTips', 'featuredTip'));
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
