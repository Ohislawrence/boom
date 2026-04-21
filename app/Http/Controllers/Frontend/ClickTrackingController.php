<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ClickEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class ClickTrackingController extends Controller
{
    public function track(Request $request): Response
    {
        // Rate limit: 60 clicks per minute per IP
        $key = 'click-track:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return response('', 204);
        }
        RateLimiter::hit($key, 60);

        $data = $request->validate([
            'event_type' => ['required', 'string', 'in:affiliate,nav,cta,external,other'],
            'label'      => ['nullable', 'string', 'max:250'],
            'target_url' => ['nullable', 'string', 'max:500'],
            'page_url'   => ['nullable', 'string', 'max:500'],
            'referrer'   => ['nullable', 'string', 'max:500'],
        ]);

        $ip          = $request->ip();
        $ipHash      = hash('sha256', $ip);
        [$countryCode, $countryName] = $this->resolveCountry($request, $ip, $ipHash);

        ClickEvent::create([
            'event_type'   => $data['event_type'],
            'label'        => $data['label'] ?? null,
            'target_url'   => isset($data['target_url']) ? substr($data['target_url'], 0, 500) : null,
            'page_url'     => isset($data['page_url']) ? substr($data['page_url'], 0, 500) : null,
            'referrer'     => isset($data['referrer']) ? substr($data['referrer'], 0, 500) : null,
            'country_code' => $countryCode,
            'country_name' => $countryName,
            'ip_hash'      => $ipHash,
            'user_id'      => auth()->id(),
            'device_type'  => $this->detectDevice($request->userAgent() ?? ''),
            'created_at'   => now(),
        ]);

        return response('', 204);
    }

    private function resolveCountry(Request $request, string $ip, string $ipHash): array
    {
        // 1. Cloudflare header (zero latency)
        $cfCode = $request->header('CF-IPCountry');
        if ($cfCode && strlen($cfCode) === 2 && $cfCode !== 'XX') {
            return [strtoupper($cfCode), null];
        }

        // 2. Cached lookup via ip-api.com (free, no key needed, cached 24h per IP)
        $cacheKey = 'geo:' . $ipHash;
        $cached   = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Skip lookup for private/local IPs
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            Cache::put($cacheKey, [null, null], 3600);
            return [null, null];
        }

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=countryCode,country");
            if ($response->successful() && $response->json('status') === 'success') {
                $result = [
                    strtoupper($response->json('countryCode', '')),
                    $response->json('country'),
                ];
                Cache::put($cacheKey, $result, 86400); // 24h
                return $result;
            }
        } catch (\Throwable) {
            // Non-fatal — just skip country
        }

        Cache::put($cacheKey, [null, null], 3600);
        return [null, null];
    }

    private function detectDevice(string $ua): string
    {
        $ua = strtolower($ua);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        return 'desktop';
    }
}
