<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use Illuminate\Support\Carbon;

class LeagueController extends Controller
{
    public function show(League $league)
    {
        $league->load(['country']);

        $date = request('date') ? Carbon::parse(request('date')) : now()->addDay();

        $fixtures = Fixture::with([
                'tips' => fn ($q) => $q->published()->orderByDesc('confidence'),
            ])
            ->where('league_id', $league->id)
            ->whereDate('match_date', $date)
            ->whereHas('tips', fn ($q) => $q->published())
            ->orderBy('match_date')
            ->paginate(20);

        $bookmakers = Bookmaker::active()->take(4)->get();

        return view('frontend.leagues.show', compact('league', 'fixtures', 'date', 'bookmakers'));
    }
}
