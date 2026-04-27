<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Tip;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, GeoLocationService $geo)
    {
        $query = trim((string) $request->query('q', ''));
        $fixtures = collect();
        $tips = collect();
        $bookmakers = collect();
        $leagues = collect();

        if ($query !== '') {
            $search = '%' . str_replace(' ', '%', $query) . '%';

            $fixtures = Fixture::with('league')
                ->where(function ($q) use ($search) {
                    $q->where('home_team', 'like', $search)
                        ->orWhere('away_team', 'like', $search)
                        ->orWhereHas('league', fn ($q) => $q->where('name', 'like', $search));
                })
                ->orderBy('match_date')
                ->limit(10)
                ->get();

            $tips = Tip::published()
                ->with('fixture.league')
                ->where(function ($q) use ($search) {
                    $q->where('market', 'like', $search)
                        ->orWhere('selection', 'like', $search)
                        ->orWhereHas('fixture', function ($q) use ($search) {
                            $q->where('home_team', 'like', $search)
                                ->orWhere('away_team', 'like', $search)
                                ->orWhereHas('league', fn ($q) => $q->where('name', 'like', $search));
                        });
                })
                ->limit(10)
                ->get();

            $bookmakers = Bookmaker::active()
                ->forCountry($geo->currentCountryCode())
                ->where('name', 'like', $search)
                ->limit(10)
                ->get();

            $leagues = League::active()
                ->where('name', 'like', $search)
                ->limit(10)
                ->get();
        }

        return view('frontend.search.index', compact('query', 'fixtures', 'tips', 'bookmakers', 'leagues'));
    }
}
