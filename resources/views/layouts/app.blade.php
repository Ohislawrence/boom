<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $pageTitle    = trim(strip_tags((string) ($title ?? '')));
            $pageDesc     = trim(strip_tags((string) ($description ?? '')));
            $pageCanon    = trim(strip_tags((string) ($canonical ?? ''))) ?: url()->current();
            $fullTitle    = $pageTitle ? $pageTitle . ' | boomodd' : 'boomodd — AI Football Betting Tips & Predictions';
            $defaultDesc  = 'boomodd uses AI to generate expert football betting tips and predictions. High-confidence match previews, value bets and odds analysis updated daily.';
            $metaDesc     = $pageDesc ?: $defaultDesc;
            $pageImage    = trim(strip_tags((string) ($image ?? asset('images/boomodd-og-image.png'))));
        @endphp

        <title>{{ $fullTitle }}</title>

        <!-- Primary meta -->
        <meta name="description" content="{{ $metaDesc }}">
        <link rel="canonical" href="{{ $pageCanon }}">
        <meta name="robots" content="index, follow">

        <!-- Open Graph -->
            <meta property="og:site_name" content="boomodd">
            <meta property="og:locale" content="en_GB">
            <meta property="og:type" content="website">
            <meta property="og:url" content="{{ $pageCanon }}">
            <meta property="og:title" content="{{ $fullTitle }}">
            <meta property="og:description" content="{{ $metaDesc }}">
            <meta property="og:image" content="{{ $pageImage }}">
            <meta property="og:logo" content="{{ asset('images/favico-boomodd.png') }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@boomodd">
        <meta name="twitter:title" content="{{ $fullTitle }}">
        <meta name="twitter:description" content="{{ $metaDesc }}">
        <meta name="twitter:image" content="{{ $pageImage }}">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favico-boomodd.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Per-page structured data & head overrides -->
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <div class="scout-page-header">
                    <h1 class="scout-page-title">{{ $header }}</h1>
                </div>
            @endisset

            <!-- Page Content -->
            <main class="scout-main">
                @if(session('success'))
                <div style="background:rgba(0,229,160,.12);border:1px solid rgba(0,229,160,.35);border-radius:8px;padding:.75rem 1rem 0.75rem 1rem;margin:1rem 1rem 0;font-size:.84rem;color:var(--accent);display:flex;align-items:center;gap:.5rem">
                    <span>✓</span> {{ session('success') }}
                </div>
                @endif
                @if(session('info'))
                <div style="background:rgba(99,179,237,.1);border:1px solid rgba(99,179,237,.35);border-radius:8px;padding:.75rem 1rem;margin:1rem 1rem 0;font-size:.84rem;color:#63b3ed;display:flex;align-items:flex-start;gap:.5rem">
                    <span style="margin-top:1px">ℹ</span> {{ session('info') }}
                </div>
                @endif
                {{ $slot }}
            </main>
        </div>

        <!-- Front-page footer (only when $footer slot is provided) -->
        {{ $footer ?? '' }}

        <!-- ══ MOBILE BOTTOM NAVIGATION ══ -->
        <nav class="scout-bottom-nav" aria-label="Mobile navigation">

            <a href="{{ url('/') }}" class="scout-bottom-nav-item {{ request()->is('/') && !request()->is('dashboard*') ? 'active' : '' }}" aria-label="Home">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Home</span>
            </a>

            <a href="{{ route('fixture.betting-tips.index') }}" class="scout-bottom-nav-item {{ request()->routeIs('fixture.betting-tips*') ? 'active' : '' }}" aria-label="AI Tips">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <span>AI Tips</span>
            </a>

            <a href="{{ route('bookmakers.index') }}" class="scout-bottom-nav-item {{ request()->routeIs('bookmakers.*') ? 'active' : '' }}" aria-label="Bookmakers">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                <span>Bookmakers</span>
            </a>

            <!-- Accumulator item -->
            <a href="{{ route('accumulator.index') }}" class="scout-bottom-nav-item {{ request()->routeIs('accumulator*') ? 'active' : '' }}" aria-label="Accumulator">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>
                <span>Accumulator</span>
            </a>

            @auth
            @if(Auth::user()->hasRole('tipster') || Auth::user()->hasRole('admin'))
            <a href="{{ route('tipster.dashboard') }}" class="scout-bottom-nav-item {{ request()->routeIs('tipster.dashboard') ? 'active' : '' }}" aria-label="Tipster Dashboard">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Tipster</span>
            </a>
            <a href="{{ route('tipster.tips.create') }}" class="scout-bottom-nav-item {{ request()->routeIs('tipster.tips.create') ? 'active' : '' }}" aria-label="Submit a Tip">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Submit</span>
            </a>
            @else
            <a href="{{ route('profile.edit') }}" class="scout-bottom-nav-item {{ request()->routeIs('profile.*') || request()->routeIs('dashboard') ? 'active' : '' }}" aria-label="My Account">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Account</span>
            </a>
            @endif
            @else
            <a href="{{ Route::has('login') ? route('login') : '#' }}" class="scout-bottom-nav-item" aria-label="Sign In">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                <span>Sign In</span>
            </a>
            @endauth

        </nav>

        <!-- ══ CLICK TRACKING ══ -->
        <script>
        (function () {
            var TRACK_URL = '{{ route('track.click') }}';
            var CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            var HOST      = location.hostname;

            function send(payload) {
                var data = new FormData();
                Object.keys(payload).forEach(function(k){ if(payload[k] != null) data.append(k, payload[k]); });
                data.append('_token', CSRF);
                if (navigator.sendBeacon) {
                    // sendBeacon can't set headers, so use fetch with keepalive for reliability
                }
                fetch(TRACK_URL, {
                    method: 'POST',
                    body: data,
                    keepalive: true,
                    headers: { 'X-CSRF-TOKEN': CSRF }
                }).catch(function(){});
            }

            function classify(el) {
                var href = el.href || '';
                // Explicit data-track-type attribute wins
                if (el.dataset.trackType) return el.dataset.trackType;
                // Affiliate links (bookmaker redirects or explicit affiliate class)
                if (el.classList.contains('bm-cta') || el.classList.contains('affiliate-link')) return 'affiliate';
                // External links
                if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
                    try {
                        var u = new URL(href, location.href);
                        if (u.hostname && u.hostname !== HOST) return 'external';
                    } catch(e){}
                }
                return 'nav';
            }

            document.addEventListener('click', function (e) {
                var el = e.target.closest('a[href], button[data-track-type]');
                if (!el) return;
                var href = el.href || el.dataset.trackUrl || '';
                if (!href || href === '#' || href.startsWith('javascript')) return;

                var type  = classify(el);
                var label = el.dataset.trackLabel
                           || el.getAttribute('aria-label')
                           || el.innerText?.trim().slice(0, 100)
                           || '';

                send({
                    event_type: type,
                    label:      label,
                    target_url: href.slice(0, 500),
                    page_url:   location.href.slice(0, 500),
                    referrer:   document.referrer.slice(0, 500),
                });
            }, { passive: true, capture: true });
        })();
        </script>
    </body>
</html>

