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

class HomeController extends Controller
{
    public function index(Request $request, GeoLocationService $geo)
    {
        $date = $request->filled('date')
            ? $geo->localDate($request->date)
            : $geo->localDate();

        $range = $geo->localDateRange($date->toDateString());

        // All NS fixtures for the date, grouped by league — tips as sub-items
        $fixtures = Fixture::with([
                'league.country',
                'tips' => fn ($q) => $q->published()->orderByDesc('confidence'),
            ])
            ->whereBetween('match_date', [$range['start'], $range['end']])
            ->where('status', 'NS')
            ->orderBy('match_date')
            ->get()
            ->groupBy(fn ($f) => $f->league_id ?? 0);

        $latestUnplayedFixtures = Fixture::with('league.country')
            ->whereBetween('match_date', [$range['start'], $range['end']])
            ->where('status', 'NS')
            ->orderBy('match_date')
            ->take(10)
            ->get();

        $latestPlayedFixtures = Fixture::with('league.country')
            ->whereBetween('match_date', [$range['start'], $range['end']])
            ->where('status', '<>', 'NS')
            ->whereNotNull('score_home')
            ->whereNotNull('score_away')
            ->orderByDesc('match_date')
            ->take(10)
            ->get();

        // Featured tip = highest confidence published tip for the date
        $featuredTip = Tip::with('fixture.league')
            ->published()
            ->whereHas('fixture', fn ($q) => $q->whereBetween('match_date', [$range['start'], $range['end']]))
            ->orderByDesc('confidence')
            ->first();

        // Active bookmakers for sidebar / section, local country first
        $bookmakers = Bookmaker::active()
            ->forCountry($geo->currentCountryCode())
            ->take(6)
            ->get();

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

        return view('welcome', compact('fixtures', 'latestUnplayedFixtures', 'latestPlayedFixtures', 'featuredTip', 'bookmakers', 'date', 'otherLeagues', 'recentSettledTips'));
    }

}
