<?php

namespace App\Http\Controllers\Tipster;

use App\Http\Controllers\Controller;
use App\Models\BetMarket;
use App\Models\Fixture;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Tip::with(['fixture.league', 'betMarket', 'tipResult'])
                    ->where('submitted_by', auth()->id())
                    ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tips = $query->paginate(20);

        return view('tipster.tips.index', compact('tips', 'status'));
    }

    public function create()
    {
        $fixtures = Fixture::with('league')
                           ->where('match_date', '>', now())
                           ->whereIn('status', ['NS', 'TBD'])
                           ->orderBy('match_date')
                           ->take(200)
                           ->get();

        $markets = BetMarket::where('is_active', true)->orderBy('sort_order')->get();

        return view('tipster.tips.create', compact('fixtures', 'markets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fixture_id'    => 'required|exists:fixtures,id',
            'bet_market_id' => 'nullable|exists:bet_markets,id',
            'market'        => 'required|string|max:100',
            'selection'     => 'required|string|max:200',
            'odds'          => 'nullable|numeric|min:1.01|max:1001',
            'confidence'    => 'required|integer|min:1|max:100',
            'reasoning'     => 'nullable|string|max:5000',
            'is_value_bet'  => 'boolean',
        ]);

        $data['submitted_by']    = auth()->id();
        $data['is_ai_generated'] = false;
        $data['status']          = 'pending';
        $data['is_value_bet']    = $request->boolean('is_value_bet');

        Tip::create($data);

        return redirect()->route('tipster.tips.index')
                         ->with('success', 'Tip submitted! It will appear on the site once approved by an admin.');
    }

    public function show(Tip $tip)
    {
        abort_unless($tip->submitted_by === auth()->id() || auth()->user()->hasRole('admin'), 403);

        $tip->load(['fixture.league', 'betMarket', 'tipResult']);

        return view('tipster.tips.show', compact('tip'));
    }

    public function destroy(Tip $tip)
    {
        abort_unless($tip->submitted_by === auth()->id(), 403);
        abort_if($tip->status === 'published', 403, 'Cannot delete a published tip.');

        $tip->delete();

        return redirect()->route('tipster.tips.index')->with('success', 'Tip deleted.');
    }
}
