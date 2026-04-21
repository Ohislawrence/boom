<?php

namespace App\Http\Controllers\Tipster;

use App\Http\Controllers\Controller;
use App\Models\Tip;
use App\Models\TipResult;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalTips     = Tip::where('submitted_by', $user->id)->count();
        $publishedTips = Tip::where('submitted_by', $user->id)->where('status', 'published')->count();
        $pendingTips   = Tip::where('submitted_by', $user->id)->where('status', 'pending')->count();
        $rejectedTips  = Tip::where('submitted_by', $user->id)->where('status', 'rejected')->count();

        // Performance from settled tip results
        $results      = TipResult::whereHas('tip', fn ($q) => $q->where('submitted_by', $user->id))->get();
        $settledCount = $results->count();
        $wins         = $results->where('result', 'win')->count();
        $winRate      = $settledCount > 0 ? round(($wins / $settledCount) * 100, 1) : null;
        $totalPL      = $results->sum('profit_loss');
        $roi          = $settledCount > 0 ? round(($totalPL / $settledCount) * 100, 1) : null;

        $recentTips = Tip::with(['fixture.league', 'betMarket', 'tipResult'])
                         ->where('submitted_by', $user->id)
                         ->orderByDesc('created_at')
                         ->take(10)
                         ->get();

        return view('tipster.dashboard', compact(
            'totalTips', 'publishedTips', 'pendingTips', 'rejectedTips',
            'settledCount', 'wins', 'winRate', 'roi', 'totalPL', 'recentTips'
        ));
    }
}
