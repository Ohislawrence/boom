<footer style="background:var(--surface);border-top:1px solid var(--border);padding:2rem 1.5rem">
    <style>
        @media (max-width: 860px) {
            .footer-menu-columns { display: none; }
        }
    </style>
    <div style="max-width:1280px;margin:0 auto">

        <div class="footer-menu-columns" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <div style="font-family:var(--fh);font-size:1.4rem;letter-spacing:.12em;color:var(--accent);margin-bottom:.5rem">{{ config('app.name','SCOUT') }}</div>
                <p style="font-size:.78rem;color:var(--muted);line-height:1.7">AI-powered betting analysis. Data-backed predictions. High-confidence signals only.</p>
            </div>
            <div>
                <div style="font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;color:var(--dim);font-weight:700;margin-bottom:.6rem">Predictions</div>
                <a href="{{ route('fixture.betting-tips.index') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">All Betting Tips</a>
                <a href="{{ route('fixture.betting-tips.index', ['date' => today()->toDateString()]) }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">Today's Tips</a>
                <a href="{{ route('fixture.betting-tips.index', ['date' => today()->addDay()->toDateString()]) }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">Tomorrow's Tips</a>
                <a href="{{ route('accumulator.index') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">Acca Builder</a>
                @foreach(['Tennis Tips','Basketball Tips','Rugby Tips','Cricket Tips'] as $link)
                <a href="#" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">{{ $link }}</a>
                @endforeach
            </div>
            <div>
                <div style="font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;color:var(--dim);font-weight:700;margin-bottom:.6rem">Top Betting Sites</div>
                @if(!empty($footerBookmakers) && $footerBookmakers->count())
                    @foreach($footerBookmakers as $fbm)
                    <a href="{{ route('bookmakers.show', $fbm->slug) }}"
                       style="display:flex;align-items:center;gap:.45rem;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.35rem"
                       onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">
                        @if($fbm->logo_url)
                        <img src="{{ $fbm->logo_url }}" alt="{{ $fbm->name }}" style="width:16px;height:16px;border-radius:2px;object-fit:contain;flex-shrink:0" loading="lazy">
                        @else
                        <span style="width:16px;height:16px;border-radius:2px;background:var(--accent);color:#07090e;font-size:.55rem;font-family:var(--fh);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr($fbm->name,0,1)) }}</span>
                        @endif
                        <span>{{ $fbm->name }}</span>
                    </a>
                    @endforeach
                    <a href="{{ route('bookmakers.index') }}" style="display:block;font-size:.72rem;color:var(--accent);text-decoration:none;margin-top:.35rem">View all sites →</a>
                @else
                    @foreach(['Best Betting Sites','Welcome Bonuses','Promo Codes','Odds Comparison'] as $link)
                    <a href="{{ route('bookmakers.index') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">{{ $link }}</a>
                    @endforeach
                @endif
            </div>
            <div>
                <div style="font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;color:var(--dim);font-weight:700;margin-bottom:.6rem">Platform</div>
                <a href="{{ route('page.about') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">About Us</a>
                <a href="{{ route('page.how-it-works') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">How It Works</a>
                <a href="{{ route('page.editorial-policy') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">Editorial Policy</a>
                <a href="{{ route('page.privacy-notice') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">Privacy Notice</a>
                <a href="{{ route('page.responsible-gambling') }}" style="display:block;font-size:.78rem;color:var(--muted);text-decoration:none;margin-bottom:.25rem" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">Responsible Gambling</a>
            </div>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem">
            <div style="font-size:.72rem;color:var(--muted)">
                Copyright &copy; {{ date('Y') }} {{ config('app.name','SCOUT') }} &mdash; All rights reserved
            </div>
            <div style="display:flex;align-items:center;gap:.75rem">
                <span style="background:var(--accent3);color:#fff;font-size:.65rem;font-weight:700;padding:.2rem .5rem;border-radius:3px">18+</span>
                <span style="font-size:.72rem;color:var(--muted)">Please Gamble Responsibly</span>
            </div>
        </div>

    </div>
</footer>
