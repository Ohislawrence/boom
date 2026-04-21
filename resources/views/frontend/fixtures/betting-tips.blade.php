<x-app-layout>

<x-slot name="title">{{ $fixture->home_team }} vs {{ $fixture->away_team }} — AI Betting Tips & Match Preview</x-slot>
<x-slot name="description">{{ Str::limit('AI betting tips for ' . $fixture->home_team . ' vs ' . $fixture->away_team . ' on ' . $fixture->match_date->format('d M Y') . '. ' . ($tips->isNotEmpty() ? $tips->count() . ' AI tips with confidence ratings, odds, match predictions and analysis.' : 'Match preview, odds, predictions and head-to-head stats.'), 160) }}</x-slot>
<x-slot name="canonical">{{ route('fixture.betting-tips', $fixture) }}</x-slot>

@push('head')
<style>
.bt-stat-bar-wrap { display:grid; grid-template-columns:1fr 90px 1fr; align-items:center; gap:.4rem; margin-bottom:.45rem; }
.bt-bar-h { height:7px; background:var(--surface); border-radius:3px; overflow:hidden; display:flex; justify-content:flex-end; }
.bt-bar-a { height:7px; background:var(--surface); border-radius:3px; overflow:hidden; }
.bt-bar-fill-h { height:100%; background:var(--accent); border-radius:3px; transition:width .4s ease; }
.bt-bar-fill-a { height:100%; background:var(--accent2); border-radius:3px; transition:width .4s ease; }
.bt-guide-step { display:flex; align-items:flex-start; gap:.75rem; padding:.65rem 0; border-bottom:1px solid var(--border); }
.bt-guide-step:last-child { border-bottom:none; }
.bt-guide-num { width:24px; height:24px; border-radius:50%; background:rgba(0,229,160,.12); border:1px solid rgba(0,229,160,.3); color:var(--accent); font-family:var(--fm); font-size:.72rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:.05rem; }
.bt-pick-card { background:linear-gradient(135deg,rgba(0,229,160,.06) 0%,rgba(0,0,0,0) 100%); border:1px solid rgba(0,229,160,.35); border-radius:10px; padding:1.1rem; margin-bottom:.75rem; }
.bt-pick-card.value { border-color:rgba(245,197,24,.5); background:linear-gradient(135deg,rgba(245,197,24,.05) 0%,rgba(0,0,0,0) 100%); }
.implied-pill { display:inline-flex; align-items:center; gap:.3rem; font-size:.65rem; color:var(--muted); background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:.15rem .5rem; }
</style>
@php
$ld = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type'       => 'Article',
            'headline'    => $fixture->home_team . ' vs ' . $fixture->away_team . ' — AI Betting Tips',
            'description' => 'AI betting tips and match analysis for ' . $fixture->home_team . ' vs ' . $fixture->away_team . ' on ' . $fixture->match_date->format('d M Y') . '.',
            'datePublished' => $fixture->created_at->toIso8601String(),
            'dateModified'  => $fixture->updated_at->toIso8601String(),
            'author'        => ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
            'publisher'     => ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
            'about' => [
                '@type'     => 'SportsEvent',
                'name'      => $fixture->home_team . ' vs ' . $fixture->away_team,
                'startDate' => $fixture->match_date->toIso8601String(),
                'sport'     => 'Football',
                'homeTeam'  => ['@type' => 'SportsTeam', 'name' => $fixture->home_team],
                'awayTeam'  => ['@type' => 'SportsTeam', 'name' => $fixture->away_team],
                'location'  => $fixture->venue ? ['@type' => 'Place', 'name' => $fixture->venue] : null,
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $fixture->home_team . ' vs ' . $fixture->away_team],
            ],
        ],
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@php
/* ═══ Global data prep ═══ */
$raw      = $fixture->raw_data ?? [];
$h2h      = $raw['h2h'] ?? [];
$homeForm = array_slice($raw['home_form'] ?? [], 0, 6);
$awayForm = array_slice($raw['away_form'] ?? [], 0, 6);
$hStats   = $raw['home_stats'] ?? [];
$aStats   = $raw['away_stats'] ?? [];
$odds     = $raw['odds'] ?? [];

$homeOdds = $fixture->home_odds   ?? ($odds['home_win'] ?? null);
$drawOdds = $fixture->draw_odds   ?? ($odds['draw']     ?? null);
$awayOdds = $fixture->away_odds   ?? ($odds['away_win'] ?? null);
$o25      = $fixture->over25_odds  ?? ($odds['over25']  ?? null);
$u25      = $fixture->under25_odds ?? ($odds['under25'] ?? null);
$bttsY    = $fixture->btts_yes_odds ?? ($odds['btts_yes'] ?? null);
$bttsN    = $fixture->btts_no_odds  ?? ($odds['btts_no']  ?? null);
$hasOdds  = $homeOdds || $o25 || $bttsY;

/* Implied probability from decimal odds */
$homeImplied = $homeOdds ? round(100 / $homeOdds) : null;
$drawImplied = $drawOdds ? round(100 / $drawOdds) : null;
$awayImplied = $awayOdds ? round(100 / $awayOdds) : null;

/* Form scoring */
$hfCount = array_count_values($homeForm);
$afCount = array_count_values($awayForm);
$homeFormPts = ($hfCount['W'] ?? 0) * 3 + ($hfCount['D'] ?? 0);
$awayFormPts = ($afCount['W'] ?? 0) * 3 + ($afCount['D'] ?? 0);
$maxPts = count($homeForm) * 3;

$formLabel = function(int $pts, int $max) {
    if ($max === 0) return ['—', 'var(--muted)'];
    $pct = $pts / $max;
    if ($pct >= .70) return ['🔥 Excellent', 'var(--accent)'];
    if ($pct >= .50) return ['👍 Good', 'var(--accent)'];
    if ($pct >= .33) return ['😐 Mixed', 'var(--accent2)'];
    return ['⚠️ Poor', '#ef4444'];
};
[$homeFormLabel, $homeFormColor] = $formLabel($homeFormPts, $maxPts);
[$awayFormLabel, $awayFormColor] = $formLabel($awayFormPts, $maxPts);

/* H2H win distribution from current home/away perspective */
$h2hCounts = ['home' => 0, 'draw' => 0, 'away' => 0];
foreach ($h2h as $hm) {
    if (!isset($hm['score'])) continue;
    $parts = array_map('intval', explode('-', (string)$hm['score']));
    if (count($parts) < 2) continue;
    [$sh, $sa] = $parts;
    if (isset($hm['home_team']) && $hm['home_team'] === $fixture->home_team) {
        if ($sh > $sa) $h2hCounts['home']++;
        elseif ($sh === $sa) $h2hCounts['draw']++;
        else $h2hCounts['away']++;
    } elseif (isset($hm['away_team']) && $hm['away_team'] === $fixture->home_team) {
        if ($sa > $sh) $h2hCounts['home']++;
        elseif ($sh === $sa) $h2hCounts['draw']++;
        else $h2hCounts['away']++;
    } else {
        if ($sh > $sa) $h2hCounts['home']++;
        elseif ($sh === $sa) $h2hCounts['draw']++;
        else $h2hCounts['away']++;
    }
}
$h2hTotal    = array_sum($h2hCounts);
$h2hHomePct  = $h2hTotal ? round($h2hCounts['home'] / $h2hTotal * 100) : 0;
$h2hDrawPct  = $h2hTotal ? round($h2hCounts['draw'] / $h2hTotal * 100) : 0;
$h2hAwayPct  = $h2hTotal ? 100 - $h2hHomePct - $h2hDrawPct : 0;

/* Top tips */
$topTip      = $tips->first();
$valueTips   = $tips->where('is_value_bet', true);
$highConfTips= $tips->where('confidence', '>=', 75);

/* League country */
$leagueCountry = ($fixture->league && $fixture->league->country instanceof \App\Models\Country)
    ? $fixture->league->country : null;

/* Stat comparison helper */
$statBar = function($hv, $av) {
    $hv = (float) $hv; $av = (float) $av;
    if ($hv == 0 && $av == 0) return [50, 50];
    $total = $hv + $av;
    return [round($hv / $total * 100), round($av / $total * 100)];
};

$formColor = fn($r) => match($r) { 'W' => 'var(--accent)', 'D' => 'var(--accent2)', default => '#ef4444' };
$formBg    = fn($r) => match($r) { 'W' => 'rgba(0,229,160,.15)', 'D' => 'rgba(245,197,24,.15)', default => 'rgba(239,68,68,.15)' };
@endphp

<div class="scout-page-wrap" style="max-width:1280px;margin:0 auto;padding:1.5rem 2rem">

    {{-- Breadcrumb --}}
    <div style="font-size:.75rem;color:var(--muted);margin-bottom:1.25rem">
        <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
        <span style="margin:0 .4rem">›</span>
        @if($fixture->league)
        <a href="{{ route('league.show', $fixture->league) }}" style="color:var(--muted);text-decoration:none">{{ $fixture->league->name }}</a>
        <span style="margin:0 .4rem">›</span>
        @endif
        <span style="color:var(--text)">{{ $fixture->home_team }} vs {{ $fixture->away_team }}</span>
    </div>

    <div class="welcome-grid" style="gap:1.5rem">

    {{-- ════════════════════ LEFT COLUMN ════════════════════ --}}
    <div>


        {{-- ══ 1. Match Header ══ --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1.25rem">

            @if($fixture->league)
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(0,229,160,.08);padding:.2rem .6rem;border-radius:3px;margin-bottom:.75rem">
                @if($leagueCountry?->flag_url)
                <img src="{{ $leagueCountry->flag_url }}" alt="{{ $leagueCountry->name }}" style="width:18px;height:13px;object-fit:cover;border-radius:2px;flex-shrink:0">
                @endif
                <span style="font-size:.7rem;color:var(--accent);letter-spacing:.06em">{{ $fixture->league->name }}{{ $leagueCountry ? ' · ' . $leagueCountry->name : '' }}</span>
            </div>
            @endif

            {{-- Teams --}}
            <div style="display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:.75rem;margin-bottom:.85rem">
                <div style="display:flex;align-items:center;gap:.6rem">
                    @if($fixture->home_logo)
                    <img src="{{ $fixture->home_logo }}" alt="{{ $fixture->home_team }}" style="width:44px;height:44px;object-fit:contain;flex-shrink:0">
                    @endif
                    <div>
                        <h1 style="font-family:var(--fh);font-size:1.4rem;letter-spacing:.06em;color:var(--text);margin:0;line-height:1.1">{{ $fixture->home_team }}</h1>
                        @if($maxPts > 0)
                        <div style="font-size:.68rem;color:{{ $homeFormColor }};margin-top:.2rem;font-weight:600">{{ $homeFormLabel }} · {{ $homeFormPts }}/{{ $maxPts }} pts</div>
                        @endif
                    </div>
                </div>
                <div style="text-align:center">
                    @if($fixture->status === 'FT' && $fixture->score_home !== null)
                    <div style="font-family:var(--fm);font-size:1.5rem;font-weight:700;color:var(--text);line-height:1">{{ $fixture->score_home }}–{{ $fixture->score_away }}</div>
                    <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Full Time</div>
                    @if($fixture->halftime_home !== null)
                    <div style="font-size:.62rem;color:var(--muted)">(HT: {{ $fixture->halftime_home }}–{{ $fixture->halftime_away }})</div>
                    @endif
                    @else
                    <div style="font-size:.72rem;color:var(--muted);font-weight:600;letter-spacing:.08em">VS</div>
                    <div style="font-size:.72rem;color:var(--accent);margin-top:.2rem;font-family:var(--fm)">{{ $fixture->match_date->format('H:i') }}</div>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:.6rem;justify-content:flex-end">
                    <div style="text-align:right">
                        <h1 style="font-family:var(--fh);font-size:1.4rem;letter-spacing:.06em;color:var(--text);margin:0;line-height:1.1">{{ $fixture->away_team }}</h1>
                        @if($maxPts > 0)
                        <div style="font-size:.68rem;color:{{ $awayFormColor }};margin-top:.2rem;font-weight:600;text-align:right">{{ $awayFormLabel }} · {{ $awayFormPts }}/{{ $maxPts }} pts</div>
                        @endif
                    </div>
                    @if($fixture->away_logo)
                    <img src="{{ $fixture->away_logo }}" alt="{{ $fixture->away_team }}" style="width:44px;height:44px;object-fit:contain;flex-shrink:0">
                    @endif
                </div>
            </div>

            {{-- Win probability bar --}}
            @if($fixture->prediction_percent_home !== null)
            @php $ph = $fixture->prediction_percent_home; $pd = $fixture->prediction_percent_draw; $pa = $fixture->prediction_percent_away; @endphp
            <div style="margin-bottom:.85rem">
                <div style="display:flex;justify-content:space-between;font-size:.68rem;color:var(--muted);margin-bottom:.3rem">
                    <span>Home {{ $ph }}%</span>
                    <span>Draw {{ $pd }}%</span>
                    <span>Away {{ $pa }}%</span>
                </div>
                <div style="display:flex;height:8px;border-radius:4px;overflow:hidden;gap:2px">
                    <div style="width:{{ $ph }}%;background:var(--accent);border-radius:4px 0 0 4px"></div>
                    <div style="width:{{ $pd }}%;background:var(--dim)"></div>
                    <div style="width:{{ $pa }}%;background:var(--accent2);border-radius:0 4px 4px 0"></div>
                </div>
            </div>
            @endif

            {{-- Meta --}}
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.6rem;font-size:.75rem;color:var(--muted);padding-top:.75rem;border-top:1px solid var(--border)">
                <span>🗓 {{ $fixture->match_date->format('D d M Y, H:i') }}</span>
                @if($fixture->venue)<span>📍 {{ $fixture->venue }}{{ $fixture->venue_city ? ', ' . $fixture->venue_city : '' }}</span>@endif
                @if($fixture->round)<span>🏆 {{ $fixture->round }}</span>@endif
                @if($fixture->referee)<span>👨‍⚖️ {{ $fixture->referee }}</span>@endif
            </div>

        </div>

        {{-- ══ 2. Our AI Picks ══ --}}
        @if($tips->isNotEmpty())
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:linear-gradient(90deg,rgba(0,229,160,.12),rgba(0,229,160,.03));border-bottom:1px solid rgba(0,229,160,.2);display:flex;align-items:center;justify-content:space-between">
                <div>
                    <span style="font-family:var(--fh);font-size:1rem;letter-spacing:.06em;color:var(--accent)">🎯 Our AI Picks for This Match</span>
                    <div style="font-size:.7rem;color:var(--muted);margin-top:.1rem">AI-ranked by confidence · only the bets worth considering</div>
                </div>
                <span style="font-family:var(--fm);font-size:.72rem;color:var(--accent);background:rgba(0,229,160,.1);border:1px solid rgba(0,229,160,.3);padding:.3rem .7rem;border-radius:20px;white-space:nowrap">{{ $tips->count() }} {{ Str::plural('tip', $tips->count()) }}</span>
            </div>
            <div style="padding:.85rem 1rem">
            @foreach($tips as $t)
            @php
                $isHigh = $t->confidence >= 75;
                $isMid  = $t->confidence >= 65 && !$isHigh;
                $confColor = $isHigh ? 'var(--accent)' : ($isMid ? 'var(--accent2)' : 'var(--muted)');
                $marketHint = match(true) {
                    str_contains(strtolower($t->market ?? ''), '1x2')         => 'Bet on the match result — Home Win, Draw or Away Win',
                    str_contains(strtolower($t->market ?? ''), 'over')        => 'Bet on whether total goals scored exceed the line',
                    str_contains(strtolower($t->market ?? ''), 'under')       => 'Bet on whether total goals scored stay below the line',
                    str_contains(strtolower($t->market ?? ''), 'btts')        => 'Both teams must score at least 1 goal each',
                    str_contains(strtolower($t->market ?? ''), 'both teams')  => 'Both teams must score at least 1 goal each',
                    str_contains(strtolower($t->market ?? ''), 'asian')       => 'Asian handicap — levels the playing field between teams',
                    str_contains(strtolower($t->market ?? ''), 'double')      => 'Double Chance — covers two of the three possible outcomes',
                    str_contains(strtolower($t->market ?? ''), 'draw no bet') => 'Your stake is returned if the match ends in a draw',
                    default => 'Place as a single bet on any major bookmaker',
                };
            @endphp
            <div class="bt-pick-card{{ $t->is_value_bet ? ' value' : '' }}">
                <div style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:start">
                    <div>
                        {{-- Badges --}}
                        <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.4rem;flex-wrap:wrap">
                            <span style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);background:var(--surface);padding:.15rem .5rem;border-radius:3px">{{ $t->market }}</span>
                            @if($t->is_value_bet)<span style="font-size:.65rem;color:var(--accent2);background:rgba(245,197,24,.12);border:1px solid rgba(245,197,24,.3);padding:.15rem .5rem;border-radius:3px;letter-spacing:.06em">⭐ VALUE BET</span>@endif
                            @if($isHigh)<span style="font-size:.65rem;color:var(--accent);background:rgba(0,229,160,.1);border:1px solid rgba(0,229,160,.25);padding:.15rem .5rem;border-radius:3px">HIGH CONFIDENCE</span>@endif
                        </div>

                        {{-- Selection --}}
                        <div style="font-family:var(--fh);font-size:1.65rem;letter-spacing:.06em;color:{{ $confColor }};margin-bottom:.25rem;line-height:1.1">{{ $t->selection }}</div>

                        {{-- Odds + implied --}}
                        @if($t->odds)
                        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:.5rem">
                            <div>
                                <div style="font-size:.58rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Odds</div>
                                <div style="font-family:var(--fm);font-size:1.2rem;color:var(--accent2);font-weight:700;line-height:1">{{ number_format($t->odds, 2) }}</div>
                            </div>
                            <div>
                                <div style="font-size:.58rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Implied chance</div>
                                <div style="font-family:var(--fm);font-size:1.1rem;color:var(--text);font-weight:600;line-height:1">≈{{ round(100/$t->odds) }}%</div>
                            </div>
                        </div>
                        @endif

                        {{-- Reasoning --}}
                        @if($t->reasoning)
                        <p style="font-size:.83rem;color:var(--muted);line-height:1.75;margin:0 0 .6rem">{{ $t->reasoning }}</p>
                        @endif

                        {{-- Market hint --}}
                        <div style="display:inline-flex;align-items:center;gap:.35rem;font-size:.7rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:4px;padding:.3rem .6rem">
                            💡 {{ $marketHint }}
                        </div>
                    </div>

                    {{-- Confidence ring --}}
                    <div style="text-align:center;min-width:78px">
                        <div style="width:74px;height:74px;border-radius:50%;background:conic-gradient({{ $confColor }} {{ $t->confidence }}%, var(--surface) {{ $t->confidence }}%);display:flex;align-items:center;justify-content:center;margin:0 auto .4rem">
                            <div style="width:56px;height:56px;border-radius:50%;background:var(--card);display:flex;flex-direction:column;align-items:center;justify-content:center">
                                <span style="font-family:var(--fm);font-size:1rem;font-weight:700;color:{{ $confColor }};line-height:1">{{ $t->confidence }}%</span>
                                <span style="font-size:.5rem;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;line-height:1;margin-top:.1rem">conf.</span>
                            </div>
                        </div>
                        <div style="font-size:.62rem;color:{{ $confColor }};text-align:center;font-weight:600">
                            {{ $isHigh ? 'Strong' : ($isMid ? 'Decent' : 'Speculative') }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
            {{-- Summary footer --}}
            <div style="padding:.75rem 1rem;border-top:1px solid var(--border);background:var(--card2);display:flex;flex-wrap:wrap;gap:.75rem;align-items:center">
                @if($highConfTips->count() > 0)
                <div style="font-size:.75rem;color:var(--muted)">✅ <strong style="color:var(--accent)">{{ $highConfTips->count() }}</strong> high-confidence {{ Str::plural('pick', $highConfTips->count()) }} (75%+)</div>
                @endif
                @if($valueTips->count() > 0)
                <div style="font-size:.75rem;color:var(--muted)">⭐ <strong style="color:var(--accent2)">{{ $valueTips->count() }}</strong> value {{ Str::plural('bet', $valueTips->count()) }} identified</div>
                @endif
                <div style="font-size:.7rem;color:var(--muted);margin-left:auto;opacity:.7">All tips generated by AI · Gamble responsibly</div>
            </div>
        </div>
        @else
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem 1.4rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem">
            <div style="font-size:2rem;flex-shrink:0">🤖</div>
            <div>
                <div style="font-family:var(--fh);font-size:1rem;letter-spacing:.06em;color:var(--text);margin-bottom:.25rem">AI Tips Not Available Yet</div>
                <p style="font-size:.82rem;color:var(--muted);margin:0;line-height:1.6">Our AI hasn't analysed this match yet. The scheduler runs daily — check back closer to kick-off.</p>
            </div>
        </div>
        @endif

        {{-- ══ 3. Odds & Implied Probability ══ --}}
        @if($hasOdds)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">📊 Odds &amp; Implied Probability</span>
                <span style="font-size:.65rem;color:var(--muted)">Opening odds · % = bookmaker's implied likelihood</span>
            </div>
            <div style="padding:.9rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">

                @if($homeOdds || $drawOdds || $awayOdds)
                <div>
                    <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.5rem">Match Result — 1X2</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem">
                        @foreach([[$fixture->home_team,$homeOdds,$homeImplied,'var(--accent)'],['Draw',$drawOdds,$drawImplied,'var(--dim)'],[$fixture->away_team,$awayOdds,$awayImplied,'var(--accent2)']] as [$label,$odd,$impl,$color])
                        @if($odd)
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:.7rem .5rem;text-align:center;position:relative;overflow:hidden">
                            @if($impl)<div style="position:absolute;bottom:0;left:0;height:3px;width:{{ $impl }}%;background:{{ $color }};opacity:.7"></div>@endif
                            <div style="font-size:.65rem;color:var(--muted);margin-bottom:.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $label }}</div>
                            <div style="font-family:var(--fm);font-size:1.25rem;font-weight:700;color:{{ $color }}">{{ number_format($odd, 2) }}</div>
                            @if($impl)<div class="implied-pill" style="margin:.25rem auto 0">≈{{ $impl }}% chance</div>@endif
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @if($homeImplied && $drawImplied && $awayImplied)
                    @php $overround = $homeImplied + $drawImplied + $awayImplied; @endphp
                    <div style="font-size:.65rem;color:var(--muted);margin-top:.4rem;text-align:right">
                        Bookmaker margin: <strong>{{ $overround - 100 }}%</strong> · Lower margin = better value for bettors
                    </div>
                    @endif
                </div>
                @endif

                @if($o25 || $u25)
                <div>
                    <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.5rem">Goals Total — Over / Under 2.5</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem">
                        @foreach([['Over 2.5 goals',$o25,'var(--accent)'],['Under 2.5 goals',$u25,'var(--accent2)']] as [$label,$odd,$color])
                        @if($odd)
                        @php $impl = round(100/$odd); @endphp
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:.7rem .5rem;text-align:center;position:relative;overflow:hidden">
                            <div style="position:absolute;bottom:0;left:0;height:3px;width:{{ $impl }}%;background:{{ $color }};opacity:.7"></div>
                            <div style="font-size:.65rem;color:var(--muted);margin-bottom:.25rem">{{ $label }}</div>
                            <div style="font-family:var(--fm);font-size:1.15rem;font-weight:700;color:{{ $color }}">{{ number_format($odd, 2) }}</div>
                            <div class="implied-pill" style="margin:.25rem auto 0">≈{{ $impl }}% chance</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($bttsY || $bttsN)
                <div>
                    <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.5rem">Both Teams To Score (BTTS)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem">
                        @foreach([['Yes — both score ✓',$bttsY,'var(--accent)'],['No — clean sheet ✗',$bttsN,'var(--accent2)']] as [$label,$odd,$color])
                        @if($odd)
                        @php $impl = round(100/$odd); @endphp
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:.7rem .5rem;text-align:center;position:relative;overflow:hidden">
                            <div style="position:absolute;bottom:0;left:0;height:3px;width:{{ $impl }}%;background:{{ $color }};opacity:.7"></div>
                            <div style="font-size:.65rem;color:var(--muted);margin-bottom:.25rem">{{ $label }}</div>
                            <div style="font-family:var(--fm);font-size:1.15rem;font-weight:700;color:{{ $color }}">{{ number_format($odd, 2) }}</div>
                            <div class="implied-pill" style="margin:.25rem auto 0">≈{{ $impl }}% chance</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($odds['over15']) || !empty($odds['over35']))
                <div>
                    <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.4rem">Other Totals</div>
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem">
                        @foreach([['Over 1.5','over15'],['Under 1.5','under15'],['Over 3.5','over35'],['Under 3.5','under35'],['Over 4.5','over45'],['Under 4.5','under45']] as [$label,$key])
                        @if(!empty($odds[$key]))
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.4rem .65rem;text-align:center;min-width:72px">
                            <div style="font-size:.62rem;color:var(--muted);margin-bottom:.15rem">{{ $label }}</div>
                            <div style="font-family:var(--fm);font-size:.9rem;font-weight:700;color:var(--text)">{{ number_format($odds[$key], 2) }}</div>
                            <div style="font-size:.6rem;color:var(--muted)">≈{{ round(100/$odds[$key]) }}%</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($odds['dc_home_draw']) || !empty($odds['dc_away_draw']))
                <div>
                    <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.4rem">Double Chance</div>
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem">
                        @foreach([['Home or Draw','dc_home_draw'],['Away or Draw','dc_away_draw'],['Home or Away','dc_home_away']] as [$label,$key])
                        @if(!empty($odds[$key]))
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.4rem .65rem;text-align:center;min-width:90px">
                            <div style="font-size:.62rem;color:var(--muted);margin-bottom:.15rem">{{ $label }}</div>
                            <div style="font-family:var(--fm);font-size:.9rem;font-weight:700;color:var(--text)">{{ number_format($odds[$key], 2) }}</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endif

        {{-- ══ 4. Form & H2H ══ --}}
        @if($h2hTotal > 0 || $homeForm || $awayForm)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">🏟 Form &amp; Head-to-Head</span>
            </div>
            <div style="padding:1rem 1.1rem">

                @if($homeForm || $awayForm)
                <div style="margin-bottom:1.25rem">
                    <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.6rem">Recent Form (last {{ max(count($homeForm), count($awayForm)) }} games · W=Win D=Draw L=Loss)</div>
                    <div style="display:flex;flex-direction:column;gap:.55rem">
                        @foreach([[$fixture->home_team,$homeForm,$homeFormLabel,$homeFormColor,$homeFormPts],[$fixture->away_team,$awayForm,$awayFormLabel,$awayFormColor,$awayFormPts]] as [$team,$form,$label,$color,$pts])
                        @if($form)
                        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
                            <span style="font-size:.78rem;color:var(--text);min-width:120px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:600">{{ $team }}</span>
                            <div style="display:flex;gap:3px">
                                @foreach($form as $r)
                                <span style="width:26px;height:26px;border-radius:4px;background:{{ $formBg($r) }};border:1px solid {{ $formColor($r) }};color:{{ $formColor($r) }};font-size:.68rem;font-weight:700;display:flex;align-items:center;justify-content:center" title="{{ $r === 'W' ? 'Win' : ($r === 'D' ? 'Draw' : 'Loss') }}">{{ $r }}</span>
                                @endforeach
                            </div>
                            <span style="font-size:.72rem;color:{{ $color }};font-weight:600">{{ $label }}</span>
                            @if($maxPts > 0)<span style="font-size:.68rem;color:var(--muted)">({{ $pts }}/{{ $maxPts }} pts)</span>@endif
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($h2hTotal > 0)
                <div>
                    <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.6rem">Head-to-Head — Last {{ $h2hTotal }} meetings</div>

                    {{-- H2H stacked bar --}}
                    <div style="margin-bottom:.85rem">
                        <div style="display:flex;height:30px;border-radius:6px;overflow:hidden;gap:2px;margin-bottom:.35rem">
                            @if($h2hCounts['home'] > 0)
                            <div style="flex:{{ $h2hCounts['home'] }};background:var(--accent);display:flex;align-items:center;justify-content:center;font-family:var(--fm);font-size:.82rem;font-weight:700;color:#07090e;min-width:24px" title="{{ $fixture->home_team }} wins">{{ $h2hCounts['home'] }}</div>
                            @endif
                            @if($h2hCounts['draw'] > 0)
                            <div style="flex:{{ $h2hCounts['draw'] }};background:var(--dim);display:flex;align-items:center;justify-content:center;font-family:var(--fm);font-size:.82rem;font-weight:700;color:#07090e;min-width:24px" title="Draws">{{ $h2hCounts['draw'] }}</div>
                            @endif
                            @if($h2hCounts['away'] > 0)
                            <div style="flex:{{ $h2hCounts['away'] }};background:var(--accent2);display:flex;align-items:center;justify-content:center;font-family:var(--fm);font-size:.82rem;font-weight:700;color:#07090e;min-width:24px" title="{{ $fixture->away_team }} wins">{{ $h2hCounts['away'] }}</div>
                            @endif
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.68rem">
                            <span style="color:var(--accent)">{{ $fixture->home_team }}: {{ $h2hCounts['home'] }}W ({{ $h2hHomePct }}%)</span>
                            <span style="color:var(--dim)">Draws: {{ $h2hCounts['draw'] }} ({{ $h2hDrawPct }}%)</span>
                            <span style="color:var(--accent2)">{{ $fixture->away_team }}: {{ $h2hCounts['away'] }}W ({{ $h2hAwayPct }}%)</span>
                        </div>
                    </div>

                    {{-- H2H match list --}}
                    @if($h2h)
                    <div style="display:flex;flex-direction:column;gap:3px">
                        @foreach(array_slice($h2h, 0, 5) as $hm)
                        @php
                            $hmParts = isset($hm['score']) ? array_map('intval', explode('-', (string)$hm['score'])) : [null,null];
                            $hmSh = $hmParts[0] ?? null; $hmSa = $hmParts[1] ?? null;
                            $hmHomeWon = $hmSh !== null && $hmSh > $hmSa;
                            $hmAwayWon = $hmSa !== null && $hmSa > $hmSh;
                        @endphp
                        <div style="display:grid;grid-template-columns:75px 1fr auto;align-items:center;gap:.5rem;padding:.35rem .5rem;background:var(--surface);border-radius:4px;font-size:.74rem">
                            <span style="color:var(--muted)">{{ \Carbon\Carbon::parse($hm['date'])->format('M Y') }}</span>
                            <span style="color:var(--text)">
                                <span style="color:{{ $hmHomeWon ? 'var(--accent)' : 'inherit' }};font-weight:{{ $hmHomeWon ? '700' : '400' }}">{{ $hm['home_team'] }}</span>
                                <span style="color:var(--muted)"> vs </span>
                                <span style="color:{{ $hmAwayWon ? 'var(--accent2)' : 'inherit' }};font-weight:{{ $hmAwayWon ? '700' : '400' }}">{{ $hm['away_team'] }}</span>
                            </span>
                            <span style="font-family:var(--fm);font-weight:700;color:var(--text);white-space:nowrap">{{ $hm['score'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

            </div>
        </div>
        @endif

        {{-- ══ 5. Season Stats Comparison ══ --}}
        @if($hStats && $aStats)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">📈 Season Stats Comparison</span>
                <span style="font-size:.68rem;color:var(--muted);margin-left:.5rem">▲ = statistical edge</span>
            </div>
            <div style="padding:.85rem 1rem">
                {{-- Headers --}}
                <div class="bt-stat-bar-wrap" style="margin-bottom:.2rem">
                    <div style="text-align:right;font-size:.72rem;color:var(--accent);font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $fixture->home_team }}</div>
                    <div></div>
                    <div style="font-size:.72rem;color:var(--accent2);font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $fixture->away_team }}</div>
                </div>
                @php
                    $statRows = [
                        ['Goals Scored',   'goals_scored',   false, '⚽'],
                        ['Goals Conceded', 'goals_conceded', true,  '🥅'],
                        ['Wins',           'wins',           false, '🏆'],
                        ['Clean Sheets',   'clean_sheets',   false, '🧤'],
                        ['Over 2.5 games', 'over25_count',   false, '📈'],
                        ['BTTS games',     'btts_count',     false, '🔄'],
                        ['League Position','position',       true,  '🏅'],
                    ];
                    $homeAdvCount = 0; $awayAdvCount = 0;
                    foreach ($statRows as [$l,$k,$lb]) {
                        if (!isset($hStats[$k]) || !isset($aStats[$k])) continue;
                        $h = (float)$hStats[$k]; $a = (float)$aStats[$k];
                        if ($lb ? $h < $a : $h > $a) $homeAdvCount++;
                        elseif ($lb ? $a < $h : $a > $h) $awayAdvCount++;
                    }
                @endphp
                @foreach($statRows as [$label,$key,$lowerBetter,$icon])
                @if(isset($hStats[$key]) && isset($aStats[$key]))
                @php
                    $hv = (float)$hStats[$key]; $av = (float)$aStats[$key];
                    [$hw,$aw] = $statBar($hv,$av);
                    $hEdge = $lowerBetter ? $hv < $av : $hv > $av;
                    $aEdge = $lowerBetter ? $av < $hv : $av > $hv;
                @endphp
                <div class="bt-stat-bar-wrap">
                    <div>
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:.3rem;margin-bottom:.2rem">
                            <span style="font-family:var(--fm);font-size:.82rem;font-weight:700;color:{{ $hEdge ? 'var(--accent)' : 'var(--text)' }}">{{ $hStats[$key] }}</span>
                            @if($hEdge)<span style="font-size:.58rem;color:var(--accent)">▲</span>@endif
                        </div>
                        <div class="bt-bar-h"><div class="bt-bar-fill-h" style="width:{{ $hw }}%;opacity:{{ $hEdge ? '1' : '.35' }}"></div></div>
                    </div>
                    <div style="text-align:center;font-size:.62rem;color:var(--muted);line-height:1.25">{{ $icon }}<br>{{ $label }}</div>
                    <div>
                        <div style="display:flex;align-items:center;gap:.3rem;margin-bottom:.2rem">
                            @if($aEdge)<span style="font-size:.58rem;color:var(--accent2)">▲</span>@endif
                            <span style="font-family:var(--fm);font-size:.82rem;font-weight:700;color:{{ $aEdge ? 'var(--accent2)' : 'var(--text)' }}">{{ $aStats[$key] }}</span>
                        </div>
                        <div class="bt-bar-a"><div class="bt-bar-fill-a" style="width:{{ $aw }}%;opacity:{{ $aEdge ? '1' : '.35' }}"></div></div>
                    </div>
                </div>
                @endif
                @endforeach

                {{-- Edge summary --}}
                <div style="margin-top:.85rem;padding:.6rem .75rem;background:var(--surface);border-radius:6px;font-size:.75rem;color:var(--muted);text-align:center">
                    @if($homeAdvCount > $awayAdvCount)
                    📊 <strong style="color:var(--accent)">{{ $fixture->home_team }}</strong> leads on {{ $homeAdvCount }}/{{ $homeAdvCount + $awayAdvCount }} comparable stats
                    @elseif($awayAdvCount > $homeAdvCount)
                    📊 <strong style="color:var(--accent2)">{{ $fixture->away_team }}</strong> leads on {{ $awayAdvCount }}/{{ $homeAdvCount + $awayAdvCount }} comparable stats
                    @else
                    📊 Both teams are evenly matched across key season stats
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ══ 6. Statistical Predictions ══ --}}
        @if($fixture->prediction_percent_home || $fixture->prediction_winner)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">🔮 Statistical Model Predictions</span>
                <span style="font-size:.68rem;color:var(--muted);margin-left:.5rem">API-Football algorithm — reference data</span>
            </div>
            <div style="padding:1rem 1.1rem">

                @if($fixture->prediction_percent_home !== null)
                @php $ph = $fixture->prediction_percent_home; $pd = $fixture->prediction_percent_draw; $pa = $fixture->prediction_percent_away; @endphp
                <div style="margin-bottom:1rem">
                    <div style="display:flex;justify-content:space-between;font-size:.72rem;margin-bottom:.5rem">
                        <div>
                            <div style="color:var(--muted)">Home Win</div>
                            <div style="font-family:var(--fm);font-size:1.15rem;font-weight:700;color:var(--accent)">{{ $ph }}%</div>
                        </div>
                        <div style="text-align:center">
                            <div style="color:var(--muted)">Draw</div>
                            <div style="font-family:var(--fm);font-size:1.15rem;font-weight:700;color:var(--dim)">{{ $pd }}%</div>
                        </div>
                        <div style="text-align:right">
                            <div style="color:var(--muted)">Away Win</div>
                            <div style="font-family:var(--fm);font-size:1.15rem;font-weight:700;color:var(--accent2)">{{ $pa }}%</div>
                        </div>
                    </div>
                    <div style="display:flex;height:12px;border-radius:6px;overflow:hidden;gap:2px">
                        <div style="width:{{ $ph }}%;background:var(--accent);border-radius:6px 0 0 6px"></div>
                        <div style="width:{{ $pd }}%;background:var(--dim)"></div>
                        <div style="width:{{ $pa }}%;background:var(--accent2);border-radius:0 6px 6px 0"></div>
                    </div>
                </div>
                @endif

                <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.85rem">
                    @if($fixture->prediction_winner)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(0,229,160,.08);border:1px solid rgba(0,229,160,.3);border-radius:20px;padding:.3rem .8rem;font-size:.75rem">
                        <span style="color:var(--muted)">Statistical favourite:</span>
                        <span style="color:var(--accent);font-weight:700">{{ $fixture->prediction_winner }}</span>
                    </span>
                    @endif
                    @if($fixture->prediction_under_over)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(245,197,24,.08);border:1px solid rgba(245,197,24,.3);border-radius:20px;padding:.3rem .8rem;font-size:.75rem">
                        <span style="color:var(--muted)">Goals signal:</span>
                        <span style="color:var(--accent2);font-weight:700">{{ $fixture->prediction_under_over }}</span>
                    </span>
                    @endif
                </div>

                @php $cmp = ($fixture->raw_data['predictions']['comparison'] ?? []); @endphp
                @if(!empty($cmp['total']))
                <div style="padding-top:.75rem;border-top:1px solid var(--border)">
                    <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.6rem">Category Comparison</div>
                    @foreach([['Form','form','📋'],['Attack','att','⚔️'],['Defence','def','🛡'],['H2H','h2h','🔁'],['Overall','total','🎯']] as [$label,$key,$icon])
                    @if(!empty($cmp[$key]['home']) && !empty($cmp[$key]['away']))
                    @php $hv = (int) rtrim($cmp[$key]['home'],'%'); $av = (int) rtrim($cmp[$key]['away'],'%'); @endphp
                    <div class="bt-stat-bar-wrap" style="margin-bottom:.35rem">
                        <div>
                            <div style="text-align:right;font-family:var(--fm);font-size:.75rem;font-weight:700;color:{{ $hv > $av ? 'var(--accent)' : 'var(--muted)' }};margin-bottom:.2rem">{{ $cmp[$key]['home'] }}</div>
                            <div class="bt-bar-h"><div class="bt-bar-fill-h" style="width:{{ $hv }}%;opacity:{{ $hv > $av ? '1' : '.35' }}"></div></div>
                        </div>
                        <div style="text-align:center;font-size:.62rem;color:var(--muted)">{{ $icon }} {{ $label }}</div>
                        <div>
                            <div style="font-family:var(--fm);font-size:.75rem;font-weight:700;color:{{ $av > $hv ? 'var(--accent2)' : 'var(--muted)' }};margin-bottom:.2rem">{{ $cmp[$key]['away'] }}</div>
                            <div class="bt-bar-a"><div class="bt-bar-fill-a" style="width:{{ $av }}%;opacity:{{ $av > $hv ? '1' : '.35' }}"></div></div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif

            </div>
        </div>
        @endif

        {{-- ══ 7. Confirmed Lineups ══ --}}
        @php $lineups = $raw['lineups'] ?? []; @endphp
        @if(!empty($lineups['home']['start_xi']))
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">📋 Confirmed Lineups</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                @foreach(['home', 'away'] as $side)
                @php $team = $lineups[$side]; @endphp
                <div style="padding:.75rem .9rem;{{ $side === 'home' ? 'border-right:1px solid var(--border)' : '' }}">
                    <div style="font-size:.72rem;font-weight:700;color:var(--{{ $side === 'home' ? 'accent' : 'accent2' }});margin-bottom:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $team['team'] }}</div>
                    @if($team['formation'])<div style="font-size:.65rem;color:var(--muted);margin-bottom:.5rem;font-family:var(--fm)">Formation: {{ $team['formation'] }}</div>@endif
                    @foreach($team['start_xi'] as $p)
                    <div style="display:flex;align-items:center;gap:.35rem;padding:.2rem 0;border-bottom:1px solid rgba(255,255,255,.04)">
                        <span style="font-family:var(--fm);font-size:.63rem;color:var(--muted);min-width:16px;text-align:center">{{ $p['number'] }}</span>
                        <span style="font-size:.72rem;color:var(--text);flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $p['name'] }}</span>
                        <span style="font-size:.58rem;color:var(--muted);background:var(--surface);padding:.1rem .3rem;border-radius:3px">{{ $p['pos'] }}</span>
                    </div>
                    @endforeach
                    @if(!empty($team['substitutes']))
                    <div style="font-size:.6rem;color:var(--muted);margin-top:.4rem;margin-bottom:.2rem;text-transform:uppercase;letter-spacing:.06em">Subs</div>
                    @foreach($team['substitutes'] as $p)
                    <div style="display:flex;align-items:center;gap:.35rem;padding:.15rem 0;opacity:.6">
                        <span style="font-family:var(--fm);font-size:.6rem;color:var(--muted);min-width:16px;text-align:center">{{ $p['number'] }}</span>
                        <span style="font-size:.68rem;color:var(--muted);flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $p['name'] }}</span>
                        <span style="font-size:.56rem;color:var(--muted)">{{ $p['pos'] }}</span>
                    </div>
                    @endforeach
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- end left --}}

    {{-- ════════════════════ RIGHT SIDEBAR ════════════════════ --}}
    <div class="welcome-sidebar">

        {{-- Match Verdict --}}
        @if($tips->isNotEmpty())
        <div style="background:linear-gradient(135deg,#0d1f18 0%,#0a1a25 100%);border:1px solid rgba(0,229,160,.3);border-radius:8px;padding:1.1rem;margin-bottom:1.25rem">
            <div style="font-size:.65rem;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);font-weight:700;margin-bottom:.6rem">🎯 AI Match Verdict</div>
            @if($topTip)
            <div style="font-size:.73rem;color:var(--muted);margin-bottom:.2rem">Top recommended bet:</div>
            <div style="font-family:var(--fh);font-size:1.45rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.15rem;line-height:1.1">{{ $topTip->selection }}</div>
            <div style="font-size:.72rem;color:var(--muted);margin-bottom:.75rem">{{ $topTip->market }} · {{ $topTip->confidence }}% confidence</div>
            @if($topTip->odds)
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:.75rem">
                <div style="background:rgba(0,229,160,.1);border:1px solid rgba(0,229,160,.2);border-radius:6px;padding:.5rem;text-align:center">
                    <div style="font-size:.6rem;color:var(--muted)">Odds</div>
                    <div style="font-family:var(--fm);font-size:1.2rem;color:var(--accent2);font-weight:700">{{ number_format($topTip->odds, 2) }}</div>
                </div>
                <div style="background:rgba(0,229,160,.1);border:1px solid rgba(0,229,160,.2);border-radius:6px;padding:.5rem;text-align:center">
                    <div style="font-size:.6rem;color:var(--muted)">AI confidence</div>
                    <div style="font-family:var(--fm);font-size:1.2rem;color:var(--accent);font-weight:700">{{ $topTip->confidence }}%</div>
                </div>
            </div>
            @endif
            @endif
            @if($tips->count() > 1)
            <div style="font-size:.72rem;color:var(--muted);padding:.45rem .6rem;background:rgba(0,229,160,.05);border-radius:5px;border:1px solid rgba(0,229,160,.1)">
                + {{ $tips->count() - 1 }} more {{ Str::plural('tip', $tips->count()-1) }} — scroll down ↓
            </div>
            @endif
        </div>
        @endif

        {{-- FAN POLL --}}
        @php
            $pollRow    = DB::table('fixture_polls')->where('fixture_id', $fixture->id)->first();
            $pollHome   = (int)($pollRow->home_votes ?? 0);
            $pollDraw   = (int)($pollRow->draw_votes ?? 0);
            $pollAway   = (int)($pollRow->away_votes ?? 0);
            $pollTotal  = $pollHome + $pollDraw + $pollAway;
            $pollHomePct = $pollTotal ? round($pollHome / $pollTotal * 100) : 33;
            $pollDrawPct = $pollTotal ? round($pollDraw / $pollTotal * 100) : 34;
            $pollAwayPct = $pollTotal ? round($pollAway / $pollTotal * 100) : 33;
        @endphp
        <div id="poll-widget" style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">🗳️ Fan Poll</span>
                <span id="poll-total-label" style="font-size:.65rem;color:var(--muted);font-family:var(--fm)">{{ $pollTotal }} {{ Str::plural('vote', $pollTotal) }}</span>
            </div>
            <div style="padding:.85rem 1rem">
                <div style="font-size:.78rem;color:var(--muted);text-align:center;margin-bottom:.85rem">What's your prediction?</div>

                {{-- Honeypot (visually hidden, aria-hidden — bots fill it, humans never see it) --}}
                <form id="poll-form" style="display:contents" aria-hidden="false">
                    <input type="text" name="website" tabindex="-1" autocomplete="off"
                        aria-hidden="true" style="opacity:0;position:absolute;height:0;width:0;pointer-events:none">
                </form>

                {{-- Vote buttons --}}
                <div id="poll-buttons" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;margin-bottom:.9rem">
                    @foreach([
                        ['home', $fixture->home_team,  'var(--accent)',  'var(--accent)'],
                        ['draw', 'Draw',               'var(--dim)',     'var(--text)'],
                        ['away', $fixture->away_team,  'var(--accent2)', 'var(--accent2)'],
                    ] as [$choice, $label, $borderColor, $textColor])
                    <button
                        onclick="submitPoll('{{ $choice }}')"
                        data-choice="{{ $choice }}"
                        style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.6rem .4rem;cursor:pointer;transition:all .2s;color:var(--muted);font-family:var(--fh);font-size:.72rem;letter-spacing:.05em;line-height:1.3;text-align:center;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                        onmouseover="this.style.borderColor='{{ $borderColor }}';this.style.color='{{ $textColor }}'"
                        onmouseout="if(!this.classList.contains('poll-chosen')){this.style.borderColor='var(--border)';this.style.color='var(--muted)'}">
                        {{ Str::limit($label, 10) }}
                    </button>
                    @endforeach
                </div>

                {{-- Results bars --}}
                <div id="poll-results" style="display:flex;flex-direction:column;gap:.55rem">
                    @foreach([
                        ['home', $fixture->home_team,  $pollHomePct, 'var(--accent)'],
                        ['draw', 'Draw',               $pollDrawPct, 'rgba(255,255,255,.35)'],
                        ['away', $fixture->away_team,  $pollAwayPct, 'var(--accent2)'],
                    ] as [$key, $label, $pct, $color])
                    <div data-result="{{ $key }}">
                        <div style="display:flex;justify-content:space-between;font-size:.7rem;margin-bottom:.2rem">
                            <span style="color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:65%">{{ Str::limit($label, 14) }}</span>
                            <span data-pct="{{ $key }}" style="font-family:var(--fm);color:{{ $color }};font-weight:700">{{ $pct }}%</span>
                        </div>
                        <div style="height:6px;background:var(--surface);border-radius:3px;overflow:hidden">
                            <div data-bar="{{ $key }}" style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:3px;transition:width .5s ease"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="poll-msg" style="margin-top:.6rem;font-size:.68rem;color:var(--muted);text-align:center;min-height:1rem"></div>
            </div>
        </div>

        <script>
        (function(){
            var VOTE_URL    = '{{ route('fixture.poll.vote', $fixture) }}';
            var CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            var STORAGE_KEY = 'poll_voted_{{ $fixture->id }}';

            // Restore voted state from localStorage on page load
            var stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                showResults(JSON.parse(stored), stored !== 'already');
            }

            window.submitPoll = function(choice) {
                if (localStorage.getItem(STORAGE_KEY)) return;

                var body = new FormData();
                body.append('choice', choice);
                // honeypot field intentionally left blank

                fetch(VOTE_URL, {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body:    body,
                })
                .then(function(r){ return r.json(); })
                .then(function(data) {
                    if (data.error) {
                        document.getElementById('poll-msg').textContent = data.error;
                        return;
                    }
                    var payload = {
                        home_pct: data.home_pct,
                        draw_pct: data.draw_pct,
                        away_pct: data.away_pct,
                        total:    data.total,
                        choice:   data.your_choice ?? null,
                    };
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
                    showResults(payload, !data.already_voted);
                })
                .catch(function(){ document.getElementById('poll-msg').textContent = 'Could not save vote. Try again.'; });
            };

            function showResults(data, isNew) {
                // Highlight chosen button
                if (data.choice) {
                    var btn = document.querySelector('[data-choice="' + data.choice + '"]');
                    if (btn) {
                        var colors = { home:'var(--accent)', draw:'var(--text)', away:'var(--accent2)' };
                        btn.classList.add('poll-chosen');
                        btn.style.borderColor = colors[data.choice] ?? 'var(--accent)';
                        btn.style.color       = colors[data.choice] ?? 'var(--accent)';
                        btn.style.background  = 'rgba(255,255,255,.04)';
                    }
                }

                // Update bars and percentages
                ['home','draw','away'].forEach(function(k) {
                    var pct = data[k + '_pct'] ?? 33;
                    var bar = document.querySelector('[data-bar="' + k + '"]');
                    var lbl = document.querySelector('[data-pct="' + k + '"]');
                    if (bar) bar.style.width = pct + '%';
                    if (lbl) lbl.textContent = pct + '%';
                });

                // Update total label
                if (data.total !== undefined) {
                    var totalEl = document.getElementById('poll-total-label');
                    if (totalEl) totalEl.textContent = data.total + (data.total === 1 ? ' vote' : ' votes');
                }

                // Status message
                var msg = document.getElementById('poll-msg');
                if (msg) {
                    msg.textContent = isNew
                        ? '✓ Thanks for voting!'
                        : 'You already voted on this match.';
                    if (isNew) msg.style.color = 'var(--accent)';
                }
            }
        })();
        </script>

        {{-- How to use this page --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">📖 How to Use This Page</span>
            </div>
            <div style="padding:.75rem 1rem">
                <div class="bt-guide-step">
                    <div class="bt-guide-num">1</div>
                    <div>
                        <div style="font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.2rem">Check Our AI Picks</div>
                        <div style="font-size:.72rem;color:var(--muted);line-height:1.6">Look for tips with 75%+ confidence (green ring). These are our strongest signals. ⭐ Value Bet means the odds may be higher than expected.</div>
                    </div>
                </div>
                <div class="bt-guide-step">
                    <div class="bt-guide-num">2</div>
                    <div>
                        <div style="font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.2rem">Understand the Odds</div>
                        <div style="font-size:.72rem;color:var(--muted);line-height:1.6">Each odds cell shows the bookmaker's implied probability (%). Compare this to the AI's confidence — if AI confidence is higher than implied %, that's a potential value opportunity.</div>
                    </div>
                </div>
                <div class="bt-guide-step">
                    <div class="bt-guide-num">3</div>
                    <div>
                        <div style="font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.2rem">Study the Stats</div>
                        <div style="font-size:.72rem;color:var(--muted);line-height:1.6">The ▲ arrows show which team has the statistical edge. Check form, H2H history, and season stats before deciding. A 🔥 Excellent form team with H2H dominance is a stronger pick.</div>
                    </div>
                </div>
                <div class="bt-guide-step">
                    <div class="bt-guide-num">4</div>
                    <div>
                        <div style="font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.2rem">Place Your Bet</div>
                        <div style="font-size:.72rem;color:var(--muted);line-height:1.6">Use a bookmaker below. Find the match → go to the market shown on the tip → select the outcome → confirm your stake.</div>
                    </div>
                </div>
                <div style="margin-top:.5rem;padding:.5rem .6rem;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);border-radius:5px;font-size:.68rem;color:var(--muted);line-height:1.5">
                    ⚠️ <strong style="color:#ef4444">Gamble responsibly.</strong> Never bet more than you can afford to lose. Tips are AI-generated for entertainment — not guaranteed outcomes.
                </div>
            </div>
        </div>

        {{-- Confidence guide --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">🎚 Confidence Tiers</span>
            </div>
            <div style="padding:.75rem 1rem;display:flex;flex-direction:column;gap:.5rem">
                <div style="display:flex;align-items:center;gap:.6rem">
                    <div style="width:36px;height:36px;border-radius:50%;background:conic-gradient(var(--accent) 80%,var(--surface) 80%);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <div style="width:26px;height:26px;border-radius:50%;background:var(--card);display:flex;align-items:center;justify-content:center;font-size:.55rem;font-weight:700;color:var(--accent)">80%</div>
                    </div>
                    <div><div style="font-size:.78rem;font-weight:600;color:var(--accent)">75%+ — Strong Pick</div><div style="font-size:.68rem;color:var(--muted)">High AI conviction. Prioritise these.</div></div>
                </div>
                <div style="display:flex;align-items:center;gap:.6rem">
                    <div style="width:36px;height:36px;border-radius:50%;background:conic-gradient(var(--accent2) 70%,var(--surface) 70%);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <div style="width:26px;height:26px;border-radius:50%;background:var(--card);display:flex;align-items:center;justify-content:center;font-size:.55rem;font-weight:700;color:var(--accent2)">70%</div>
                    </div>
                    <div><div style="font-size:.78rem;font-weight:600;color:var(--accent2)">65–74% — Decent Pick</div><div style="font-size:.68rem;color:var(--muted)">Good signal, some uncertainty remains.</div></div>
                </div>
                <div style="display:flex;align-items:center;gap:.6rem">
                    <div style="width:36px;height:36px;border-radius:50%;background:conic-gradient(var(--muted) 55%,var(--surface) 55%);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <div style="width:26px;height:26px;border-radius:50%;background:var(--card);display:flex;align-items:center;justify-content:center;font-size:.55rem;font-weight:700;color:var(--muted)">55%</div>
                    </div>
                    <div><div style="font-size:.78rem;font-weight:600;color:var(--muted)">Below 65% — Speculative</div><div style="font-size:.68rem;color:var(--muted)">Lower confidence. Smaller stakes advised.</div></div>
                </div>
            </div>
        </div>

        {{-- Market glossary --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">📚 Market Glossary</span>
            </div>
            <div style="padding:.65rem 1rem;font-size:.72rem;color:var(--muted);display:flex;flex-direction:column;gap:.45rem;line-height:1.6">
                <div><span style="color:var(--text);font-weight:600">1X2</span> — Home Win / Draw / Away Win</div>
                <div><span style="color:var(--text);font-weight:600">Over 2.5</span> — 3 or more goals total in the match</div>
                <div><span style="color:var(--text);font-weight:600">Under 2.5</span> — 2 or fewer goals total in the match</div>
                <div><span style="color:var(--text);font-weight:600">BTTS Yes</span> — Both teams each score ≥1 goal</div>
                <div><span style="color:var(--text);font-weight:600">Double Chance</span> — Covers 2 outcomes, lower risk &amp; lower odds</div>
                <div><span style="color:var(--text);font-weight:600">Draw No Bet</span> — Win if your team wins; stake back on draw</div>
                <div><span style="color:var(--text);font-weight:600">⭐ Value Bet</span> — AI thinks odds are higher than they should be</div>
                <div><span style="color:var(--text);font-weight:600">Implied %</span> — The bookmaker's probability (1 ÷ odds × 100)</div>
            </div>
        </div>

        {{-- Bookmakers --}}
        @if($bookmakers->isNotEmpty())
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">🏦 Bet With The Best</span>
            </div>
            @foreach($bookmakers as $bm)
            <div style="padding:.75rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:.75rem">
                <div>
                    <div style="font-size:.85rem;font-weight:600;color:var(--text)">{{ $bm->name }}</div>
                    @if($bm->welcome_offer)<div style="font-size:.7rem;color:var(--accent2)">{{ $bm->welcome_offer }}</div>@endif
                </div>
                <a href="{{ $bm->affiliate_url }}" target="_blank" rel="nofollow noopener"
                   style="background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.78rem;letter-spacing:.06em;padding:.35rem .85rem;border-radius:4px;text-decoration:none;white-space:nowrap">
                    BET NOW
                </a>
            </div>
            @endforeach
        </div>
        @endif

        <a href="{{ route('home') }}"
           style="display:block;text-align:center;background:var(--surface);border:1px solid var(--border);color:var(--muted);font-size:.8rem;padding:.6rem;border-radius:6px;text-decoration:none;transition:all .2s"
           onmouseover="this.style.color='var(--text)';this.style.borderColor='var(--dim)'" onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
            ← Back to all fixtures
        </a>

    </div>{{-- end sidebar --}}

    </div>{{-- end grid --}}
</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
