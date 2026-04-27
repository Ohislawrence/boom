<x-app-layout>

<x-slot name="title">AI Football Betting Tips Today — Free Predictions</x-slot>
<x-slot name="description">Get free AI-generated football betting tips updated daily. High-confidence match predictions, value bets and expert analysis from SCOUT.</x-slot>

{{-- ══════════════════════════════════════════════
     SPORT CATEGORY TAB BAR
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
     STICKY BOOKMAKER BAR (appears on scroll)
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
     HERO CTA
══════════════════════════════════════════════ --}}
<div style="background:var(--card);border-bottom:1px solid var(--border)">
    <div style="max-width:1280px;margin:0 auto;padding:1.25rem 2rem;display:flex;align-items:center;justify-content:space-between;gap:1.5rem">
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

        {{-- TODAY'S TIPS HEADER --}}
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
        <div style="display:flex;gap:.4rem;margin-bottom:1.1rem;flex-wrap:wrap">
            @foreach($dateTabs as $tab)
            <a href="{{ route('home', ['date' => $tab['date']]) }}"
               style="padding:.35rem .85rem;border-radius:20px;font-size:.76rem;font-weight:600;text-decoration:none;border:1px solid {{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--border)' }};background:{{ $activeDate === $tab['date'] ? 'rgba(0,229,160,.12)' : 'var(--surface)' }};color:{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--muted)' }};transition:all .15s"
               onmouseover="this.style.borderColor='var(--dim)';this.style.color='var(--text)'"
               onmouseout="this.style.borderColor='{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--border)' }}';this.style.color='{{ $activeDate === $tab['date'] ? 'var(--accent)' : 'var(--muted)' }}'">
                {{ $tab['label'] }}
            </a>
            @endforeach
        </div>

        <div id="tips" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
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

        {{-- COMPACT BOOKMAKER STRIP (auto-rotates every 45s) --}}
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

        @php $fixtureIdx = 0; $loadMoreLimit = 25; $topBookmaker = $bookmakers->first(); @endphp
        <style>.tip-fx-h{display:none!important}.tip-lg-h{display:none!important}
        @media(max-width:640px){.bm-row{grid-template-columns:40px 1fr!important;grid-template-rows:auto auto}.bm-row>:nth-child(3){grid-column:2;text-align:left}.bm-row>:nth-child(4){grid-column:1/-1}}
        </style>
        @forelse($fixtures as $leagueId => $leagueFixtures)
        @php
            $leagueModel = $leagueFixtures->first()->league;
            $leagueCountry = ($leagueModel && $leagueModel->country instanceof \App\Models\Country) ? $leagueModel->country : null;
            $leagueFirstIdx = $fixtureIdx;
        @endphp
        <div class="tip-league-card{{ $leagueFirstIdx >= $loadMoreLimit ? ' tip-lg-h' : '' }}" data-tip-league-first="{{ $leagueFirstIdx }}" style="background:var(--card);border:1px solid var(--border);border-radius:8px;margin-bottom:1rem;overflow:hidden">

            {{-- League header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.55rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                <div style="display:flex;align-items:center;gap:.55rem">
                    @if($leagueCountry?->flag_url)
                    <img src="{{ $leagueCountry->flag_url }}" alt="{{ $leagueCountry->name }}" style="width:18px;height:13px;object-fit:cover;border-radius:2px;flex-shrink:0">
                    @elseif($leagueModel?->logo_url)
                    <img src="{{ $leagueModel->logo_url }}" alt="{{ $leagueModel->name }}" style="width:18px;height:18px;object-fit:contain;flex-shrink:0">
                    @endif
                    <span style="font-size:.75rem;color:var(--muted)">{{ $leagueCountry?->name ?? ($leagueModel?->country ?? 'International') }}</span>
                    <span style="color:var(--border)">·</span>
                    <span style="font-size:.82rem;font-weight:600;color:var(--text)">{{ $leagueModel?->name ?? 'Unknown League' }}</span>
                </div>
                @if($leagueModel)
                <a href="{{ route('league.show', ['league' => $leagueModel->slug, 'date' => $activeDate]) }}" style="font-size:.7rem;color:var(--accent);text-decoration:none;font-weight:600;letter-spacing:.04em;white-space:nowrap">ALL →</a>
                @endif
            </div>

            {{-- Fixture rows --}}
            @foreach($leagueFixtures as $fixture)
            @php $hasTips = $fixture->tips->isNotEmpty(); $topTip = $hasTips ? $fixture->tips->first() : null; $thisIdx = $fixtureIdx++; @endphp
            <div class="tip-fx-row{{ $thisIdx >= $loadMoreLimit ? ' tip-fx-h' : '' }}"
                 data-tip-idx="{{ $thisIdx }}"
                 style="border-bottom:1px solid var(--border)">

                {{-- Match header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem 1.1rem;background:transparent;transition:background .15s"
                     onmouseover="this.style.background='rgba(255,255,255,.02)'" onmouseout="this.style.background='transparent'">
                    <a href="{{ route('fixture.betting-tips', $fixture) }}"
                       style="display:flex;align-items:center;gap:.6rem;text-decoration:none;flex:1;min-width:0">
                        {{-- Home --}}
                        <div style="display:flex;align-items:center;gap:.35rem;min-width:0">
                            @if($fixture->home_logo)
                            <img src="{{ $fixture->home_logo }}" alt="{{ $fixture->home_team }}" style="width:22px;height:22px;object-fit:contain;flex-shrink:0">
                            @else
                            <span style="width:22px;height:22px;background:var(--surface);border:1px solid var(--border);border-radius:50%;display:inline-block;flex-shrink:0"></span>
                            @endif
                            <span style="font-size:.92rem;font-weight:700;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $fixture->home_team }}</span>
                        </div>
                        <span style="font-size:.7rem;color:var(--muted);font-weight:600;flex-shrink:0">VS</span>
                        {{-- Away --}}
                        <div style="display:flex;align-items:center;gap:.35rem;min-width:0">
                            <span style="font-size:.92rem;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $fixture->away_team }}</span>
                            @if($fixture->away_logo)
                            <img src="{{ $fixture->away_logo }}" alt="{{ $fixture->away_team }}" style="width:22px;height:22px;object-fit:contain;flex-shrink:0">
                            @else
                            <span style="width:22px;height:22px;background:var(--surface);border:1px solid var(--border);border-radius:50%;display:inline-block;flex-shrink:0"></span>
                            @endif
                        </div>
                    </a>
                    <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0">
                        <span style="font-size:.72rem;color:var(--muted)">{{ $fixture->match_date->format('H:i') }}</span>
                        @if($hasTips)
                        <a href="{{ route('fixture.betting-tips', $fixture) }}"
                           style="font-size:.72rem;font-weight:600;color:var(--accent);text-decoration:none;border:1px solid rgba(0,229,160,.35);padding:.2rem .6rem;border-radius:4px;white-space:nowrap;transition:background .15s"
                           onmouseover="this.style.background='rgba(0,229,160,.1)'" onmouseout="this.style.background='transparent'">
                            View Tips →
                        </a>
                        @else
                        <a href="{{ route('fixture.betting-tips', $fixture) }}"
                           style="font-size:.72rem;font-weight:600;color:var(--muted);text-decoration:none;border:1px solid var(--border);padding:.2rem .6rem;border-radius:4px;white-space:nowrap;transition:all .15s"
                           onmouseover="this.style.borderColor='var(--dim)';this.style.color='var(--text)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                            Preview →
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Tips chips + prediction bar --}}
                @if($hasTips || $fixture->prediction_percent_home !== null)
                <div style="padding:.1rem 1.1rem 0">
                    {{-- Slim prediction probability bar --}}
                    @if($fixture->prediction_percent_home !== null)
                    @php $ph = $fixture->prediction_percent_home; $pd = $fixture->prediction_percent_draw; $pa = $fixture->prediction_percent_away; @endphp
                    <div style="display:flex;height:4px;border-radius:2px;overflow:hidden;gap:2px;margin-bottom:.4rem" title="{{ $fixture->home_team }} {{ $ph }}% / Draw {{ $pd }}% / {{ $fixture->away_team }} {{ $pa }}%">
                        <div style="width:{{ $ph }}%;background:var(--accent);border-radius:2px 0 0 2px"></div>
                        <div style="width:{{ $pd }}%;background:var(--dim)"></div>
                        <div style="width:{{ $pa }}%;background:var(--accent2);border-radius:0 2px 2px 0"></div>
                    </div>
                    @endif
                </div>
                @if($hasTips)
                <div style="padding:.25rem 1.1rem .65rem;display:flex;flex-wrap:wrap;gap:.4rem">
                    @foreach($fixture->tips as $t)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;background:{{ $t->confidence >= 75 ? 'rgba(0,229,160,.08)' : 'var(--surface)' }};border:1px solid {{ $t->confidence >= 75 ? 'rgba(0,229,160,.4)' : 'var(--border)' }};border-radius:20px;padding:.22rem .65rem;font-size:.75rem;color:var(--text);white-space:nowrap">
                        <span style="color:var(--muted);font-size:.68rem">{{ $t->market }}:</span>
                        <span style="font-weight:700">{{ $t->selection }}</span>
                        @if($t->odds)
                        <span style="font-family:var(--fm);color:var(--accent2);font-weight:700">{{ number_format($t->odds, 2) }}</span>
                        @endif
                        <span style="font-family:var(--fm);color:{{ $t->confidence >= 75 ? 'var(--accent)' : 'var(--muted)' }};font-size:.68rem">{{ $t->confidence }}%</span>
                        @if($t->is_value_bet)<span style="color:var(--accent2)">⭐</span>@endif
                    </span>
                    @endforeach
                    {{-- Quick odds (1X2) if available and no tips chips overlap --}}
                    @if($fixture->home_odds && $fixture->tips->isEmpty())
                    <span style="display:inline-flex;align-items:center;gap:.25rem;background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:.22rem .65rem;font-size:.72rem;color:var(--muted);white-space:nowrap">
                        <span style="color:var(--accent);font-family:var(--fm);font-weight:700">{{ number_format($fixture->home_odds, 2) }}</span>
                        <span style="opacity:.5">|</span>
                        <span style="font-family:var(--fm);font-weight:700">{{ number_format($fixture->draw_odds, 2) }}</span>
                        <span style="opacity:.5">|</span>
                        <span style="color:var(--accent2);font-family:var(--fm);font-weight:700">{{ number_format($fixture->away_odds, 2) }}</span>
                    </span>
                    @endif
                </div>
                @elseif($fixture->home_odds)
                <div style="padding:.25rem 1.1rem .65rem;display:flex;gap:.4rem">
                    <span style="display:inline-flex;align-items:center;gap:.25rem;background:var(--surface);border:1px solid var(--border);border-radius:20px;padding:.22rem .75rem;font-size:.72rem;color:var(--muted);white-space:nowrap">
                        <span style="color:var(--accent);font-family:var(--fm);font-weight:700">{{ number_format($fixture->home_odds, 2) }}</span>
                        <span style="opacity:.4">|</span>
                        <span style="font-family:var(--fm);font-weight:700">{{ number_format($fixture->draw_odds, 2) }}</span>
                        <span style="opacity:.4">|</span>
                        <span style="color:var(--accent2);font-family:var(--fm);font-weight:700">{{ number_format($fixture->away_odds, 2) }}</span>
                    </span>
                </div>
                @endif
                @endif

            </div>
            @endforeach

        </div>
        @empty
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:2.5rem;text-align:center;margin-bottom:1rem">
            <div style="font-size:2rem;margin-bottom:.75rem">📭</div>
            <div style="font-family:var(--fh);font-size:1.2rem;letter-spacing:.06em;color:var(--muted);margin-bottom:.5rem">No fixtures found for this date</div>
            <p style="font-size:.82rem;color:var(--muted)">Try a different date or check back once the scheduler has run.</p>

        </div>
        @endforelse

        {{-- LOAD MORE BUTTON --}}
        @php $totalFixtures = $fixtureIdx ?? 0; @endphp
        @if($totalFixtures > $loadMoreLimit)
        <div id="tips-loadmore-wrap" style="text-align:center;margin-bottom:1.5rem">
            <button id="tips-loadmore" onclick="tipsLoadMore()"
                style="background:var(--surface);border:1px solid var(--accent);border-radius:6px;color:var(--accent);font-family:var(--fh);font-size:.85rem;letter-spacing:.07em;padding:.6rem 1.8rem;cursor:pointer;transition:background .15s"
                onmouseover="this.style.background='rgba(0,229,160,.1)'" onmouseout="this.style.background='var(--surface)'">
                LOAD MORE GAMES
                <span id="tips-loadmore-count" style="font-size:.75rem;opacity:.7;margin-left:.4rem">({{ min($totalFixtures - $loadMoreLimit, 10) }} of {{ $totalFixtures - $loadMoreLimit }})</span>
            </button>
        </div>
        @endif

        <script>
        (function () {
            var BATCH = 10;
            var limit = {{ $loadMoreLimit ?? 25 }};
            var total = {{ $totalFixtures ?? 0 }};

            function syncTips() {
                document.querySelectorAll('[data-tip-idx]').forEach(function (r) {
                    r.classList.toggle('tip-fx-h', +r.dataset.tipIdx >= limit);
                });
                document.querySelectorAll('[data-tip-league-first]').forEach(function (c) {
                    c.classList.toggle('tip-lg-h', +c.dataset.tipLeagueFirst >= limit);
                });
                var remaining = total - limit;
                var wrap = document.getElementById('tips-loadmore-wrap');
                var cnt  = document.getElementById('tips-loadmore-count');
                if (wrap) {
                    wrap.style.display = remaining > 0 ? '' : 'none';
                    if (cnt && remaining > 0) cnt.textContent = '(' + Math.min(remaining, BATCH) + ' of ' + remaining + ')';
                }
            }

            window.tipsLoadMore = function () {
                limit = Math.min(limit + BATCH, total);
                syncTips();
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', syncTips);
            } else {
                syncTips();
            }
        })();
        </script>

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
    <div class="welcome-sidebar">

        {{-- FEATURED TIP --}}
        <div style="background:linear-gradient(135deg,#0d1f18 0%,#0a1a25 100%);border:1px solid var(--accent);border-radius:8px;padding:1.1rem;margin-bottom:1.25rem">
            <div style="font-size:.65rem;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);font-weight:700;margin-bottom:.5rem">⚡ Featured Tip of the Day</div>
            @if($featuredTip)
            <div style="font-family:var(--fh);font-size:1.15rem;letter-spacing:.06em;color:var(--text);margin-bottom:.3rem">{{ $featuredTip->fixture->home_team }} vs {{ $featuredTip->fixture->away_team }}</div>
            <div style="font-size:.78rem;color:var(--muted);margin-bottom:.7rem">
                {{ $featuredTip->fixture->league?->name ?? 'Football' }} — {{ $featuredTip->fixture->match_date->format('d M H:i') }}
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.8rem">
                <span style="background:rgba(0,229,160,.15);border:1px solid var(--accent);color:var(--accent);font-family:var(--fm);font-size:1rem;font-weight:700;padding:.3rem .8rem;border-radius:4px">{{ $featuredTip->selection }}</span>
                @if($featuredTip->odds)
                <div>
                    <div style="font-size:.72rem;color:var(--muted)">Best odds</div>
                    <div style="font-family:var(--fm);font-size:1.1rem;color:var(--accent2);font-weight:700">{{ number_format($featuredTip->odds, 2) }}</div>
                </div>
                @endif
                <div style="margin-left:auto;text-align:right">
                    <div style="font-size:.72rem;color:var(--muted)">AI confidence</div>
                    <div style="font-family:var(--fm);font-size:1.1rem;color:var(--accent);font-weight:700">{{ $featuredTip->confidence }}%</div>
                </div>
            </div>
            <div style="background:var(--border);border-radius:3px;height:6px;overflow:hidden;margin-bottom:.8rem">
                <div style="height:100%;width:{{ $featuredTip->confidence }}%;background:linear-gradient(90deg,var(--accent),#00b880);border-radius:3px"></div>
            </div>
            <a href="{{ route('fixture.betting-tips', $featuredTip->fixture) }}" style="display:block;text-align:center;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.95rem;letter-spacing:.08em;padding:.55rem;border-radius:5px;text-decoration:none">
                VIEW FULL ANALYSIS
            </a>
            {{-- Social proof --}}
            <div style="display:flex;align-items:center;gap:.6rem;margin-top:.75rem;padding:.6rem;background:rgba(0,229,160,.05);border-radius:6px;border:1px solid rgba(0,229,160,.1)">
                <div style="display:flex;margin-left:5px">
                    @foreach([['3b82f6','1d4ed8'],['8b5cf6','6d28d9'],['ec4899','db2777'],['f59e0b','d97706'],['10b981','059669']] as $pair)
                    <div style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#{{ $pair[0] }},#{{ $pair[1] }});border:2px solid var(--card);margin-left:-5px"></div>
                    @endforeach
                </div>
                <span style="font-size:.72rem;color:var(--muted)">
                    <strong style="color:var(--text)">2,847</strong> bettors used {{ config('app.name') }} today
                </span>
            </div>
            @else
            <div style="text-align:center;padding:.5rem 0">
                <div style="font-size:.8rem;color:var(--muted)">No featured tip for today yet.</div>

            </div>
            @endif
        </div>

        {{-- LIVE ODDS SNAPSHOT --}}
        <div id="odds" style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">Best Odds Now</span>
                <span style="font-size:.65rem;color:var(--accent);background:rgba(0,229,160,.1);padding:.2rem .5rem;border-radius:12px;font-family:var(--fm)">LIVE</span>
            </div>
            @php $oddsFixtures = $fixtures->flatten()->filter(fn($f) => $f->home_odds)->take(4); @endphp
            <div style="padding:.5rem 0">
                @forelse($oddsFixtures as $f)
                <div class="odds-row" style="padding:.45rem 1rem;border-bottom:1px solid var(--border)">
                    <a href="{{ route('fixture.betting-tips', $f) }}"
                       style="font-size:.75rem;color:var(--text);display:block;margin-bottom:.3rem;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;transition:color .15s"
                       onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">
                        {{ $f->home_team }} vs {{ $f->away_team }}
                    </a>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.3rem">
                        @foreach(['1' => $f->home_odds, 'X' => $f->draw_odds, '2' => $f->away_odds] as $label => $val)
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:4px;text-align:center;padding:.2rem .4rem">
                            <div style="font-size:.6rem;color:var(--muted)">{{ $label }}</div>
                            <div style="font-family:var(--fm);font-size:.8rem;color:var(--accent2);font-weight:700">{{ number_format($val, 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div style="padding:1rem;text-align:center;font-size:.78rem;color:var(--muted)">No odds available for today.</div>
                @endforelse
            </div>
        </div>

        {{-- RECENT RESULTS --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
            <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">Recent Results</span>
                <span style="font-size:.65rem;color:var(--accent);font-family:var(--fm)">VERIFIED</span>
            </div>
            @forelse($recentSettledTips as $tip)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.55rem 1rem;border-bottom:1px solid var(--border)">
                <div style="min-width:0;flex:1">
                    <div style="font-size:.75rem;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}
                    </div>
                    <div style="font-size:.65rem;color:var(--muted)">{{ $tip->market }}: {{ $tip->selection }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0">
                    @if($tip->odds)
                    <span style="font-family:var(--fm);font-size:.75rem;color:var(--accent2)">{{ number_format($tip->odds, 2) }}</span>
                    @endif
                    <span style="font-family:var(--fm);font-size:.72rem;font-weight:700;padding:.18rem .5rem;border-radius:4px;background:{{ $tip->result === 'won' ? 'rgba(0,229,160,.12)' : 'rgba(255,77,109,.1)' }};color:{{ $tip->result === 'won' ? 'var(--accent)' : 'var(--accent3)' }}">
                        {{ strtoupper($tip->result) }}
                    </span>
                </div>
            </div>
            @empty
            <div style="padding:1rem;text-align:center;font-size:.78rem;color:var(--muted)">No settled tips yet — check back soon.</div>
            @endforelse
        </div>

        {{-- CTA --}}
        @guest
        <div style="background:linear-gradient(135deg,#0d1f2e,#0a1520);border:1px solid var(--border);border-radius:8px;padding:1.1rem;text-align:center">
            <div style="font-family:var(--fh);font-size:1.2rem;letter-spacing:.06em;color:var(--text);margin-bottom:.4rem">Get Full Access</div>
            <p style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem;line-height:1.5">Unlock all predictions, confidence scores, and AI match analysis.</p>
            @if(Route::has('register'))
            <a href="{{ route('register') }}" class="scout-btn scout-btn-primary" style="display:block;text-align:center;margin-bottom:.5rem;font-size:.85rem">
                Create Free Account
            </a>
            @endif
            @if(Route::has('login'))
            <a href="{{ route('login') }}" class="scout-btn scout-btn-outline" style="display:block;text-align:center;font-size:.85rem">
                Sign In
            </a>
            @endif
        </div>
        @endguest

    </div>

</div>{{-- end two-col grid --}}

{{-- ══════════════════════════════════════════════
     BOOKMAKERS SECTION — High Conversion
══════════════════════════════════════════════ --}}
<div id="bookmakers" style="background:linear-gradient(180deg,var(--surface) 0%,#0a0f18 100%);border-top:2px solid var(--accent);border-bottom:1px solid var(--border);padding:2.5rem 1.5rem">
    <div style="max-width:1280px;margin:0 auto">

        <div style="text-align:center;margin-bottom:2rem">
            <h2 style="font-family:var(--fh);font-size:1.8rem;letter-spacing:.08em;color:var(--text);margin-bottom:.5rem">
                Best Betting Sites <span style="color:var(--accent)">{{ now()->year }}</span>
            </h2>
            <p style="font-size:.85rem;color:var(--muted);max-width:600px;margin:0 auto;line-height:1.6">
                Compare exclusive welcome offers. New players only. All bookmakers are licensed &amp; regulated.
                <span style="display:block;margin-top:.3rem;font-size:.75rem;opacity:.7">18+ | Gamble Responsibly</span>
            </p>
        </div>

        <div style="display:flex;flex-direction:column;gap:.75rem">
            @forelse($bookmakers as $i => $bm)
            <div class="bm-row" style="background:var(--card);border:1px solid var(--border);border-radius:10px;display:grid;grid-template-columns:40px 1fr auto auto;align-items:center;gap:1.25rem;padding:1rem 1.25rem;transition:all .2s;position:relative"
                 onmouseover="this.style.borderColor='var(--accent)';this.style.transform='translateX(3px)';this.style.boxShadow='0 4px 20px rgba(0,0,0,.25)'"
                 onmouseout="this.style.borderColor='var(--border)';this.style.transform='';this.style.boxShadow=''">

                {{-- Rank badge --}}
                <div style="background:{{ $i < 3 ? 'linear-gradient(135deg,var(--accent),#00b880)' : 'var(--surface)' }};color:{{ $i < 3 ? '#07090e' : 'var(--muted)' }};width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--fh);font-size:.9rem;font-weight:800;flex-shrink:0">
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

                {{-- CTA --}}
                <div style="display:flex;flex-direction:column;gap:.3rem;min-width:120px">
                    <a href="{{ $bm->affiliate_url }}" target="_blank" rel="nofollow noopener"
                       style="display:block;text-align:center;background:linear-gradient(135deg,#00e5a0,#00b880);color:#07090e;font-family:var(--fh);font-size:.85rem;letter-spacing:.06em;padding:.55rem 1rem;border-radius:6px;text-decoration:none;font-weight:800;box-shadow:0 2px 10px rgba(0,229,160,.25);transition:all .15s"
                       onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 15px rgba(0,229,160,.35)'"
                       onmouseout="this.style.transform='';this.style.boxShadow='0 2px 10px rgba(0,229,160,.25)'">
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
