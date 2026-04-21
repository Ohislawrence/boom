<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BetMarket;
use Illuminate\Http\Request;

class BetMarketController extends Controller
{
    public function index()
    {
        $betMarkets = BetMarket::orderBy('sort_order')->orderBy('name')->paginate(40);
        return view('admin.bet-markets.index', compact('betMarkets'));
    }

    public function create()
    {
        return view('admin.bet-markets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:bet_markets,slug',
            'category'    => 'nullable|string|max:60',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        BetMarket::create($data);

        return redirect()->route('admin.bet-markets.index')
            ->with('success', "Bet market \"{$data['name']}\" created.");
    }

    public function edit(BetMarket $betMarket)
    {
        return view('admin.bet-markets.edit', compact('betMarket'));
    }

    public function update(Request $request, BetMarket $betMarket)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:bet_markets,slug,' . $betMarket->id,
            'category'    => 'nullable|string|max:60',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active', false);
        $data['sort_order'] = $data['sort_order'] ?? $betMarket->sort_order;

        $betMarket->update($data);

        return redirect()->route('admin.bet-markets.index')
            ->with('success', "Bet market \"{$betMarket->name}\" updated.");
    }

    public function destroy(BetMarket $betMarket)
    {
        $name = $betMarket->name;
        $betMarket->delete();

        return redirect()->route('admin.bet-markets.index')
            ->with('success', "Bet market \"{$name}\" deleted.");
    }

    public function toggle(BetMarket $betMarket)
    {
        $betMarket->update(['is_active' => ! $betMarket->is_active]);
        return back()->with('success', 'Bet market status updated.');
    }
}
