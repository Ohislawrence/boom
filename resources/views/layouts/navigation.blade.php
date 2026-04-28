<nav class="scout-header">
  <div class="scout-header-inner">

    <!-- Logo — always links to homepage -->
    <div class="scout-logo">
        <a href="{{ url('/') }}" class="scout-auth-logo scout-header-logo">{{ config('app.name', 'SCOUT') }}</a>
        <div class="scout-auth-logo-sub scout-header-logo-sub">AI Betting Analyzer</div>
    </div>

    <form action="{{ route('search') }}" method="GET" class="scout-search-form" style="max-width:540px">
        <input name="q" type="search" value="{{ request('q') }}" placeholder="Search teams, tips, leagues or bookmakers" class="scout-search-input">
        <button type="submit" class="scout-search-btn">Search</button>
    </form>

    <!-- Desktop Nav Links — between logo and user menu -->
    <div class="scout-nav-links">
        <x-nav-link :href="url('/')" :active="request()->is('/') && !request()->routeIs('dashboard')">
            {{ __('Home') }}
        </x-nav-link>
        <x-nav-link :href="route('fixture.betting-tips.index')" :active="request()->routeIs('fixture.betting-tips*')">
            {{ __('Ai Tips') }}
        </x-nav-link>
        <x-nav-link :href="route('accumulator.index')" :active="request()->routeIs('accumulator.*')">
            {{ __('Accumulator') }}
        </x-nav-link>
        <x-nav-link :href="route('bookmakers.index')" :active="request()->routeIs('bookmakers.*')">
            {{ __('Bookmakers') }}
        </x-nav-link>
        <x-nav-link :href="route('page.virtual-games')" :active="request()->routeIs('page.virtual-games')">
            {{ __('Casino') }}
        </x-nav-link>
        @auth
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>

        <!-- User Dropdown — pushed to the far right -->
        <div class="scout-user-dropdown">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="scout-dropdown-trigger">
                        {{ Auth::user()->name }}
                        <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    @if(Auth::user()->hasRole('admin'))
                    <x-dropdown-link :href="route('admin.dashboard')" class="scout-dropdown-link-accent">
                        ⚙ Admin Panel
                    </x-dropdown-link>
                    @endif
                    @if(Auth::user()->hasRole('tipster') || Auth::user()->hasRole('admin'))
                    <div class="scout-dropdown-divider"></div>
                    <div class="scout-dropdown-label">Tipster Portal</div>
                    <x-dropdown-link :href="route('tipster.dashboard')" class="scout-dropdown-link-accent2">
                        🎯 Dashboard
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('tipster.tips.create')">
                        ✏️ Submit a Tip
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('tipster.tips.index')">
                        📋 My Tips
                    </x-dropdown-link>
                    <div class="scout-dropdown-divider"></div>
                    @endif
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}" class="scout-dropdown-form">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
        @endauth
    </div>

    <div class="scout-hamburger" @click.prevent="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle mobile menu">
        <span>☰</span>
    </div>

    @guest
        <div class="scout-guest-nav">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="scout-nav-link">{{ __('Log in') }}</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="scout-btn scout-btn-primary scout-guest-register">{{ __('Register') }}</a>
            @endif
        </div>
    @endguest

  </div>
</nav>

