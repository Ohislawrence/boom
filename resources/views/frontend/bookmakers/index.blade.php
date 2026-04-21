<x-app-layout>

<x-slot name="title">Best Betting Sites {{ date('Y') }} — Bookmaker Reviews &amp; Offers</x-slot>
<x-slot name="description">Compare the best online bookmakers for {{ date('Y') }}. SCOUT reviews betting sites on odds, features, welcome offers and trustworthiness to help you find the best bet.</x-slot>

<style>
.bk-hero-card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:1.25rem;position:relative;overflow:hidden;display:flex;flex-direction:column;gap:.6rem}
.bk-hero-card.top{border-color:var(--accent)}
.bk-logo{width:40px;height:40px;border-radius:6px;object-fit:contain;background:var(--card2);padding:3px;border:1px solid var(--border)}
.bk-logo-fallback{width:40px;height:40px;border-radius:6px;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:1.1rem;display:inline-flex;align-items:center;justify-content:center;letter-spacing:.04em;flex-shrink:0}
.bk-feat{font-size:.64rem;background:var(--surface);color:var(--muted);padding:.12rem .38rem;border-radius:3px;border:1px solid var(--border);white-space:nowrap}
.bk-badge{display:inline-flex;align-items:center;gap:.2rem;font-size:.62rem;font-weight:700;letter-spacing:.05em;padding:.18rem .45rem;border-radius:3px}
.bk-badge.yes{background:rgba(0,229,160,.12);color:var(--accent);border:1px solid rgba(0,229,160,.25)}
.bk-badge.no{background:rgba(255,255,255,.04);color:var(--muted);border:1px solid var(--border)}
.star-on{color:var(--accent2)}
.star-off{color:var(--border)}
</style>

<div style="max-width:1280px;margin:0 auto;padding:1.5rem 2rem">

    <div style="margin-bottom:1.5rem">
        <h1 style="font-family:var(--fh);font-size:1.8rem;letter-spacing:.08em;color:var(--text)">Bookmakers</h1>
        <p style="font-size:.82rem;color:var(--muted);margin-top:.25rem">Compare the best betting sites. We earn commission on sign-ups — always at no extra cost to you.</p>
    </div>

    {{-- Top picks hero grid --}}
    @if($bookmakers->count() >= 3)
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:1rem;margin-bottom:2rem">
        @foreach($bookmakers->take(3) as $i => $bm)
        <div class="bk-hero-card {{ $i === 0 ? 'top' : '' }}">
            @if($i === 0)
            <div style="position:absolute;top:.6rem;right:.6rem;font-size:.6rem;background:var(--accent);color:#07090e;padding:.15rem .45rem;border-radius:3px;letter-spacing:.06em;font-weight:700">TOP PICK</div>
            @endif

            {{-- Logo + name row --}}
            <div style="display:flex;align-items:center;gap:.7rem">
                @if($bm->logo_url)
                <img src="{{ $bm->logo_url }}" alt="{{ $bm->name }} logo" class="bk-logo" loading="lazy">
                @else
                <div class="bk-logo-fallback">{{ strtoupper(substr($bm->name,0,2)) }}</div>
                @endif
                <div>
                    <div style="font-family:var(--fh);font-size:1.2rem;letter-spacing:.06em;color:var(--text)">{{ $bm->name }}</div>
                    @if($bm->founded_year)
                    <div style="font-size:.67rem;color:var(--dim)">Est. {{ $bm->founded_year }} &bull; {{ $bm->license ?? 'Licensed' }}</div>
                    @endif
                </div>
            </div>

            {{-- Star rating --}}
            <div style="display:flex;align-items:center;gap:.25rem">
                @for($s=1;$s<=5;$s++)
                <span class="{{ $s <= round($bm->rating / 2) ? 'star-on' : 'star-off' }}" style="font-size:.9rem">★</span>
                @endfor
                <span style="font-size:.72rem;color:var(--muted);margin-left:.2rem">{{ number_format($bm->rating, 1) }}/10</span>
                @if($bm->fast_withdrawal)
                <span class="bk-badge yes" style="margin-left:.5rem">⚡ Fast W/D</span>
                @endif
            </div>

            {{-- Welcome offer --}}
            @if($bm->welcome_offer)
            <div style="background:rgba(0,229,160,.06);border:1px solid rgba(0,229,160,.2);border-radius:5px;padding:.5rem .75rem">
                <div style="font-size:.65rem;color:var(--accent);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.12rem">Welcome Offer</div>
                <div style="font-size:.85rem;color:var(--text);font-weight:600">{{ $bm->welcome_offer }}</div>
            </div>
            @endif

            {{-- Key features --}}
            @if($bm->key_features && count($bm->key_features))
            <div style="display:flex;flex-wrap:wrap;gap:.3rem">
                @foreach($bm->key_features as $feat)
                <span class="bk-feat">{{ $feat }}</span>
                @endforeach
            </div>
            @endif

            {{-- Min deposit + withdrawal time --}}
            <div style="display:flex;gap:1.5rem">
                @if($bm->min_deposit)
                <div>
                    <div style="font-size:.62rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Min Deposit</div>
                    <div style="font-size:.82rem;color:var(--text);font-weight:600">{{ $bm->min_deposit }}</div>
                </div>
                @endif
                @if($bm->withdrawal_time)
                <div>
                    <div style="font-size:.62rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Withdrawal</div>
                    <div style="font-size:.82rem;color:var(--text);font-weight:600">{{ $bm->withdrawal_time }}</div>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:.5rem;margin-top:auto">
                <a href="{{ $bm->affiliate_url }}" target="_blank" rel="nofollow noopener"
                   style="flex:1;text-align:center;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.85rem;letter-spacing:.06em;padding:.5rem;border-radius:5px;text-decoration:none">
                    JOIN NOW
                </a>
                <a href="{{ route('bookmakers.show', $bm->slug) }}"
                   style="flex:1;text-align:center;background:transparent;color:var(--text);border:1px solid var(--border);font-size:.82rem;padding:.5rem;border-radius:5px;text-decoration:none"
                   onmouseover="this.style.borderColor='var(--dim)'" onmouseout="this.style.borderColor='var(--border)'">
                    Read Review
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- All bookmakers cards --}}
    <div style="margin-bottom:.75rem;display:flex;align-items:center;justify-content:space-between">
        <span style="font-family:var(--fh);font-size:1rem;letter-spacing:.06em;color:var(--text)">All Bookmakers</span>
        <span style="font-size:.72rem;color:var(--muted)">{{ $bookmakers->count() }} sites compared</span>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem">
        @foreach($bookmakers as $i => $bm)
        <div class="bk-hero-card {{ $bm->is_featured && $i === 0 ? 'top' : '' }}" style="position:relative">

            @if($bm->is_featured && $i === 0)
            <div style="position:absolute;top:.6rem;right:.6rem;font-size:.6rem;background:var(--accent);color:#07090e;padding:.15rem .45rem;border-radius:3px;letter-spacing:.06em;font-weight:700">TOP PICK</div>
            @endif

            {{-- Logo + name --}}
            <div style="display:flex;align-items:center;gap:.7rem">
                @if($bm->logo_url)
                <img src="{{ $bm->logo_url }}" alt="{{ $bm->name }} logo" class="bk-logo" loading="lazy">
                @else
                <div class="bk-logo-fallback">{{ strtoupper(substr($bm->name,0,2)) }}</div>
                @endif
                <div>
                    <div style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--text)">{{ $bm->name }}</div>
                    @if($bm->founded_year)
                    <div style="font-size:.65rem;color:var(--dim)">Est. {{ $bm->founded_year }}@if($bm->license) &bull; {{ $bm->license }}@endif</div>
                    @elseif($bm->license)
                    <div style="font-size:.65rem;color:var(--dim)">{{ $bm->license }}</div>
                    @endif
                </div>
            </div>

            {{-- Rating + fast W/D --}}
            <div style="display:flex;align-items:center;gap:.25rem">
                @for($s=1;$s<=5;$s++)
                <span class="{{ $s <= round($bm->rating / 2) ? 'star-on' : 'star-off' }}" style="font-size:.9rem">★</span>
                @endfor
                <span style="font-size:.72rem;color:var(--muted);margin-left:.2rem">{{ number_format($bm->rating, 1) }}/10</span>
                @if($bm->fast_withdrawal)
                <span class="bk-badge yes" style="margin-left:.5rem">⚡ Fast W/D</span>
                @endif
            </div>

            {{-- Welcome offer --}}
            @if($bm->welcome_offer)
            <div style="background:rgba(0,229,160,.06);border:1px solid rgba(0,229,160,.2);border-radius:5px;padding:.45rem .7rem">
                <div style="font-size:.62rem;color:var(--accent);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.1rem">Welcome Offer</div>
                <div style="font-size:.83rem;color:var(--text);font-weight:600">{{ $bm->welcome_offer }}</div>
            </div>
            @endif

            {{-- Quick stats --}}
            <div style="display:flex;gap:1.25rem">
                @if($bm->min_deposit)
                <div>
                    <div style="font-size:.6rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Min Dep.</div>
                    <div style="font-size:.8rem;color:var(--text);font-weight:600">{{ $bm->min_deposit }}</div>
                </div>
                @endif
                @if($bm->withdrawal_time)
                <div>
                    <div style="font-size:.6rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Withdrawal</div>
                    <div style="font-size:.8rem;color:var(--text);font-weight:600">{{ $bm->withdrawal_time }}</div>
                </div>
                @endif
                <div>
                    <div style="font-size:.6rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Live</div>
                    <div style="font-size:.8rem;color:var(--text);font-weight:600">{{ $bm->live_betting ? 'Yes' : 'No' }}</div>
                </div>
                <div>
                    <div style="font-size:.6rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">App</div>
                    <div style="font-size:.8rem;color:var(--text);font-weight:600">{{ $bm->mobile_app ? 'Yes' : 'No' }}</div>
                </div>
            </div>

            {{-- Key features --}}
            @if($bm->key_features && count($bm->key_features))
            <div style="display:flex;flex-wrap:wrap;gap:.3rem">
                @foreach($bm->key_features as $feat)
                <span class="bk-feat">{{ $feat }}</span>
                @endforeach
            </div>
            @endif

            {{-- Actions --}}
            <div style="display:flex;gap:.5rem;margin-top:auto">
                <a href="{{ $bm->affiliate_url }}" target="_blank" rel="nofollow noopener"
                   style="flex:1;text-align:center;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.82rem;letter-spacing:.06em;padding:.5rem;border-radius:5px;text-decoration:none">
                    JOIN NOW
                </a>
                <a href="{{ route('bookmakers.show', $bm->slug) }}"
                   style="flex:1;text-align:center;background:transparent;color:var(--text);border:1px solid var(--border);font-size:.82rem;padding:.5rem;border-radius:5px;text-decoration:none"
                   onmouseover="this.style.borderColor='var(--dim)'" onmouseout="this.style.borderColor='var(--border)'">
                    Read Review
                </a>
            </div>

        </div>
        @endforeach
    </div>

    {{-- Disclaimer --}}
    <p style="font-size:.7rem;color:var(--muted);margin-top:1.25rem;line-height:1.7">
        ⚠️ Gambling can be addictive. Please gamble responsibly. 18+ only. T&Cs apply. We may receive a commission from bookmakers listed on this page.
        This commission never affects the tips we provide. <a href="#" style="color:var(--accent);text-decoration:none">Learn about our affiliate model.</a>
    </p>

</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>

