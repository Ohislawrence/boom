<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;

class BookmakerController extends Controller
{
    public function index(GeoLocationService $geo)
    {
        $bookmakers = Bookmaker::active()
            ->forCountry($geo->currentCountryCode())
            ->orderBy('sort_order')
            ->get();

        return view('frontend.bookmakers.index', compact('bookmakers'));
    }

    public function show(Bookmaker $bookmaker, GeoLocationService $geo)
    {
        abort_unless($bookmaker->is_active, 404);

        $bookmaker->load('betMarkets');

        $others = Bookmaker::active()
            ->where('id', '!=', $bookmaker->id)
            ->take(4)
            ->get();

        return view('frontend.bookmakers.show', compact('bookmaker', 'others'));
    }
}
