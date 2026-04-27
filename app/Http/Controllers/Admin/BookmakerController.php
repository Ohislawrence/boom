<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BetMarket;
use App\Models\Bookmaker;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookmakerController extends Controller
{
    public function index()
    {
        $bookmakers = Bookmaker::withCount('betMarkets')->orderBy('sort_order')->get();
        return view('admin.bookmakers.index', compact('bookmakers'));
    }

    public function create()
    {
        $markets = BetMarket::where('is_active', true)->orderBy('name')->get();
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('admin.bookmakers.create', compact('markets', 'countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'affiliate_url' => 'required|url',
            'welcome_offer' => 'nullable|string|max:200',
            'bonus_text'    => 'nullable|string|max:400',
            'review'        => 'nullable|string',
            'rating'        => 'required|numeric|min:0|max:5',
            'sort_order'    => 'nullable|integer',
            'is_active'     => 'boolean',
            'markets'       => 'nullable|array',
            'markets.*'     => 'exists:bet_markets,id',
            'countries'     => 'nullable|array',
            'countries.*'   => 'exists:countries,id',
        ]);
        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $bookmaker = Bookmaker::create($data);
        if (!empty($data['markets'])) {
            $bookmaker->betMarkets()->sync($data['markets']);
        }
        $bookmaker->countries()->sync($data['countries'] ?? []);
        return redirect()->route('admin.bookmakers.index')->with('success', 'Bookmaker created.');
    }

    public function show(Bookmaker $bookmaker)
    {
        return redirect()->route('admin.bookmakers.edit', $bookmaker);
    }

    public function edit(Bookmaker $bookmaker)
    {
        $markets = BetMarket::where('is_active', true)->orderBy('name')->get();
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $bookmaker->load(['betMarkets', 'countries']);
        return view('admin.bookmakers.edit', compact('bookmaker', 'markets', 'countries'));
    }

    public function update(Request $request, Bookmaker $bookmaker)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'affiliate_url' => 'required|url',
            'welcome_offer' => 'nullable|string|max:200',
            'bonus_text'    => 'nullable|string|max:400',
            'review'        => 'nullable|string',
            'rating'        => 'required|numeric|min:0|max:5',
            'sort_order'    => 'nullable|integer',
            'is_active'     => 'boolean',
            'markets'       => 'nullable|array',
            'markets.*'     => 'exists:bet_markets,id',
            'countries'     => 'nullable|array',
            'countries.*'   => 'exists:countries,id',
        ]);
        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $bookmaker->update($data);
        $bookmaker->betMarkets()->sync($data['markets'] ?? []);
        $bookmaker->countries()->sync($data['countries'] ?? []);
        return redirect()->route('admin.bookmakers.index')->with('success', 'Bookmaker updated.');
    }

    public function destroy(Bookmaker $bookmaker)
    {
        $bookmaker->delete();
        return redirect()->route('admin.bookmakers.index')->with('success', 'Bookmaker deleted.');
    }
}