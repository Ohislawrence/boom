<x-admin-layout title="Click Analytics">

@php
    $typeColors = [
        'affiliate' => 'var(--accent)',
        'nav'       => '#63b3ed',
        'cta'       => 'var(--accent2)',
        'external'  => '#b794f4',
        'other'     => 'var(--muted)',
    ];
    $maxDay = $byDay->max('total') ?: 1;
@endphp

{{-- ── Filters ── --}}
<form method="GET" style="display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:1.5rem;align-items:flex-end">
    <div>
        <div style="font-size:.68rem;color:var(--muted);margin-bottom:.25rem;text-transform:uppercase;letter-spacing:.08em">Period</div>
        <select name="days" onchange="this.form.submit()" style="background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:6px;padding:.35rem .6rem;font-size:.8rem">
            @foreach([1=>'Today',7=>'7 days',14=>'14 days',30=>'30 days',90=>'90 days'] as $val=>$label)
            <option value="{{ $val }}" {{ $days == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <div style="font-size:.68rem;color:var(--muted);margin-bottom:.25rem;text-transform:uppercase;letter-spacing:.08em">Event Type</div>
        <select name="type" onchange="this.form.submit()" style="background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:6px;padding:.35rem .6rem;font-size:.8rem">
            <option value="">All types</option>
            @foreach($availableTypes as $t)
            <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <div style="font-size:.68rem;color:var(--muted);margin-bottom:.25rem;text-transform:uppercase;letter-spacing:.08em">Country</div>
        <select name="country" onchange="this.form.submit()" style="background:var(--card);border:1px solid var(--border);color:var(--text);border-radius:6px;padding:.35rem .6rem;font-size:.8rem">
            <option value="">All countries</option>
            @foreach($availableCountries as $c)
            <option value="{{ $c->country_code }}" {{ $country === $c->country_code ? 'selected' : '' }}>
                {{ $c->country_code }}{{ $c->country_name ? ' — '.$c->country_name : '' }}
            </option>
            @endforeach
        </select>
    </div>
    @if($type || $country)
    <a href="{{ route('admin.click-analytics.index') }}" style="font-size:.75rem;color:var(--muted);padding:.38rem .6rem;border:1px solid var(--border);border-radius:6px;text-decoration:none">✕ Clear</a>
    @endif
</form>

{{-- ── Summary stats ── --}}
<div class="admin-stat-grid" style="margin-bottom:1.5rem">
    <div class="admin-stat">
        <div class="admin-stat-value" style="color:var(--accent)">{{ number_format($totalClicks) }}</div>
        <div class="admin-stat-label">Total Clicks ({{ $days }}d)</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value">{{ number_format($todayClicks) }}</div>
        <div class="admin-stat-label">Clicks Today</div>
    </div>
    <div class="admin-stat" style="border-color:rgba(0,229,160,.3)">
        <div class="admin-stat-value" style="color:var(--accent)">{{ number_format($affiliateClicks) }}</div>
        <div class="admin-stat-label">Affiliate Clicks</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value">{{ number_format($uniqueIps) }}</div>
        <div class="admin-stat-label">Unique Visitors</div>
    </div>
</div>

{{-- ── Three-column grid ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">

    {{-- Clicks by type --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Clicks by Type</span>
        </div>
        <div style="padding:.75rem 1rem">
            @php $maxType = $byType->max('total') ?: 1; @endphp
            @forelse($byType as $row)
            <div style="margin-bottom:.6rem">
                <div style="display:flex;justify-content:space-between;margin-bottom:.2rem">
                    <span style="font-size:.78rem;color:{{ $typeColors[$row->event_type] ?? 'var(--text)' }}">{{ ucfirst($row->event_type) }}</span>
                    <span style="font-family:var(--fm);font-size:.75rem;color:var(--text)">{{ number_format($row->total) }}</span>
                </div>
                <div style="height:4px;background:var(--surface);border-radius:2px">
                    <div style="height:4px;border-radius:2px;background:{{ $typeColors[$row->event_type] ?? 'var(--accent)' }};width:{{ round($row->total / $maxType * 100) }}%"></div>
                </div>
            </div>
            @empty
            <div style="font-size:.8rem;color:var(--muted);text-align:center;padding:1rem">No data</div>
            @endforelse
        </div>
    </div>

    {{-- Clicks by device --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Device Breakdown</span>
        </div>
        <div style="padding:.75rem 1rem">
            @php $maxDev = $byDevice->max('total') ?: 1; @endphp
            @forelse($byDevice as $row)
            @php $icon = match($row->device_type) { 'mobile' => '📱', 'tablet' => '🖥', default => '💻' }; @endphp
            <div style="margin-bottom:.6rem">
                <div style="display:flex;justify-content:space-between;margin-bottom:.2rem">
                    <span style="font-size:.78rem;color:var(--text)">{{ $icon }} {{ ucfirst($row->device_type ?? 'unknown') }}</span>
                    <span style="font-family:var(--fm);font-size:.75rem;color:var(--text)">{{ number_format($row->total) }}</span>
                </div>
                <div style="height:4px;background:var(--surface);border-radius:2px">
                    <div style="height:4px;border-radius:2px;background:var(--accent);width:{{ round($row->total / $maxDev * 100) }}%"></div>
                </div>
            </div>
            @empty
            <div style="font-size:.8rem;color:var(--muted);text-align:center;padding:1rem">No data</div>
            @endforelse
        </div>
    </div>

    {{-- Top referrers --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Top Referrers</span>
        </div>
        <div style="padding:.75rem 1rem">
            @php $maxRef = $topReferrers->max('total') ?: 1; @endphp
            @forelse($topReferrers as $row)
            <div style="margin-bottom:.55rem">
                <div style="display:flex;justify-content:space-between;margin-bottom:.2rem">
                    <span style="font-size:.72rem;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px" title="{{ $row->domain }}">
                        {{ $row->domain ?: '(direct)' }}
                    </span>
                    <span style="font-family:var(--fm);font-size:.72rem;color:var(--muted)">{{ number_format($row->total) }}</span>
                </div>
                <div style="height:3px;background:var(--surface);border-radius:2px">
                    <div style="height:3px;border-radius:2px;background:#b794f4;width:{{ round($row->total / $maxRef * 100) }}%"></div>
                </div>
            </div>
            @empty
            <div style="font-size:.8rem;color:var(--muted);text-align:center;padding:1rem">No referrer data</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Activity sparkline + Countries side by side ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">

    {{-- Daily activity --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Daily Activity</span>
        </div>
        <div style="padding:.75rem 1rem">
            @php
                $period = \Carbon\CarbonPeriod::create(now()->subDays($days - 1)->startOfDay(), '1 day', now());
            @endphp
            <div style="display:flex;align-items:flex-end;gap:3px;height:60px;margin-bottom:.5rem">
                @foreach($period as $date)
                @php $dayKey = $date->format('Y-m-d'); $cnt = $byDay[$dayKey]->total ?? 0; $h = $maxDay > 0 ? max(2, round($cnt / $maxDay * 56)) : 2; @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px">
                    <div title="{{ $dayKey }}: {{ $cnt }} clicks" style="width:100%;height:{{ $h }}px;background:var(--accent);border-radius:2px 2px 0 0;opacity:{{ $cnt > 0 ? '1' : '.2' }}"></div>
                </div>
                @endforeach
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.62rem;color:var(--muted)">
                <span>{{ now()->subDays($days - 1)->format('d M') }}</span>
                <span>Today</span>
            </div>
        </div>
    </div>

    {{-- Top countries --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Top Countries</span>
        </div>
        <div style="padding:.75rem 1rem">
            @php $maxCty = $byCountry->max('total') ?: 1; @endphp
            @forelse($byCountry->take(10) as $row)
            <div style="display:grid;grid-template-columns:30px 1fr 48px;gap:.5rem;align-items:center;margin-bottom:.4rem">
                <span style="font-family:var(--fm);font-size:.78rem;font-weight:700;color:var(--accent2)">{{ $row->country_code }}</span>
                <div style="height:6px;background:var(--surface);border-radius:3px">
                    <div style="height:6px;border-radius:3px;background:var(--accent2);width:{{ round($row->total / $maxCty * 100) }}%"></div>
                </div>
                <span style="font-family:var(--fm);font-size:.72rem;color:var(--muted);text-align:right">{{ number_format($row->total) }}</span>
            </div>
            @empty
            <div style="font-size:.8rem;color:var(--muted);text-align:center;padding:1rem">No country data yet</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Top clicked URLs ── --}}
<div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
    <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
        <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Top Clicked URLs</span>
    </div>
    @forelse($topUrls as $row)
    <div style="padding:.55rem 1rem;border-bottom:1px solid var(--border);display:grid;grid-template-columns:auto 1fr auto auto;gap:.75rem;align-items:center">
        <span class="badge" style="font-size:.6rem;background:{{ $typeColors[$row->event_type] ?? 'var(--card2)' }}22;color:{{ $typeColors[$row->event_type] ?? 'var(--muted)' }};border:1px solid {{ $typeColors[$row->event_type] ?? 'var(--border)' }}44;padding:.15rem .4rem;border-radius:4px">{{ $row->event_type }}</span>
        <div>
            @if($row->label)
            <div style="font-size:.8rem;color:var(--text)">{{ $row->label }}</div>
            @endif
            <div style="font-size:.68rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:480px">{{ $row->target_url }}</div>
        </div>
        <span style="font-family:var(--fm);font-size:.85rem;font-weight:700;color:var(--accent)">{{ number_format($row->total) }}</span>
        <span style="font-size:.65rem;color:var(--muted)">clicks</span>
    </div>
    @empty
    <div style="padding:1.5rem;text-align:center;font-size:.82rem;color:var(--muted)">No URL data yet</div>
    @endforelse
</div>

{{-- ── Recent events log ── --}}
<div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
    <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
        <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Recent Events</span>
        <span style="font-size:.72rem;color:var(--muted)">{{ number_format($events->total()) }} total</span>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:.75rem">
            <thead>
                <tr style="background:var(--surface)">
                    <th style="padding:.45rem .75rem;text-align:left;color:var(--muted);font-weight:500;white-space:nowrap">Time</th>
                    <th style="padding:.45rem .75rem;text-align:left;color:var(--muted);font-weight:500">Type</th>
                    <th style="padding:.45rem .75rem;text-align:left;color:var(--muted);font-weight:500">Label</th>
                    <th style="padding:.45rem .75rem;text-align:left;color:var(--muted);font-weight:500">Target URL</th>
                    <th style="padding:.45rem .75rem;text-align:left;color:var(--muted);font-weight:500">Page</th>
                    <th style="padding:.45rem .75rem;text-align:left;color:var(--muted);font-weight:500">Country</th>
                    <th style="padding:.45rem .75rem;text-align:left;color:var(--muted);font-weight:500">Device</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:.45rem .75rem;color:var(--muted);white-space:nowrap">{{ $event->created_at->format('d M H:i') }}</td>
                    <td style="padding:.45rem .75rem">
                        <span style="font-size:.68rem;color:{{ $typeColors[$event->event_type] ?? 'var(--muted)' }}">{{ $event->event_type }}</span>
                    </td>
                    <td style="padding:.45rem .75rem;color:var(--text);max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $event->label }}</td>
                    <td style="padding:.45rem .75rem;color:var(--muted);max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $event->target_url }}">{{ $event->target_url }}</td>
                    <td style="padding:.45rem .75rem;color:var(--muted);max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $event->page_url }}">{{ $event->page_url ? parse_url($event->page_url, PHP_URL_PATH) : '' }}</td>
                    <td style="padding:.45rem .75rem;color:var(--accent2);font-family:var(--fm);font-size:.72rem">{{ $event->country_code ?? '—' }}</td>
                    <td style="padding:.45rem .75rem;color:var(--muted)">{{ $event->device_type ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:2rem;text-align:center;color:var(--muted)">No events recorded yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($events->hasPages())
    <div style="padding:.75rem 1rem;border-top:1px solid var(--border)">
        {{ $events->links() }}
    </div>
    @endif
</div>

</x-admin-layout>
