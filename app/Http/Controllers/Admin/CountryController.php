<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->paginate(40);
        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100|unique:countries,name',
            'code'      => 'nullable|string|max:3',
            'flag_url'  => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Country::create($data);

        return redirect()->route('admin.countries.index')
            ->with('success', "Country \"{$data['name']}\" created.");
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100|unique:countries,name,' . $country->id,
            'code'      => 'nullable|string|max:3',
            'flag_url'  => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $country->update($data);

        return redirect()->route('admin.countries.index')
            ->with('success', "Country \"{$country->name}\" updated.");
    }

    public function destroy(Country $country)
    {
        $name = $country->name;
        $country->delete();

        return redirect()->route('admin.countries.index')
            ->with('success', "Country \"{$name}\" deleted.");
    }

    public function toggle(Country $country)
    {
        $country->update(['is_active' => ! $country->is_active]);
        return back()->with('success', "Country status updated.");
    }
}
