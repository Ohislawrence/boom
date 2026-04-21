<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyRunLog;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Bookmaker;
use App\Models\Tip;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'tips_today'      => Tip::whereDate('created_at', today())->count(),
            'tips_total'      => Tip::count(),
            'tips_pending'    => Tip::where('status', 'pending')->count(),
            'fixtures_today'  => Fixture::whereDate('match_date', today())->count(),
            'leagues_active'  => League::where('is_active', true)->count(),
            'bookmakers'      => Bookmaker::count(),
        ];
        $recentLogs  = DailyRunLog::orderByDesc('run_date')->take(7)->get();
        $pendingTips = Tip::with(['fixture', 'betMarket'])
                          ->where('status', 'pending')
                          ->orderByDesc('confidence')
                          ->take(10)
                          ->get();
        return view('admin.dashboard', compact('stats', 'recentLogs', 'pendingTips'));
    }
}