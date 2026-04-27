<x-app-layout>

<x-slot name="title">{{ $league->name }} Betting Tips — {{ $date->format('d M Y') }}</x-slot>
<x-slot name="description">AI-generated betting tips for {{ $league->name }}{{ $league->country ? ' (' . $league->country . ')' : '' }} on {{ $date->format('l j F Y') }}. SCOUT analyses every fixture to surface high-confidence predictions.</x-slot>
<x-slot name="canonical">{{ route('league.show', $league) }}</x-slot>

@push('head')
@php
$ldLeague = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',  'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tips',  'item' => route('tips.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $league->name . ' Tips'],
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($ldLeague, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

{{-- ══════════════════════════════════════════════
     LEAGUE HEADER
══════════════════════════════════════════════ --}}
<div style="background:var(--card);border-bottom:1px solid var(--border)">
    <div style="max-width:1280px;margin:0 auto;padding:1.25rem 2rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">

        @if($league->logo_url)
        <img src="{{ $league->logo_url }}" alt="{{ $league->name }}" style="width:48px;height:48px;object-fit:contain;border-radius:4px">
        @endif

        @php $leagueCountry = ($league->country instanceof \App\Models\Country) ? $league->country : null; @endphp
        <div>
            <h1 style="font-family:var(--fh);font-size:1.8rem;letter-spacing:.08em;color:var(--text);margin-bottom:.15rem">
                @if($leagueCountry?->flag_url)
                <img src="{{ $leagueCountry->flag_url }}" alt="{{ $leagueCountry->name }}" style="width:22px;height:16px;object-fit:cover;border-radius:2px;vertical-align:middle;margin-right:.4rem">
                @endif
                {{ $league->name }}
            </h1>
            <div style="font-size:.78rem;color:var(--muted)">
                {{ $leagueCountry?->name ?? $league->country }}
                @if($league->season)
                · Season {{ $league->season }}
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════
     DATE TABS
══════════════════════════════════════════════ --}}
<div style="background:var(--surface);border-bottom:1px solid var(--border);overflow-x:auto;overflow-y:hidden">
    <div style="max-width:1280px;margin:0 auto;display:flex;padding:0 2rem;gap:0">
        @foreach(collect(range(-1, 3))->map(fn($d) => now()->addDays($d)) as $day)
        <a href="{{ route('league.show', ['league' => $league->slug, 'date' => $day->toDateString()]) }}"
           style="display:inline-flex;flex-direction:column;align-items:center;flex-shrink:0;padding:.55rem .9rem;font-size:.72rem;text-decoration:none;border-bottom:2px solid {{ $date->isSameDay($day) ? 'var(--accent)' : 'transparent' }};color:{{ $date->isSameDay($day) ? 'var(--accent)' : 'var(--muted)' }};white-space:nowrap;text-transform:uppercase;letter-spacing:.05em;font-weight:600">
            <span style="font-size:.6rem">{{ $day->format('D') }}</span>
            <span style="font-size:.88rem">{{ $day->format('d M') }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MAIN
══════════════════════════════════════════════ --}}
<div class="scout-page-wrap" style="max-width:1280px;margin:0 auto;padding:1.5rem 2rem">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.5rem">
        <span style="font-size:.75rem;color:var(--muted);background:var(--card);padding:.3rem .7rem;border-radius:20px;border:1px solid var(--border)">
            {{ $fixtures->count() }} {{ Str::plural('game', $fixtures->count()) }}
        </span>
        <a href="{{ route('fixture.betting-tips', $league->slug) }}" style="font-size:.75rem;color:var(--accent);text-decoration:none;font-weight:600">All tips →</a>
    </div>

    @forelse($fixtures as $fixture)
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;margin-bottom:1rem;overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem 1.1rem;border-bottom:1px solid var(--border);background:var(--surface)">
            <div style="display:flex;align-items:center;gap:.75rem;flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:.4rem">
                    @if($fixture->home_logo)
                    <img src="{{ $fixture->home_logo }}" alt="{{ $fixture->home_team }}" style="width:22px;height:22px;object-fit:contain">
                    @endif
                    <span style="font-size:.95rem;font-weight:700;color:var(--text)">{{ $fixture->home_team }}</span>
                </div>
                <span style="font-size:.7rem;color:var(--muted);font-weight:600">VS</span>
                <div style="display:flex;align-items:center;gap:.4rem">
                    <span style="font-size:.95rem;font-weight:700;color:var(--text)">{{ $fixture->away_team }}</span>
                    @if($fixture->away_logo)
                    <img src="{{ $fixture->away_logo }}" alt="{{ $fixture->away_team }}" style="width:22px;height:22px;object-fit:contain">
                    @endif
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;flex-shrink:0">
                <span style="font-size:.7rem;color:var(--muted)">{{ $fixture->match_date->format('H:i') }}</span>
                <a href="{{ route('fixture.betting-tips', $fixture) }}" style="font-size:.72rem;font-weight:600;color:var(--accent);text-decoration:none;border:1px solid rgba(0,229,160,.35);padding:.2rem .6rem;border-radius:4px;white-space:nowrap;transition:background .15s"
                   onmouseover="this.style.background='rgba(0,229,160,.1)'" onmouseout="this.style.background='transparent'">
                    View Analysis →
                </a>
                @if($fixture->tips->isNotEmpty())
                    <span title="Has AI tips" style="margin-left:.5rem;display:inline-flex;align-items:center;gap:.2rem;background:rgba(0,229,160,.08);border:1px solid rgba(0,229,160,.4);border-radius:20px;padding:.18rem .6rem;font-size:.72rem;color:var(--accent);font-weight:600">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="10" fill="#00e5a0" fill-opacity="0.18"/><circle cx="10" cy="10" r="5" fill="#00e5a0"/></svg>
                        AI Tips
                    </span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:2.5rem;text-align:center">
        <div style="font-size:2rem;margin-bottom:.75rem">📭</div>
        <div style="font-family:var(--fh);font-size:1.2rem;letter-spacing:.06em;color:var(--muted);margin-bottom:.5rem">No fixtures for this date</div>
        <p style="font-size:.82rem;color:var(--muted)">Try a different date or check back after the 06:00 AI scheduler run.</p>
    </div>
    @endforelse

    {{-- Pagination removed: $fixtures is a Collection, not a Paginator --}}

</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
