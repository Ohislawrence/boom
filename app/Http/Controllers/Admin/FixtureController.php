<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\League;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    public function index(Request $request)
    {
        $query = Fixture::with('league')->withCount('tips');

        if ($request->filled('league')) {
            $query->where('league_id', $request->league);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('match_date', $request->date);
        } else {
            // default: today ± 3 days in app timezone
            $now = Carbon::now(config('app.timezone'));
            $query->whereBetween('match_date', [$now->copy()->subDays(3), $now->copy()->addDays(7)]);
        }

        $fixtures = $query->orderBy('match_date')->paginate(40);
        $leagues  = League::where('is_active', true)->orderBy('name')->get();

        $statuses = ['NS', 'FT', 'PST', 'CANC', 'LIVE'];

        return view('admin.fixtures.index', compact('fixtures', 'leagues', 'statuses'));
    }
}
