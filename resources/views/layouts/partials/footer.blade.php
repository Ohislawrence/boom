<footer class="scout-footer">
    <div class="scout-footer-inner">

        <div class="scout-footer-columns">
            <div>
                <div class="scout-footer-brand">{{ config('app.name','SCOUT') }}</div>
                <p class="scout-footer-copy">AI-powered betting analysis. Data-backed predictions. High-confidence signals only.</p>
            </div>
            <div>
                <div class="scout-footer-section-title">Predictions</div>
                <a href="{{ route('fixture.betting-tips.index') }}" class="scout-footer-link">All Betting Tips</a>
                @php
                    $footerNow = \Carbon\Carbon::now(session('geo.timezone') ?: config('app.timezone'));
                @endphp
                <a href="{{ route('fixture.betting-tips.index', ['date' => $footerNow->toDateString()]) }}" class="scout-footer-link">Today's Tips</a>
                <a href="{{ route('fixture.betting-tips.index', ['date' => $footerNow->copy()->addDay()->toDateString()]) }}" class="scout-footer-link">Tomorrow's Tips</a>
                <a href="{{ route('accumulator.index') }}" class="scout-footer-link">Acca Builder</a>
                @foreach(['Tennis Tips','Basketball Tips','Rugby Tips','Cricket Tips'] as $link)
                <a href="#" class="scout-footer-link">{{ $link }}</a>
                @endforeach
            </div>
            <div>
                <div class="scout-footer-section-title">Top Betting Sites</div>
                @if(!empty($footerBookmakers) && $footerBookmakers->count())
                    @foreach($footerBookmakers as $fbm)
                    <a href="{{ route('bookmakers.show', $fbm->slug) }}" class="scout-footer-bookmaker-link">
                        @if($fbm->logo_url)
                        <img src="{{ $fbm->logo_url }}" alt="{{ $fbm->name }}" class="scout-footer-logo" loading="lazy">
                        @else
                        <span class="scout-footer-logo-fallback">{{ strtoupper(substr($fbm->name,0,1)) }}</span>
                        @endif
                        <span>{{ $fbm->name }}</span>
                    </a>
                    @endforeach
                    <a href="{{ route('bookmakers.index') }}" class="scout-footer-link-cta">View all sites →</a>
                @else
                    @foreach(['Best Betting Sites','Welcome Bonuses','Promo Codes','Odds Comparison'] as $link)
                    <a href="{{ route('bookmakers.index') }}" class="scout-footer-link">{{ $link }}</a>
                    @endforeach
                @endif
            </div>
            <div>
                <div class="scout-footer-section-title">Platform</div>
                <a href="{{ route('page.about') }}" class="scout-footer-link">About Us</a>
                <a href="{{ route('page.how-it-works') }}" class="scout-footer-link">How It Works</a>
                <a href="{{ route('page.editorial-policy') }}" class="scout-footer-link">Editorial Policy</a>
                <a href="{{ route('page.privacy-notice') }}" class="scout-footer-link">Privacy Notice</a>
                <a href="{{ route('page.responsible-gambling') }}" class="scout-footer-link">Responsible Gambling</a>
            </div>
        </div>

        <div class="scout-footer-bottom">
            <div class="scout-footer-note">
                Copyright &copy; {{ date('Y') }} {{ config('app.name','SCOUT') }} &mdash; All rights reserved
            </div>
            <div class="scout-footer-bottom-right">
                <span class="scout-footer-badge">18+</span>
                <span class="scout-footer-note">Please Gamble Responsibly</span>
            </div>
        </div>

    </div>
</footer>
