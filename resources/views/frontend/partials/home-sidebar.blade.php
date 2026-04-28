{{-- Shared homepage sidebar used on welcome page and fixtures index --}}
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
        @php
            $oddsFixtures = collect();
            if (isset($fixtures)) {
                $oddsFixtures = $fixtures->flatten();
            }
            if ($oddsFixtures->isEmpty()) {
                if (isset($latestUnplayedFixtures)) {
                    $oddsFixtures = $oddsFixtures->merge($latestUnplayedFixtures);
                }
                if (isset($latestPlayedFixtures)) {
                    $oddsFixtures = $oddsFixtures->merge($latestPlayedFixtures);
                }
            }
            $oddsFixtures = $oddsFixtures->filter(fn($f) => $f->home_odds)->take(4);
        @endphp
        <div style="padding:.5rem 0">
            @forelse($oddsFixtures as $f)
            <div class="odds-row" style="padding:.45rem 1rem;border-bottom:1px solid var(--border)">
                <a href="{{ route('fixture.betting-tips', $f) }}" style="font-size:.75rem;color:var(--text);display:block;margin-bottom:.3rem;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;transition:color .15s" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">
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
                <div style="font-size:.75rem;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</div>
                <div style="font-size:.65rem;color:var(--muted)">{{ $tip->market }}: {{ $tip->selection }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0">
                @if($tip->odds)
                <span style="font-family:var(--fm);font-size:.75rem;color:var(--accent2)">{{ number_format($tip->odds, 2) }}</span>
                @endif
                <span style="font-family:var(--fm);font-size:.72rem;font-weight:700;padding:.18rem .5rem;border-radius:4px;background:{{ $tip->result === 'win' ? 'rgba(0,229,160,.12)' : 'rgba(255,77,109,.1)' }};color:{{ $tip->result === 'win' ? 'var(--accent)' : 'var(--accent3)' }}">{{ strtoupper($tip->result) }}</span>
            </div>
        </div>
        @empty
        <div style="padding:1rem;text-align:center;font-size:.78rem;color:var(--muted)">No recent results available.</div>
        @endforelse
    </div>

</div>
