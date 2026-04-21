<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClickAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $days      = (int) $request->input('days', 7);
        $type      = $request->input('type');
        $country   = $request->input('country');
        $days      = in_array($days, [1, 7, 14, 30, 90]) ? $days : 7;
        $since     = now()->subDays($days)->startOfDay();

        $base = ClickEvent::query()->where('created_at', '>=', $since);

        if ($type) {
            $base->where('event_type', $type);
        }
        if ($country) {
            $base->where('country_code', strtoupper($country));
        }

        // Summary counts
        $totalClicks   = (clone $base)->count();
        $todayClicks   = ClickEvent::whereDate('created_at', today())->count();
        $affiliateClicks = (clone $base)->where('event_type', 'affiliate')->count();
        $uniqueIps     = (clone $base)->distinct('ip_hash')->count('ip_hash');

        // Clicks by type
        $byType = (clone $base)
            ->select('event_type', DB::raw('COUNT(*) as total'))
            ->groupBy('event_type')
            ->orderByDesc('total')
            ->get();

        // Top clicked URLs (affiliate / external)
        $topUrls = (clone $base)
            ->whereIn('event_type', ['affiliate', 'external', 'cta'])
            ->whereNotNull('target_url')
            ->select('target_url', 'label', 'event_type', DB::raw('COUNT(*) as total'))
            ->groupBy('target_url', 'label', 'event_type')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        // Clicks by country
        $byCountry = (clone $base)
            ->whereNotNull('country_code')
            ->select('country_code', 'country_name', DB::raw('COUNT(*) as total'))
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        // Clicks by day (sparkline)
        $byDay = (clone $base)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Clicks by device
        $byDevice = (clone $base)
            ->select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        // Top referrer domains
        $topReferrers = (clone $base)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->select('referrer', DB::raw('COUNT(*) as total'))
            ->groupBy('referrer')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $host = parse_url($row->referrer, PHP_URL_HOST) ?: $row->referrer;
                $row->domain = $host;
                return $row;
            })
            ->groupBy('domain')
            ->map(fn($group) => (object)['domain' => $group->first()->domain, 'total' => $group->sum('total')])
            ->sortByDesc('total')
            ->take(15)
            ->values();

        // Recent events (paginated)
        $events = ClickEvent::query()
            ->where('created_at', '>=', $since)
            ->when($type, fn($q) => $q->where('event_type', $type))
            ->when($country, fn($q) => $q->where('country_code', strtoupper($country)))
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        $availableTypes    = ['affiliate', 'nav', 'cta', 'external', 'other'];
        $availableCountries = ClickEvent::whereNotNull('country_code')
            ->select('country_code', 'country_name')
            ->distinct()
            ->orderBy('country_code')
            ->get();

        return view('admin.click-analytics.index', compact(
            'totalClicks', 'todayClicks', 'affiliateClicks', 'uniqueIps',
            'byType', 'topUrls', 'byCountry', 'byDay', 'byDevice',
            'topReferrers', 'events',
            'days', 'type', 'country',
            'availableTypes', 'availableCountries'
        ));
    }
}
