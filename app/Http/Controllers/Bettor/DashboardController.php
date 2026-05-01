<?php

namespace App\Http\Controllers\Bettor;

use App\Http\Controllers\Controller;
use App\Models\Tip;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, GeoLocationService $geo)
    {
        $localDayStartUtc = $geo->localDate()->startOfDay()->setTimezone('UTC');

        $featuredTips = Tip::with(['fixture.league', 'betMarket'])
            ->where('status', 'published')
            ->where('confidence', '>=', 70)
            ->whereHas('fixture', fn($q) => $q->where('match_date', '>=', $localDayStartUtc))
            ->orderByDesc('confidence')
            ->limit(6)
            ->get();

        return view('bettor.dashboard', compact('featuredTips'));
    }
}
