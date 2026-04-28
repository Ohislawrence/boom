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
    <body class="font-sans antialiased" x-data="{ mobileMenuOpen: false }">
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
                <div x-data="{ show: true }"
                    x-show="show"
                    x-init="setTimeout(() => show = false, 5000)"
                    class="scout-alert scout-alert-success"
                    role="alert">
                    <span class="scout-alert-icon">✓</span>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="scout-alert-close">✕</button>
                </div>
                @endif
                @if(session('info'))
                <div style="background:rgba(99,179,237,.1);border:1px solid rgba(99,179,237,.35);border-radius:8px;padding:.75rem 1rem;margin:1rem 1rem 0;font-size:.84rem;color:#63b3ed;display:flex;align-items:flex-start;gap:.5rem">
                    <span style="margin-top:1px">ℹ</span> {{ session('info') }}
                </div>
                @endif
                @if($errors->any())
                <div class="scout-err-box mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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

            <a href="#" data-mobile-menu-toggle @click.prevent="mobileMenuOpen = true" class="scout-bottom-nav-item" :class="{ 'active': mobileMenuOpen }" aria-label="Menu" :aria-expanded="mobileMenuOpen.toString()">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <span>Menu</span>
            </a>

        </nav>

        <div id="mobileMenuBackdrop"
             x-show="mobileMenuOpen"
             x-cloak
             aria-hidden="true"
             @click="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="scout-mobile-menu-backdrop">
        </div>

        <div id="mobileMenu"
             class="scout-mobile-menu"
             x-show="mobileMenuOpen"
             x-cloak
             @click.away="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             aria-modal="true"
             role="dialog"
             aria-label="Mobile menu">

             <div class="scout-mobile-menu-header">
                 <span>Menu</span>
                 <button type="button" @click="mobileMenuOpen = false" aria-label="Close menu" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.3rem;">✕</button>
             </div>

             <!-- Mobile Search -->
             <div class="scout-mobile-search" style="padding: 1rem 1.5rem;">
                 <form action="{{ route('search') }}" method="GET" class="scout-search-form">
                     <input name="q" type="search" value="{{ request('q') }}" placeholder="Search teams, tips, leagues or bookmakers" class="scout-search-input">
                     <button type="submit" class="scout-search-btn">Search</button>
                 </form>
             </div>

             <!-- Nav Links -->
             <nav>
                 <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="scout-mobile-item {{ request()->is('/') && !request()->is('dashboard*') ? 'active' : '' }}">Home</a>
                 <a href="{{ route('fixture.betting-tips.index') }}" @click="mobileMenuOpen = false" class="scout-mobile-item {{ request()->routeIs('fixture.betting-tips*') ? 'active' : '' }}">AI Tips</a>
                 <a href="{{ route('accumulator.index') }}" @click="mobileMenuOpen = false" class="scout-mobile-item {{ request()->routeIs('accumulator.*') ? 'active' : '' }}">Accumulator</a>
                 <a href="{{ route('bookmakers.index') }}" @click="mobileMenuOpen = false" class="scout-mobile-item {{ request()->routeIs('bookmakers.*') ? 'active' : '' }}">Bookmakers</a>
                 <a href="{{ route('page.virtual-games') }}" @click="mobileMenuOpen = false" class="scout-mobile-item {{ request()->routeIs('page.virtual-games') ? 'active' : '' }}">Casino</a>
             </nav>

             <div class="scout-mobile-divider"></div>

             <!-- Auth / Guest State -->
             @auth
                 <div class="scout-mobile-user">Logged in as {{ Auth::user()->name }}</div>
                 <a href="{{ route('dashboard') }}" @click="mobileMenuOpen = false" class="scout-mobile-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                 @if(Auth::user()->hasRole('admin'))
                     <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="scout-mobile-item scout-dropdown-link-accent">⚙ Admin Panel</a>
                 @endif
                 @if(Auth::user()->hasRole('tipster') || Auth::user()->hasRole('admin'))
                     <a href="{{ route('tipster.dashboard') }}" @click="mobileMenuOpen = false" class="scout-mobile-item scout-dropdown-link-accent2">🎯 Tipster Dashboard</a>
                     <a href="{{ route('tipster.tips.create') }}" @click="mobileMenuOpen = false" class="scout-mobile-item">✏️ Submit a Tip</a>
                     <a href="{{ route('tipster.tips.index') }}" @click="mobileMenuOpen = false" class="scout-mobile-item">📋 My Tips</a>
                 @endif
                 <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false" class="scout-mobile-item">Profile</a>

                 <form method="POST" action="{{ route('logout') }}" class="scout-dropdown-form">
                     @csrf
                     <button type="submit" class="scout-mobile-item" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</button>
                 </form>
             @endauth

             @guest
                 @if(Route::has('login'))
                     <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="scout-mobile-item">Log in</a>
                 @endif
                 @if(Route::has('register'))
                     <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="scout-mobile-item">Register</a>
                 @endif
             @endguest
        </div>

        <div x-data="{ loading: false }" x-show="loading"
            class="fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center"
            x-cloak>
            <div class="scout-loading-spinner"></div>
        </div>

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
        <div x-data="{ showConsent: !localStorage.getItem('cookieConsent') }"
            x-show="showConsent"
            x-cloak
            class="fixed bottom-20 left-0 right-0 bg-card border-t border-border p-4 z-50">
            <div class="max-w-1280 mx-auto flex flex-col sm:flex-row gap-3 items-center justify-between">
                <p class="text-sm text-muted">We use cookies to improve your experience.</p>
                <div class="flex gap-3">
                    <button @click="localStorage.setItem('cookieConsent', 'true'); showConsent = false"
                            class="scout-btn scout-btn-primary text-sm">
                        Accept
                    </button>
                </div>
            </div>
        </div>
    </body>
</html>

