<?php

namespace App\Http\Controllers\Bettor;

use App\Http\Controllers\Controller;
use App\Models\Tip;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $featuredTips = Tip::with(['fixture.league', 'betMarket'])
            ->where('status', 'published')
            ->where('confidence', '>=', 70)
            ->whereHas('fixture', fn($q) => $q->whereDate('match_date', '>=', now()->toDateString()))
            ->orderByDesc('confidence')
            ->limit(6)
            ->get();

        return view('bettor.dashboard', compact('featuredTips'));
    }
}
