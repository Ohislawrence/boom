<?php

namespace App\Http\Middleware;

use App\Services\GeoLocationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectGeoLocation
{
    public function handle(Request $request, Closure $next): Response
    {
        [$countryCode, $countryName, $timezone] = app(GeoLocationService::class)->resolve($request);

        view()->share('geoCountryCode', $countryCode);
        view()->share('geoCountryName', $countryName);
        view()->share('geoTimezone', $timezone);

        return $next($request);
    }
}
