<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Tip;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $date = request('date') ? Carbon::parse(request('date')) : now();

        // All NS fixtures for the date, grouped by league — tips as sub-items
        $fixtures = Fixture::with([
                'league.country',
                'tips' => fn ($q) => $q->published()->orderByDesc('confidence'),
            ])
            ->whereDate('match_date', $date)
            ->where('status', 'NS')
            ->orderBy('match_date')
            ->get()
            ->groupBy(fn ($f) => $f->league_id ?? 0);

        // Featured tip = highest confidence published tip for the date
        $featuredTip = Tip::with('fixture.league')
            ->published()
            ->whereHas('fixture', fn ($q) => $q->whereDate('match_date', $date))
            ->orderByDesc('confidence')
            ->first();

        // Active bookmakers for sidebar / section
        $bookmakers = Bookmaker::active()->take(6)->get();

        // Other competitions: leagues with published tips in next 7 days, excluding today's leagues
        $todayLeagueIds = $fixtures->keys()->filter()->all();
        $otherLeagues = League::with('country')
            ->whereHas('fixtures', function ($q) use ($date, $todayLeagueIds) {
                $q->whereHas('tips', fn ($t) => $t->published())
                  ->whereBetween('match_date', [
                      $date->copy()->addDay()->startOfDay(),
                      $date->copy()->addDays(7)->endOfDay(),
                  ])
                  ->when($todayLeagueIds, fn ($q) => $q->whereNotIn('league_id', $todayLeagueIds));
            })
            ->withCount(['fixtures as upcoming_tips_count' => function ($q) use ($date) {
                $q->whereHas('tips', fn ($t) => $t->published())
                  ->whereBetween('match_date', [
                      $date->copy()->addDay()->startOfDay(),
                      $date->copy()->addDays(7)->endOfDay(),
                  ]);
            }])
            ->orderByDesc('upcoming_tips_count')
            ->take(12)
            ->get();

        // Recent settled tips (win/loss) for the "Recent Results" sidebar widget
        $recentSettledTips = Tip::with(['fixture'])
            ->whereIn('result', ['win', 'loss'])
            ->whereNotNull('result')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        return view('welcome', compact('fixtures', 'featuredTip', 'bookmakers', 'date', 'otherLeagues', 'recentSettledTips'));
    }
}
