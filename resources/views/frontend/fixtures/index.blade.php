<x-app-layout>

<x-slot name="title">Free Football Betting Tips {{ $date->format('d M Y') }} — AI Predictions</x-slot>
<x-slot name="description">Browse all AI-generated football betting tips for {{ $date->format('d F Y') }}. High-confidence picks with match previews, odds and value bets.</x-slot>

<style>
.fixtures-page .league-card { overflow:hidden; border-radius:8px; margin-bottom:1rem; background:var(--card); border:1px solid var(--border); }
.fixtures-page .fixture-row { border-bottom:1px solid rgba(255,255,255,.04); }
.fixtures-page .fixture-row-header { display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
.fixtures-page .fixture-teams { display:flex; align-items:center; gap:.6rem; flex:1 1 0; min-width:0; flex-wrap:wrap; }
.fixtures-page .fixture-team { display:flex; align-items:center; gap:.35rem; flex:1 1 0; min-width:0; }
.fixtures-page .fixture-score { flex-shrink:0; text-align:center; min-width:52px; }
.fixtures-page .fixture-actions { display:flex; align-items:center; gap:.6rem; flex-shrink:0; margin-left:.75rem; }
.fixtures-page .fixture-actions a { white-space:nowrap; }
.fixtures-page .fixture-tips { padding:0 1.1rem .6rem; }
.fixtures-page .tip-chip { display:inline-flex; align-items:center; gap:.3rem; }
@media (max-width: 767px) {
  .fixtures-page .fixture-row-header { flex-direction:column; align-items:flex-start; padding:.7rem .9rem; }
  .fixtures-page .fixture-teams { flex-direction:column; align-items:flex-start; gap:.5rem; }
  .fixtures-page .fixture-score { width:100%; text-align:left; min-width:auto; }
  .fixtures-page .fixture-actions { width:100%; justify-content:flex-start; margin-left:0; }
  .fixtures-page .fixture-actions a { width:100%; }
  .fixtures-page .fixture-row { border-bottom:none; }
  .fixtures-page .fixture-row + .fixture-row { border-top:1px solid rgba(255,255,255,.04); }
}
</style>

{{-- Sport tab bar (consistent with home) --}}
<div class="sport-tab-sticky" style="background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:60px;z-index:100;overflow-x:auto">
    <div style="max-width:1280px;margin:0 auto;display:flex;gap:0;padding:0 2rem">
        @foreach([
            ['icon'=>'⚽','label'=>'Football','active'=>true],
            ['icon'=>'🎾','label'=>'Tennis','active'=>false],
            ['icon'=>'🏀','label'=>'Basketball','active'=>false],
            ['icon'=>'🏉','label'=>'Rugby','active'=>false],
            ['icon'=>'🏏','label'=>'Cricket','active'=>false],
        ] as $tab)
        <a href="#" style="display:inline-flex;align-items:center;flex-shrink:0;gap:.4rem;padding:.75rem .9rem;font-size:.78rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;text-decoration:none;border-bottom:2px solid {{ $tab['active'] ? 'var(--accent)' : 'transparent' }};color:{{ $tab['active'] ? 'var(--accent)' : 'var(--muted)' }};white-space:nowrap">
            {{ $tab['icon'] }} {{ $tab['label'] }}
        </a>
        @endforeach
    </div>
</div>

<div class="scout-page-wrap fixtures-page" style="max-width:1280px;margin:0 auto;padding:1.5rem 2rem">
    <div class="welcome-grid" style="display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:1.5rem;align-items:start">

    <div>

    {{-- Breadcrumb --}}
    <div style="font-size:.75rem;color:var(--muted);margin-bottom:1.25rem">
        <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
        <span style="margin:0 .4rem">›</span>
        <span style="color:var(--text)">Betting Tips</span>
    </div>

    {{-- Page header + date tabs --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.1rem">
        <h1 style="font-family:var(--fh);font-size:1.7rem;letter-spacing:.08em;color:var(--text)">
            ⚽ Betting Tips —
            @php
                $today    = \Carbon\Carbon::today()->toDateString();
                $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
                $yesterday= \Carbon\Carbon::yesterday()->toDateString();
                $activeDate = $date->toDateString();
            @endphp
            @if($activeDate === $today) Today
            @elseif($activeDate === $tomorrow) Tomorrow
            @elseif($activeDate === $yesterday) Yesterday
            @else {{ $date->format('d M') }}
            @endif
        </h1>
        <span style="font-size:.75rem;color:var(--muted);background:var(--card);padding:.3rem .75rem;border-radius:20px;border:1px solid var(--border)">
            {{ $date->format('l, d F Y') }}
        </span>
    </div>

    {{-- Date navigation --}}
    @php
        $dateTabs = [
            ['label' => 'Yesterday', 'date' => $yesterday],
            ['label' => 'Today',     'date' => $today],
            ['label' => 'Tomorrow',  'date' => $tomorrow],
            ['label' => '+2 Days',   'date' => \Carbon\Carbon::today()->addDays(2)->toDateString()],
        ];
    @endphp
    <div style="display:flex;gap:.4rem;margin-bottom:1.5rem;flex-wrap:wrap">
        @foreach($dateTabs as $tab)
        <a href="{{ route('fixture.betting-tips.index', ['date' => $tab['date']]) }}"
           style="padding:.35rem .85rem;border-radius:20px;font-size:.76rem;font-weight:600;text-decoration:none;border:1px solid {{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--border)' }};background:{{ $activeDate === $tab['date'] ? 'rgba(0,229,160,.12)' : 'var(--surface)' }};color:{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--muted)' }};transition:all .15s"
           onmouseover="this.style.borderColor='var(--dim)';this.style.color='var(--text)'"
           onmouseout="this.style.borderColor='{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--border)' }}';this.style.color='{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--muted)' }}'">
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>

    @if($fixtures->isEmpty())
    {{-- Empty state --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:3rem;text-align:center">
        <div style="font-size:2.5rem;margin-bottom:.75rem">📊</div>
        <div style="font-family:var(--fh);font-size:1.2rem;letter-spacing:.06em;color:var(--text);margin-bottom:.5rem">No Tips for This Date</div>
        <p style="font-size:.85rem;color:var(--muted);max-width:380px;margin:0 auto .75rem">
            Our AI analysts haven't published any picks for {{ $date->format('d F Y') }} yet. Check back later or browse another date.
        </p>
        <a href="{{ route('fixture.betting-tips.index') }}" style="display:inline-block;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.85rem;letter-spacing:.06em;padding:.55rem 1.4rem;border-radius:5px;text-decoration:none">
            TODAY'S TIPS
        </a>
    </div>
    @else

    {{-- Summary bar --}}
    @php
        $totalFixtures = $fixtures->flatten()->count();
        $totalTips = $fixtures->flatten()->flatMap->tips->count();
        $highConf = $fixtures->flatten()->flatMap->tips->where('confidence', '>=', 75)->count();
        $valueBets = $fixtures->flatten()->flatMap->tips->where('is_value_bet', true)->count();
    @endphp
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.25rem">
        @foreach([
            ['val' => $totalFixtures, 'lbl' => 'Fixtures'],
            ['val' => $totalTips,     'lbl' => 'Total Tips'],
            ['val' => $highConf,      'lbl' => 'High Confidence'],
            ['val' => $valueBets,     'lbl' => 'Value Bets ⭐'],
        ] as $stat)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:.55rem .9rem;text-align:center;min-width:90px">
            <div style="font-family:var(--fm);font-size:1.1rem;font-weight:700;color:var(--accent)">{{ $stat['val'] }}</div>
            <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">{{ $stat['lbl'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Fixtures grouped by league --}}
    @foreach($fixtures as $leagueId => $leagueFixtures)
    @php
        $leagueModel   = $leagueFixtures->first()->league;
        $leagueCountry = $leagueModel?->country instanceof \App\Models\Country ? $leagueModel->country : null;
    @endphp
    <div class="league-card" style="background:var(--card);border:1px solid var(--border);border-radius:8px;margin-bottom:1rem;overflow:hidden">

        {{-- League header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.55rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:center;gap:.55rem">
                @if($leagueCountry?->flag_url)
                <img src="{{ $leagueCountry->flag_url }}" alt="{{ $leagueCountry->name }}" style="width:18px;height:13px;object-fit:cover;border-radius:2px;flex-shrink:0">
                @elseif($leagueModel?->logo_url)
                <img src="{{ $leagueModel->logo_url }}" alt="{{ $leagueModel->name }}" style="width:18px;height:18px;object-fit:contain;flex-shrink:0">
                @endif
                <span style="font-size:.75rem;color:var(--muted)">{{ $leagueCountry?->name ?? 'International' }}</span>
                <span style="color:var(--border)">·</span>
                <span style="font-size:.82rem;font-weight:600;color:var(--text)">{{ $leagueModel?->name ?? 'Unknown League' }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:.75rem">
                <span style="font-size:.7rem;color:var(--muted)">{{ $leagueFixtures->count() }} {{ Str::plural('match', $leagueFixtures->count()) }}</span>
                @if($leagueModel)
                <a href="{{ route('league.show', $leagueModel) }}" style="font-size:.7rem;color:var(--accent);text-decoration:none;font-weight:600;letter-spacing:.04em">ALL →</a>
                @endif
            </div>
        </div>

        {{-- Fixture rows --}}
        @foreach($leagueFixtures as $fixture)
        @php $tips = $fixture->tips; $topTip = $tips->first(); @endphp
        <div class="fixture-row" style="border-bottom:1px solid rgba(255,255,255,.04)">

            {{-- Match row --}}
            <div class="fixture-row-header" style="display:flex;align-items:center;justify-content:space-between;padding:.7rem 1.1rem;transition:background .15s"
                 onmouseover="this.style.background='rgba(255,255,255,.02)'" onmouseout="this.style.background='transparent'">

                {{-- Teams --}}
                <a href="{{ route('fixture.betting-tips', $fixture) }}"
                   class="fixture-teams"
                   style="display:flex;align-items:center;gap:.6rem;text-decoration:none;flex:1;min-width:0">
                    {{-- Home team --}}
                    <div class="fixture-team" style="display:flex;align-items:center;gap:.35rem;flex:1;min-width:0;justify-content:flex-end">
                        <span style="font-size:.92rem;font-weight:700;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $fixture->home_team }}</span>
                        @if($fixture->home_logo)
                        <img src="{{ $fixture->home_logo }}" alt="{{ $fixture->home_team }}" style="width:24px;height:24px;object-fit:contain;flex-shrink:0">
                        @else
                        <span style="width:24px;height:24px;background:var(--surface);border:1px solid var(--border);border-radius:50%;display:inline-block;flex-shrink:0"></span>
                        @endif
                    </div>
                    {{-- Time / score --}}
                    <div style="flex-shrink:0;text-align:center;min-width:52px">
                        @if($fixture->score_home !== null)
                        <div style="font-family:var(--fm);font-size:.95rem;font-weight:700;color:var(--text)">{{ $fixture->score_home }} — {{ $fixture->score_away }}</div>
                        @else
                        <div style="font-size:.78rem;font-weight:700;color:var(--muted)">{{ $fixture->match_date->format('H:i') }}</div>
                        @endif
                        <div style="font-size:.6rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">{{ $fixture->status === 'NS' ? 'KO' : $fixture->status }}</div>
                    </div>
                    {{-- Away team --}}
                    <div class="fixture-team" style="display:flex;align-items:center;gap:.35rem;flex:1;min-width:0">
                        @if($fixture->away_logo)
                        <img src="{{ $fixture->away_logo }}" alt="{{ $fixture->away_team }}" style="width:24px;height:24px;object-fit:contain;flex-shrink:0">
                        @else
                        <span style="width:24px;height:24px;background:var(--surface);border:1px solid var(--border);border-radius:50%;display:inline-block;flex-shrink:0"></span>
                        @endif
                        <span style="font-size:.92rem;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $fixture->away_team }}</span>
                    </div>
                </a>

                {{-- Right side: tips count + CTA --}}
                <div class="fixture-actions" style="display:flex;align-items:center;gap:.6rem;flex-shrink:0;margin-left:.75rem">
                    <span style="font-size:.7rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);padding:.18rem .5rem;border-radius:4px;white-space:nowrap">
                        {{ $tips->count() }} {{ Str::plural('tip', $tips->count()) }}
                    </span>
                    <a href="{{ route('fixture.betting-tips', $fixture) }}"
                       style="font-size:.72rem;font-weight:700;color:var(--accent);text-decoration:none;border:1px solid rgba(0,229,160,.35);padding:.22rem .65rem;border-radius:4px;white-space:nowrap;transition:background .15s"
                       onmouseover="this.style.background='rgba(0,229,160,.1)'" onmouseout="this.style.background='transparent'">
                        View Tips →
                    </a>
                </div>
            </div>

            {{-- Prediction bar + tip chips --}}
            @if($fixture->prediction_percent_home !== null || $tips->isNotEmpty())
            <div class="fixture-tips" style="padding:0 1.1rem .6rem">
                {{-- Slim probability bar --}}
                @if($fixture->prediction_percent_home !== null)
                @php $ph = $fixture->prediction_percent_home; $pd = $fixture->prediction_percent_draw; $pa = $fixture->prediction_percent_away; @endphp
                <div style="display:flex;align-items:center;gap:.35rem;margin-bottom:.4rem">
                    <span style="font-size:.6rem;color:var(--dim);width:28px;text-align:right;flex-shrink:0">{{ $ph }}%</span>
                    <div style="flex:1;display:flex;height:5px;border-radius:3px;overflow:hidden;gap:2px">
                        <div style="width:{{ $ph }}%;background:var(--accent);border-radius:2px 0 0 2px" title="{{ $fixture->home_team }} {{ $ph }}%"></div>
                        <div style="width:{{ $pd }}%;background:var(--dim)" title="Draw {{ $pd }}%"></div>
                        <div style="width:{{ $pa }}%;background:var(--accent2);border-radius:0 2px 2px 0" title="{{ $fixture->away_team }} {{ $pa }}%"></div>
                    </div>
                    <span style="font-size:.6rem;color:var(--dim);width:28px;flex-shrink:0">{{ $pa }}%</span>
                </div>
                @endif

                {{-- Tip chips --}}
                @if($tips->isNotEmpty())
                <div style="display:flex;flex-wrap:wrap;gap:.35rem">
                    @foreach($tips as $t)
                    <span class="tip-chip" style="display:inline-flex;align-items:center;gap:.3rem;background:{{ $t->confidence >= 75 ? 'rgba(0,229,160,.08)' : 'var(--surface)' }};border:1px solid {{ $t->confidence >= 75 ? 'rgba(0,229,160,.4)' : 'var(--border)' }};border-radius:20px;padding:.2rem .6rem;font-size:.74rem;color:var(--text);white-space:nowrap">
                        <span style="color:var(--muted);font-size:.67rem">{{ $t->market }}:</span>
                        <span style="font-weight:700">{{ $t->selection }}</span>
                        @if($t->odds)
                        <span style="font-family:var(--fm);color:var(--accent2);font-weight:700">{{ number_format($t->odds, 2) }}</span>
                        @endif
                        <span style="font-family:var(--fm);color:{{ $t->confidence >= 75 ? 'var(--accent)' : 'var(--muted)' }};font-size:.67rem">{{ $t->confidence }}%</span>
                        @if($t->is_value_bet)<span style="color:var(--accent2)">⭐</span>@endif
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

        </div>
        @endforeach
    </div>
    @endforeach

    @endif {{-- end fixtures empty check --}}

    </div>
    @include('frontend.partials.home-sidebar')
    </div>

</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
