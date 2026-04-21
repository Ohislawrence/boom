<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\League;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function index()
    {
        $leagues = League::with('country')->withCount('fixtures')->orderBy('name')->get();
        return view('admin.leagues.index', compact('leagues'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('admin.leagues.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'country_id'      => 'nullable|exists:countries,id',
            'season'          => 'nullable|string|max:20',
            'api_football_id' => 'nullable|integer',
            'logo_url'        => 'nullable|url',
            'is_active'       => 'boolean',
        ]);

        // Derive the country string from the chosen Country record
        $data['country'] = $data['country_id']
            ? Country::find($data['country_id'])->name
            : '';

        $data['is_active'] = $request->boolean('is_active');

        League::create($data);
        return redirect()->route('admin.leagues.index')->with('success', 'League created.');
    }

    public function edit(League $league)
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('admin.leagues.edit', compact('league', 'countries'));
    }

    public function update(Request $request, League $league)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'country_id'      => 'nullable|exists:countries,id',
            'season'          => 'nullable|string|max:20',
            'api_football_id' => 'nullable|integer',
            'logo_url'        => 'nullable|url',
            'is_active'       => 'boolean',
        ]);

        $data['country'] = $data['country_id']
            ? Country::find($data['country_id'])->name
            : $league->country;

        $data['is_active'] = $request->boolean('is_active');

        $league->update($data);
        return redirect()->route('admin.leagues.index')->with('success', 'League updated.');
    }

    public function toggle(League $league)
    {
        $league->update(['is_active' => !$league->is_active]);
        return back()->with('success', 'League status updated.');
    }
}
