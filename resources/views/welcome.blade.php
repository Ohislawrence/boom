<x-app-layout>

<x-slot name="title">AI Football Betting Tips Today — Free Predictions</x-slot>
<x-slot name="description">Get free AI-generated football betting tips updated daily. High-confidence match predictions, value bets and expert analysis from SCOUT.</x-slot>

<style>
/* Mobile-first overrides - NO GRADIENTS */
@media (max-width: 767px) {
    .sport-tab-sticky {
        top: 52px !important;
        background: var(--surface) !important;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }
    .sport-tab-sticky > div {
        padding: 0 0.75rem !important;
    }
    .sport-tab-sticky a {
        padding: 0.6rem 0.7rem !important;
        font-size: 0.7rem !important;
    }

    /* Hero section */
    .frontpage-hero-inner {
        flex-direction: column !important;
        text-align: center !important;
        padding: 1rem !important;
        gap: 1rem !important;
    }
    .frontpage-hero-inner > div:first-child {
        width: 100%;
    }
    .frontpage-hero-inner > div:first-child > div:first-child {
        font-size: 1.15rem !important;
    }
    .frontpage-hero-inner > div:last-child {
        width: 100%;
    }
    .frontpage-hero-inner a {
        width: 100%;
        text-align: center;
    }

    /* Remove all gradients */
    .bm-row div:first-child {
        background: var(--surface) !important;
    }

    /* Sticky bookmaker bar */
    #sticky-bm-bar {
        top: 90px !important;
        padding: 0.5rem 0.75rem !important;
    }
    #sticky-bm-bar > div {
        flex-direction: column;
        align-items: stretch !important;
        gap: 0.5rem !important;
    }
    #sticky-bm-bar > div > div:first-child {
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }
    #sticky-bm-bar a {
        background: var(--surface) !important;
    }

    /* Bookmaker grid */
    .bm-row {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
        padding: 1rem !important;
        text-align: center;
    }
    .bm-row > div:first-child {
        margin: 0 auto;
    }
    .bm-row > div:first-child + div {
        text-align: center;
    }
    .bm-row > div:first-child + div > div:first-child {
        justify-content: center;
    }
    .bm-row > div:first-child + div > div:nth-child(2),
    .bm-row > div:first-child + div > div:nth-child(3) {
        justify-content: center;
        justify-content: center;
        display: flex;
        flex-wrap: wrap;
    }
    .bm-row > div:nth-child(3) {
        text-align: center !important;
        min-width: auto !important;
    }
    .bm-row > div:last-child {
        width: 100%;
        min-width: auto !important;
    }
    .bm-row > div:last-child a {
        width: 100%;
    }

    /* Date tabs */
    .date-tabs {
        overflow-x: auto;
        flex-wrap: nowrap !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        padding-bottom: 0.25rem;
    }
    .date-tabs a {
        white-space: nowrap;
        font-size: 0.7rem !important;
        padding: 0.3rem 0.7rem !important;
    }

    /* Tips header */
    .tips-header {
        flex-direction: column;
        align-items: flex-start !important;
    }

    /* Bookmarks strip (3 cards) */
    #bm-strip {
        grid-template-columns: 1fr !important;
        gap: 0.5rem !important;
    }

    /* Other competitions grid */
    .other-comps-grid {
        grid-template-columns: 1fr !important;
    }

    /* Bookmakers section heading */
    .bookmakers-heading {
        font-size: 1.4rem !important;
    }
}

/* Tablet improvements */
@media (min-width: 768px) and (max-width: 1023px) {
    .welcome-grid {
        gap: 1rem !important;
    }
    .bm-row {
        grid-template-columns: 35px 1fr auto auto !important;
        gap: 0.75rem !important;
        padding: 0.75rem 1rem !important;
    }
    .bm-row > div:first-child + div > div:first-child {
        flex-wrap: wrap;
    }
    .bm-row > div:nth-child(3) {
        min-width: 100px !important;
    }
    .bm-row > div:last-child {
        min-width: 100px !important;
    }
}

/* Desktop remains unchanged */
.sport-tab-sticky a {
    transition: color 0.2s ease;
}
.bm-row {
    transition: all 0.2s ease;
}
.home-fixture-card-compact {
    display: block;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    overflow: hidden;
}

.home-fixture-card-compact:active {
    transform: scale(0.98);
    background: rgba(0,229,160,.02);
}

.home-fixture-compact-layout {
    padding: 0.65rem 0.85rem;
}

.home-fixture-league {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.6rem;
}

.home-fixture-league-name {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--accent);
    background: rgba(0,229,160,.1);
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
}

.home-fixture-time {
    font-size: 0.7rem;
    font-family: var(--fm);
    color: var(--muted);
}

.home-fixture-teams-compact {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.6rem;
}

.home-team-compact {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex: 1;
    min-width: 0;
}

.home-team-compact--away {
    justify-content: flex-end;
}

.home-team-logo {
    width: 24px;
    height: 24px;
    object-fit: contain;
    flex-shrink: 0;
}

.home-team-name {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    min-width: 0;
}

.home-team-compact--away .home-team-name {
    text-align: right;
}

.home-vs-badge {
    font-size: 0.65rem;
    font-weight: 800;
    font-family: var(--fm);
    color: var(--accent2);
    background: rgba(245,197,24,.1);
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    flex-shrink: 0;
}

.home-fixture-action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 0.4rem;
    border-top: 1px solid var(--border);
}

.home-fixture-action-badge {
    font-size: 0.6rem;
    color: var(--accent);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.home-fixture-arrow {
    font-size: 0.9rem;
    color: var(--accent);
    font-weight: 700;
    transition: transform 0.2s ease;
}

.home-fixture-card-compact:hover .home-fixture-arrow {
    transform: translateX(3px);
}

/* Ultra-compact mobile view */
@media (max-width: 480px) {
    .home-fixture-compact-layout {
        padding: 0.5rem 0.7rem;
    }

    .home-team-logo {
        width: 20px;
        height: 20px;
    }

    .home-team-name {
        font-size: 0.75rem;
    }

    .home-vs-badge {
        font-size: 0.55rem;
        padding: 0.15rem 0.35rem;
    }

    .home-fixture-league-name {
        font-size: 0.55rem;
    }

    .home-fixture-time {
        font-size: 0.6rem;
    }

    .home-fixture-action-badge {
        font-size: 0.55rem;
    }

    .home-fixture-arrow {
        font-size: 0.8rem;
    }
}

/* For the played fixtures section - use same style */
.home-fixture-score {
    font-family: var(--fm);
    font-size: 0.85rem;
    font-weight: 800;
    color: var(--accent);
    background: rgba(0,229,160,.08);
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
}

/* Apply to played fixtures */
.home-fixture-score-badge {
    font-size: 0.65rem;
    font-weight: 700;
    font-family: var(--fm);
    color: var(--accent2);
}
.home-score-badge {
    font-size: 0.7rem;
    font-weight: 800;
    font-family: var(--fm);
    color: var(--accent2);
    background: rgba(245,197,24,.12);
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    flex-shrink: 0;
    letter-spacing: 0.02em;
}

@media (max-width: 480px) {
    .home-score-badge {
        font-size: 0.6rem;
        padding: 0.15rem 0.4rem;
    }
}
</style>

{{-- ══════════════════════════════════════════════
     SPORT CATEGORY TAB BAR (NO GRADIENTS)
══════════════════════════════════════════════ --}}
<div class="sport-tab-sticky" style="background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:60px;z-index:100;overflow-x:auto;overflow-y:hidden">
    <div style="max-width:1280px;margin:0 auto;display:flex;gap:0;padding:0 2rem">
        @foreach([
            ['icon'=>'⚽','label'=>'Football','active'=>true],
            ['icon'=>'🎾','label'=>'Tennis','active'=>false],
            ['icon'=>'🏀','label'=>'Basketball','active'=>false],
            ['icon'=>'🏉','label'=>'Rugby','active'=>false],
            ['icon'=>'🏏','label'=>'Cricket','active'=>false],
            ['icon'=>'🥊','label'=>'MMA/Boxing','active'=>false],
            ['icon'=>'🏎️','label'=>'Formula 1','active'=>false],
            ['icon'=>'🎯','label'=>'Darts','active'=>false],
        ] as $tab)
        <a href="#" style="display:inline-flex;align-items:center;flex-shrink:0;gap:.4rem;padding:.75rem .9rem;font-size:.78rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;text-decoration:none;border-bottom:2px solid {{ $tab['active'] ? 'var(--accent)' : 'transparent' }};color:{{ $tab['active'] ? 'var(--accent)' : 'var(--muted)' }};transition:color .2s;white-space:nowrap" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='{{ $tab['active'] ? 'var(--accent)' : 'var(--muted)' }}'">
            <span>{{ $tab['icon'] }}</span>{{ $tab['label'] }}
        </a>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════
     STICKY BOOKMAKER BAR (NO GRADIENTS)
══════════════════════════════════════════════ --}}
<div id="sticky-bm-bar" style="background:var(--card);border-bottom:1px solid var(--border);padding:.5rem 2rem;position:sticky;top:107px;z-index:99;transform:translateY(-100%);transition:transform .3s,opacity .3s;opacity:0">
    <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:1rem">
        <div style="display:flex;align-items:center;gap:.8rem;overflow-x:auto">
            <span style="font-size:.7rem;color:var(--muted);white-space:nowrap">Top sites:</span>
            @foreach($bookmakers->take(3) as $bm)
            <a href="{{ $bm->affiliate_url }}" target="_blank" rel="nofollow"
               style="display:flex;align-items:center;gap:.4rem;background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:.3rem .8rem;text-decoration:none;white-space:nowrap;transition:all .15s"
               onmouseover="this.style.borderColor='var(--accent)';this.style.background='rgba(0,229,160,.05)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)'">
                @if($bm->logo_url)
                <img src="{{ $bm->logo_url }}" alt="{{ $bm->name }}" style="height:16px;width:auto;object-fit:contain">
                @endif
                <span style="font-size:.72rem;color:var(--text);font-weight:600">{{ $bm->name }}</span>
                @if($bm->welcome_offer)
                <span style="font-size:.65rem;color:var(--accent);font-weight:700">{{ Str::limit($bm->welcome_offer, 14) }}</span>
                @endif
            </a>
            @endforeach
        </div>
        <a href="#bookmakers" style="font-size:.75rem;color:var(--accent);text-decoration:none;font-weight:600;white-space:nowrap">Compare all →</a>
    </div>
</div>

<script>
(function(){
    var SHOW_AFTER  = 400;
    var HIDE_BEFORE = 200;
    var ticking     = false;
    window.addEventListener('scroll', function(){
        if (!ticking) {
            window.requestAnimationFrame(function(){
                var sy  = window.scrollY;
                var bar = document.getElementById('sticky-bm-bar');
                if (sy > SHOW_AFTER) {
                    bar.style.transform = 'translateY(0)';
                    bar.style.opacity   = '1';
                } else if (sy < HIDE_BEFORE) {
                    bar.style.transform = 'translateY(-100%)';
                    bar.style.opacity   = '0';
                }
                ticking = false;
            });
            ticking = true;
        }
    }, {passive:true});
})();
</script>

{{-- ══════════════════════════════════════════════
     HERO CTA (NO GRADIENTS)
══════════════════════════════════════════════ --}}
<div class="frontpage-hero" style="background:var(--card);border-bottom:1px solid var(--border)">
    <div class="frontpage-hero-inner" style="max-width:1280px;margin:0 auto;padding:1.25rem 2rem;display:flex;align-items:center;justify-content:space-between;gap:1.5rem">
        <div style="flex:1;min-width:0">
            <div style="font-family:var(--fh);font-size:1.35rem;letter-spacing:.06em;color:var(--text);line-height:1.3;margin-bottom:.5rem">
                Expert Betting Tips +
                <span style="color:var(--accent)">Exclusive Bonuses</span>
            </div>
            <div style="font-size:.8rem;color:var(--muted);line-height:1.5">
                Join 50,000+ bettors using {{ config('app.name') }}'s AI analysis. New users get matched deposit bonuses.
            </div>
        </div>
        <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:.4rem">
            @if($bookmakers->first())
            <a href="{{ $bookmakers->first()->affiliate_url }}" target="_blank" rel="nofollow noopener"
               style="display:inline-block;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.95rem;letter-spacing:.08em;padding:.75rem 1.75rem;border-radius:6px;text-decoration:none;white-space:nowrap">
                GET BONUS @ {{ $bookmakers->first()->name }} →
            </a>
            @endif
            <a href="#bookmakers" style="font-size:.65rem;color:var(--muted);text-decoration:none">Compare all sites ↓</a>
            <span style="font-size:.62rem;color:var(--muted)">18+ | T&amp;Cs apply</span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MAIN CONTENT — TWO COLUMNS
══════════════════════════════════════════════ --}}
<div class="welcome-grid scout-page-wrap" style="max-width:1280px;margin:0 auto;padding:1.5rem">

    {{-- ── LEFT COLUMN ── --}}
    <div>

        {{-- DATE NAVIGATION --}}
        @php
            $localNow = \Illuminate\Support\Carbon::now($geoTimezone ?? config('app.timezone'));
            $yesterday = $localNow->copy()->subDay()->toDateString();
            $today     = $localNow->toDateString();
            $tomorrow  = $localNow->copy()->addDay()->toDateString();
            $dayAfter  = $localNow->copy()->addDays(2)->toDateString();
            $activeDate = $date->toDateString();
            $dateTabs = [
                ['label' => 'Yesterday', 'date' => $yesterday],
                ['label' => 'Today',     'date' => $today],
                ['label' => 'Tomorrow',  'date' => $tomorrow],
                ['label' => '+2 Days',   'date' => $dayAfter],
            ];
        @endphp
        <div class="date-tabs" style="display:flex;gap:.4rem;margin-bottom:1.1rem;flex-wrap:wrap">
            @foreach($dateTabs as $tab)
            <a href="{{ route('home', ['date' => $tab['date']]) }}"
               style="padding:.35rem .85rem;border-radius:20px;font-size:.76rem;font-weight:600;text-decoration:none;border:1px solid {{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--border)' }};background:{{ $activeDate === $tab['date'] ? 'rgba(0,229,160,.12)' : 'var(--surface)' }};color:{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--muted)' }};transition:all .15s"
               onmouseover="this.style.borderColor='var(--dim)';this.style.color='var(--text)'"
               onmouseout="this.style.borderColor='{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--border)' }}';this.style.color='{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--muted)' }}'">
                {{ $tab['label'] }}
            </a>
            @endforeach
        </div>

        <div class="tips-header" id="tips" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
            <h1 style="font-family:var(--fh);font-size:1.6rem;letter-spacing:.08em;color:var(--text)">
                ⚽ Football Tips —
                @if($activeDate === $today) Today
                @elseif($activeDate === $tomorrow) Tomorrow
                @elseif($activeDate === $yesterday) Yesterday
                @else {{ $date->format('d M') }}
                @endif
            </h1>
            <div style="display:flex;align-items:center;gap:.6rem">
                <span style="font-size:.75rem;color:var(--muted);background:var(--card);padding:.3rem .7rem;border-radius:20px;border:1px solid var(--border)">
                    {{ $date->format('d M Y') }}
                </span>
            </div>
        </div>

        @if($latestUnplayedFixtures->isNotEmpty())
        <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;margin-bottom:1.25rem;overflow:hidden">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.1rem;border-bottom:1px solid var(--border);flex-wrap:wrap">
                <div>
                    <div style="font-family:var(--fh);font-size:1rem;color:var(--text);margin-bottom:.25rem">Next 10 unplayed games</div>
                    <div style="font-size:.8rem;color:var(--muted)">Upcoming fixtures for {{ $date->format('d M Y') }} that are still not played.</div>
                </div>
                <a href="{{ route('fixture.betting-tips.index', ['date' => $date->toDateString(), 'status' => 'NS']) }}"
                   style="font-size:.8rem;font-weight:700;color:var(--accent);text-decoration:none;border:1px solid var(--accent);padding:.55rem .9rem;border-radius:999px;white-space:nowrap;">
                    See all unplayed games for {{ $date->format('d M') }} →
                </a>
            </div>
           <div style="display:grid;gap:.5rem;padding:.5rem .75rem .75rem .75rem">
                @foreach($latestUnplayedFixtures as $fixture)
                <a href="{{ route('fixture.betting-tips', $fixture) }}" class="home-fixture-card-compact">
                    <div class="home-fixture-compact-layout">
                        {{-- League badge --}}
                        <div class="home-fixture-league">
                            <span class="home-fixture-league-name">{{ optional($fixture->league)->name ?? 'Unknown League' }}</span>
                            <span class="home-fixture-time">{{ $fixture->match_date->format('H:i') }}</span>
                        </div>

                        {{-- Teams row --}}
                        <div class="home-fixture-teams-compact">
                            <div class="home-team-compact">
                                @if($fixture->home_logo)
                                <img src="{{ $fixture->home_logo }}" alt="{{ $fixture->home_team }}" class="home-team-logo">
                                @endif
                                <span class="home-team-name">{{ $fixture->home_team }}</span>
                            </div>

                            <div class="home-vs-badge">VS</div>

                            <div class="home-team-compact home-team-compact--away">
                                <span class="home-team-name">{{ $fixture->away_team }}</span>
                                @if($fixture->away_logo)
                                <img src="{{ $fixture->away_logo }}" alt="{{ $fixture->away_team }}" class="home-team-logo">
                                @endif
                            </div>
                        </div>

                        {{-- Action row --}}
                        <div class="home-fixture-action-row">
                            <span class="home-fixture-action-badge">AI Tips Available</span>
                            <span class="home-fixture-arrow">→</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div style="display:grid;gap:.5rem;padding:.5rem .75rem .75rem .75rem">
            @foreach($latestPlayedFixtures as $fixture)
            <a href="{{ route('fixture.betting-tips', $fixture) }}" class="home-fixture-card-compact">
                <div class="home-fixture-compact-layout">
                    {{-- League badge --}}
                    <div class="home-fixture-league">
                        <span class="home-fixture-league-name">{{ optional($fixture->league)->name ?? 'Unknown League' }}</span>
                        <span class="home-fixture-time">Final</span>
                    </div>

                    {{-- Teams row with score --}}
                    <div class="home-fixture-teams-compact">
                        <div class="home-team-compact">
                            @if($fixture->home_logo)
                            <img src="{{ $fixture->home_logo }}" alt="{{ $fixture->home_team }}" class="home-team-logo">
                            @endif
                            <span class="home-team-name">{{ $fixture->home_team }}</span>
                        </div>

                        <div class="home-score-badge">
                            {{ $fixture->score_home }} - {{ $fixture->score_away }}
                        </div>

                        <div class="home-team-compact home-team-compact--away">
                            <span class="home-team-name">{{ $fixture->away_team }}</span>
                            @if($fixture->away_logo)
                            <img src="{{ $fixture->away_logo }}" alt="{{ $fixture->away_team }}" class="home-team-logo">
                            @endif
                        </div>
                    </div>

                    {{-- Action row --}}
                    <div class="home-fixture-action-row">
                        <span class="home-fixture-action-badge">Match Analysis →</span>
                        <span class="home-fixture-arrow">→</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- COMPACT BOOKMAKER STRIP (auto-rotates every 45s) NO GRADIENTS --}}
        @if($bookmakers->count() > 0)
        @php
        $bmData = $bookmakers->map(fn($b) => [
            'url'   => $b->affiliate_url,
            'logo'  => $b->logo_url,
            'name'  => $b->name,
            'offer' => Str::limit($b->welcome_offer ?? '', 22),
        ])->values()->toArray();
        @endphp
        <div id="bm-strip" style="display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem;margin-bottom:1.2rem;transition:opacity .5s ease"></div>
        <div style="display:flex;justify-content:center;gap:.35rem;margin-top:-.7rem;margin-bottom:1rem" id="bm-dots"></div>
        <script>
        (function(){
            var bms   = @json($bmData);
            var PER   = 3;
            var DELAY = 45000;
            var cur   = 0;
            var total = Math.ceil(bms.length / PER);
            var strip = document.getElementById('bm-strip');
            var dots  = document.getElementById('bm-dots');

            function card(bm, first) {
                var a = document.createElement('a');
                a.href = bm.url;
                a.target = '_blank';
                a.rel = 'nofollow noopener';
                a.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:.3rem;background:var(--card);border:1px solid '+(first?'var(--accent)':'var(--border)')+';border-radius:8px;padding:.75rem .6rem;text-decoration:none;transition:all .2s';
                a.onmouseover = function(){ this.style.borderColor='var(--accent)'; this.style.transform='translateY(-1px)'; };
                a.onmouseout  = function(){ this.style.borderColor=first?'var(--accent)':'var(--border)'; this.style.transform=''; };
                if (bm.logo) {
                    var img = document.createElement('img');
                    img.src = bm.logo; img.alt = bm.name;
                    img.style.cssText = 'height:20px;object-fit:contain';
                    a.appendChild(img);
                }
                var offer = document.createElement('span');
                offer.style.cssText = 'font-family:var(--fm);font-size:.8rem;color:var(--accent2);font-weight:700;text-align:center';
                offer.textContent = bm.offer;
                a.appendChild(offer);
                var btn = document.createElement('span');
                btn.style.cssText = 'font-family:var(--fh);font-size:.73rem;letter-spacing:.06em;color:'+(first?'#07090e':'var(--accent)')+';background:'+(first?'var(--accent)':'transparent')+';border:1px solid var(--accent);border-radius:4px;padding:.2rem .6rem;width:100%;text-align:center;box-sizing:border-box';
                btn.textContent = 'CLAIM '+(first?'NOW':'OFFER')+' \u2192';
                a.appendChild(btn);
                var fine = document.createElement('span');
                fine.style.cssText = 'font-size:.58rem;color:var(--muted)';
                fine.textContent = '18+ T\u0026Cs apply';
                a.appendChild(fine);
                return a;
            }

            function renderDots() {
                dots.innerHTML = '';
                if (total <= 1) return;
                for (var i = 0; i < total; i++) {
                    var d = document.createElement('span');
                    d.style.cssText = 'display:inline-block;width:6px;height:6px;border-radius:50%;background:'+(i===cur?'var(--accent)':'var(--border)')+';transition:background .3s;cursor:pointer';
                    (function(idx){ d.addEventListener('click', function(){ show(idx); }); })(i);
                    dots.appendChild(d);
                }
            }

            function show(idx) {
                cur = ((idx % total) + total) % total;
                strip.style.opacity = '0';
                setTimeout(function(){
                    strip.innerHTML = '';
                    var slice = bms.slice(cur * PER, cur * PER + PER);
                    slice.forEach(function(bm, i){ strip.appendChild(card(bm, i === 0)); });
                    strip.style.opacity = '1';
                    renderDots();
                }, 500);
            }

            // initial render without fade delay
            var slice = bms.slice(0, PER);
            slice.forEach(function(bm, i){ strip.appendChild(card(bm, i === 0)); });
            renderDots();

            if (total > 1) {
                setInterval(function(){ show(cur + 1); }, DELAY);
            }
        })();
        </script>
        @endif

        <style>
            .tip-fx-h{display:none!important}
            .tip-lg-h{display:none!important}
            .tip-league-card { display:grid; gap:.5rem; }
            .tip-fx-row { display:grid; gap:.35rem; }
            .tip-fx-row-header { display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
            .tip-fx-row-header > a { flex:1 1 0; min-width:0; }
            .tip-fx-row-header > div { flex:0 0 auto; min-width:0; }
            .home-fixture-card { display:grid; grid-template-columns:1fr auto; gap:.75rem; align-items:center; background:var(--card); border:1px solid var(--border); border-radius:10px; padding:.95rem 1rem; text-decoration:none; color:inherit; transition:background .15s; }
            .home-fixture-card:hover { background:rgba(0,229,160,.04); }
            .home-fixture-teams { display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:.75rem; min-width:0; }
            .home-team-side { display:flex; align-items:center; gap:.6rem; min-width:0; }
            .home-team-side--away { justify-content:flex-end; text-align:right; }
            .home-score-block { display:flex; flex-direction:column; align-items:center; gap:.2rem; min-width:auto; }
            .home-score-label { font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
            .home-score-value { font-family:var(--fm); font-size:1rem; font-weight:700; color:var(--accent); }
            .home-fixture-meta { display:flex; align-items:center; justify-content:space-between; gap:.75rem; font-size:.78rem; color:var(--muted); width:100%; min-width:0; }
            .home-fixture-meta span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
            .home-fixture-action { font-family:var(--fm); font-size:.82rem; color:var(--accent); font-weight:700; white-space:nowrap; }
            @media(max-width:640px){
                .bm-row{grid-template-columns:40px 1fr!important;grid-template-rows:auto auto}
                .bm-row>:nth-child(3){grid-column:2;text-align:left}
                .bm-row>:nth-child(4){grid-column:1/-1}
            }
            @media(max-width:767px) {
                .tip-fx-row-header { flex-direction:column; align-items:flex-start; }
                .tip-fx-row-header > div { width:100%; justify-content:flex-start; }
                .tip-fx-row-header > a { width:100%; }
                .tip-league-card { padding: .5rem 0; }
                .tip-fx-row { border-radius: 8px; }
                .home-fixture-card { grid-template-columns:1fr; }
                .home-fixture-teams { grid-template-columns:1fr; gap:1rem; }
                .home-team-side, .home-team-side--away { justify-content:center; text-align:center; flex-direction:column; }
                .home-score-block { width:100%; }
                .home-fixture-meta { flex-direction:column; align-items:center; justify-content:flex-start; gap:.35rem; }
                .home-fixture-meta span { white-space:normal; text-align:center; }
                .home-fixture-action { width:100%; }
            }
        </style>

        {{-- OTHER COMPETITIONS --}}
        @if($otherLeagues->isNotEmpty())
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1rem;margin-bottom:1.5rem">
            <div style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--muted);margin-bottom:.8rem">OTHER COMPETITIONS</div>
            <div class="other-comps-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.5rem">
                @foreach($otherLeagues as $otherLeague)
                @php $otherCountry = ($otherLeague->country instanceof \App\Models\Country) ? $otherLeague->country : null; @endphp
                <a href="{{ route('league.show', ['league' => $otherLeague->slug, 'date' => $activeDate]) }}" style="display:flex;align-items:center;gap:.4rem;padding:.4rem .6rem;border-radius:4px;background:var(--surface);border:1px solid var(--border);text-decoration:none;color:var(--muted);font-size:.78rem;transition:all .2s" onmouseover="this.style.color='var(--text)';this.style.borderColor='var(--dim)'" onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
                    @if($otherCountry?->flag_url)
                    <img src="{{ $otherCountry->flag_url }}" alt="{{ $otherCountry->name }}" style="width:16px;height:12px;object-fit:cover;border-radius:1px;flex-shrink:0">
                    @elseif($otherLeague->logo_url)
                    <img src="{{ $otherLeague->logo_url }}" alt="{{ $otherLeague->name }}" style="width:16px;height:16px;object-fit:contain;flex-shrink:0">
                    @else
                    <span>🏆</span>
                    @endif
                    <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $otherLeague->name }}</span>
                    @if($otherLeague->upcoming_tips_count)
                    <span style="font-size:.65rem;background:rgba(0,229,160,.12);color:var(--accent);border-radius:8px;padding:.05rem .35rem;flex-shrink:0">{{ $otherLeague->upcoming_tips_count }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ABOUT SECTION --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.25rem;margin-bottom:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.4rem;letter-spacing:.06em;color:var(--text);margin-bottom:.75rem">
                {{ config('app.name', 'SCOUT') }} — AI-Powered Betting Analysis
            </h2>
            <p style="color:var(--muted);font-size:.85rem;line-height:1.8;margin-bottom:.75rem">
                {{ config('app.name', 'SCOUT') }} is a dedicated sports betting intelligence platform powered by cutting-edge AI and real-time data from API-Football.
                Built for bettors who demand data-driven insights, the platform analyzes head-to-head history, recent form, league standings, injury reports and live odds simultaneously.
            </p>
            <p style="color:var(--muted);font-size:.85rem;line-height:1.8;margin-bottom:.75rem">
                Only signals with {{ env('CONFIDENCE_THRESHOLD', '75') }}%+ AI confidence are surfaced — cutting noise and delivering only the highest-conviction opportunities.
                Our prediction engine covers the Premier League, Bundesliga, Serie A, LaLiga, Ligue 1, Champions League, and dozens more competitions daily.
            </p>
            <p style="color:var(--muted);font-size:.85rem;line-height:1.8">
                Every prediction is backed by transparent metrics: confidence score, historical accuracy, supporting data points — so you understand <em>why</em> a signal was generated, not just what it says.
            </p>
        </div>

    </div>

    {{-- ── RIGHT SIDEBAR ── --}}
    @include('frontend.partials.home-sidebar')

</div>{{-- end two-col grid --}}

{{-- ══════════════════════════════════════════════
     BOOKMAKERS SECTION — NO GRADIENTS, FLAT DESIGN
══════════════════════════════════════════════ --}}
<div id="bookmakers" style="background:var(--surface);border-top:2px solid var(--accent);border-bottom:1px solid var(--border);padding:2.5rem 1.5rem">
    <div style="max-width:1280px;margin:0 auto">

        <div style="text-align:center;margin-bottom:2rem">
            <h2 class="bookmakers-heading" style="font-family:var(--fh);font-size:1.8rem;letter-spacing:.08em;color:var(--text);margin-bottom:.5rem">
                Best Betting Sites <span style="color:var(--accent)">{{ now()->year }}</span>
            </h2>
            <p style="font-size:.85rem;color:var(--muted);max-width:600px;margin:0 auto;line-height:1.6">
                Compare exclusive welcome offers. New players only. All bookmakers are licensed &amp; regulated.
                <span style="display:block;margin-top:.3rem;font-size:.75rem;opacity:.7">18+ | Gamble Responsibly</span>
            </p>
        </div>

        <div style="display:flex;flex-direction:column;gap:.75rem">
            @forelse($bookmakers as $i => $bm)
            <div style="background:{{ $i < 3 ? 'var(--accent)' : 'var(--surface)' }};color:{{ $i < 3 ? '#07090e' : 'var(--muted)' }};width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--fh);font-size:.9rem;font-weight:800;flex-shrink:0;display:none" class="bm-rank-mobile-only">
                <style>
                    @media (max-width: 767px) {
                        .bm-rank-mobile-only {
                            display: flex !important;
                        }
                    }
                </style>
                {{ $i + 1 }}
            </div>

                {{-- Info --}}
                <div style="min-width:0">
                    <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.25rem;flex-wrap:wrap">
                        @if($bm->logo_url)
                        <img src="{{ $bm->logo_url }}" alt="{{ $bm->name }}" style="height:22px;width:auto;object-fit:contain">
                        @endif
                        <span style="font-size:.95rem;font-weight:700;color:var(--text)">{{ $bm->name }}</span>
                        @if($bm->is_featured)
                        <span style="font-size:.6rem;background:var(--accent);color:#07090e;padding:.15rem .4rem;border-radius:3px;font-weight:700;letter-spacing:.04em">FEATURED</span>
                        @endif
                    </div>
                    <div style="font-size:.72rem;color:var(--muted);display:flex;gap:.8rem;flex-wrap:wrap;align-items:center">
                        @if($bm->rating)
                        <span>⭐ {{ number_format($bm->rating, 1) }}/5</span>
                        @endif
                        @if($bm->betMarkets->isNotEmpty())
                        <span>{{ $bm->betMarkets->pluck('name')->take(2)->implode(', ') }}</span>
                        @endif
                        @if($bm->fast_withdrawal)
                        <span style="color:var(--accent)">⚡ Fast Withdrawal</span>
                        @endif
                    </div>
                    @if($bm->key_features)
                    <div style="font-size:.7rem;color:var(--muted);margin-top:.3rem;display:flex;gap:.8rem;flex-wrap:wrap">
                        @foreach(array_slice((array)$bm->key_features, 0, 3) as $feat)
                        <span>✓ {{ $feat }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Bonus --}}
                <div style="text-align:right;min-width:130px">
                    <div style="font-size:1rem;color:var(--accent2);font-weight:800;font-family:var(--fm)">{{ $bm->welcome_offer ?? 'Welcome Bonus' }}</div>
                    <div style="font-size:.68rem;color:var(--muted)">Welcome Offer</div>
                </div>

                {{-- CTA - NO GRADIENT --}}
                <div style="display:flex;flex-direction:column;gap:.3rem;min-width:120px">
                    <a href="{{ $bm->affiliate_url }}" target="_blank" rel="nofollow noopener"
                       style="display:block;text-align:center;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.85rem;letter-spacing:.06em;padding:.55rem 1rem;border-radius:6px;text-decoration:none;font-weight:800;transition:all .15s"
                       onmouseover="this.style.transform='translateY(-1px)';this.style.opacity='0.88'"
                       onmouseout="this.style.transform='';this.style.opacity='1'">
                        CLAIM OFFER →
                    </a>
                    <span style="font-size:.6rem;color:var(--muted);text-align:center">T&amp;Cs apply</span>
                </div>

            </div>
            @empty
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.25rem;text-align:center">
                <div style="font-size:.82rem;color:var(--muted)">No bookmakers configured yet.</div>
            </div>
            @endforelse
        </div>

        {{-- Trust signals --}}
        <div style="display:flex;justify-content:center;gap:1.5rem;margin-top:1.5rem;flex-wrap:wrap;opacity:.55">
            <span style="font-size:.7rem;color:var(--muted)">🔒 SSL Secured</span>
            <span style="font-size:.7rem;color:var(--muted)">18+ Only</span>
            <span style="font-size:.7rem;color:var(--muted)">Licensed Operators</span>
            <span style="font-size:.7rem;color:var(--muted)">GambleAware Committed</span>
        </div>

        <p style="font-size:.65rem;color:var(--muted);margin-top:.75rem;text-align:center;opacity:.5">
            Bonus offers are subject to change. Always check current T&amp;Cs on the bookmaker's website.
        </p>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     FOOTER — via slot so it only renders on front pages
══════════════════════════════════════════════ --}}
<x-slot name="footer">
    @include('layouts.partials.footer')
</x-slot>

</x-app-layout>
