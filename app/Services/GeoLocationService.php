<?php

namespace App\Services;

use App\Models\Country;
use App\Models\IpLocationRange;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GeoLocationService
{
    public function resolve(Request $request): array
    {
        $session = $request->session();

        if ($session->has('geo.country_code')) {
            return [
                $session->get('geo.country_code'),
                $session->get('geo.country_name'),
                $session->get('geo.timezone'),
            ];
        }

        $countryCode = $this->headerCountryCode($request);
        $countryName = null;
        $timezone    = null;
        $ip          = $request->ip();

        if ($this->isPublicIp($ip)) {
            $location = $this->findIpLocation($ip);
            if ($location) {
                $countryCode = $location->country_code;
                $countryName = $location->country_name;
                $timezone    = $location->timezone;
            }
        }

        if (!$countryCode) {
            $countryCode = $this->headerCountryCode($request);
        }

        if ($countryCode && !$countryName) {
            $countryName = $this->resolveCountryName($countryCode);
        }

        if (!$timezone && $countryCode) {
            $timezone = $this->resolveTimezoneForCountry($countryCode);
        }

        $session->put('geo.country_code', $countryCode);
        $session->put('geo.country_name', $countryName);
        $session->put('geo.timezone', $timezone);

        return [$countryCode, $countryName, $timezone];
    }

    public function currentCountryCode(): ?string
    {
        return session('geo.country_code');
    }

    public function currentCountryName(): ?string
    {
        return session('geo.country_name');
    }

    public function currentTimezone(): ?string
    {
        return session('geo.timezone');
    }

    public function localNow(): Carbon
    {
        return Carbon::now($this->currentTimezone() ?: config('app.timezone'));
    }

    public function localDate(string $date = null): Carbon
    {
        $timezone = $this->currentTimezone() ?: config('app.timezone');
        if ($date) {
            return Carbon::parse($date, $timezone);
        }
        return Carbon::now($timezone);
    }

    public function localDateRange(string $date = null): array
    {
        $timezone = $this->currentTimezone() ?: config('app.timezone');
        $localDate = $this->localDate($date)->startOfDay();

        return [
            'start' => $localDate->copy()->setTimezone('UTC'),
            'end'   => $localDate->copy()->endOfDay()->setTimezone('UTC'),
        ];
    }

    public function findIpLocation(string $ip): ?IpLocationRange
    {
        $ipNumber = $this->ipToNumber($ip);
        if ($ipNumber === null) {
            return null;
        }

        return IpLocationRange::where('ip_from', '<=', $ipNumber)
            ->where('ip_to', '>=', $ipNumber)
            ->orderBy('ip_from', 'desc')
            ->first();
    }

    public function ipToNumber(string $ip): ?int
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return sprintf('%u', ip2long($ip));
        }

        return null;
    }

    public function headerCountryCode(Request $request): ?string
    {
        $code = strtoupper((string) $request->header('CF-IPCountry', ''));
        if ($code && strlen($code) === 2 && $code !== 'XX') {
            return $code;
        }

        return null;
    }

    public function resolveCountryName(string $countryCode): ?string
    {
        return Country::where('code', strtoupper($countryCode))->value('name');
    }

    public function resolveTimezoneForCountry(string $countryCode): ?string
    {
        $countryCode = strtoupper($countryCode);
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);
        if (!empty($timezones)) {
            return $timezones[0];
        }

        return config('app.timezone');
    }

    private function isPublicIp(?string $ip): bool
    {
        if (! $ip) {
            return false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
