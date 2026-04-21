<nav x-data="{ open: false }" class="scout-header">
  <div class="scout-header-inner" style="width:100%">

    <!-- Logo — always links to homepage -->
    <div style="flex-shrink:0">
        <a href="{{ url('/') }}" class="scout-auth-logo" style="font-size:1.8rem;display:block;line-height:1">{{ config('app.name', 'SCOUT') }}</a>
        <div class="scout-auth-logo-sub" style="text-align:left">AI Betting Analyzer</div>
    </div>

    <!-- Desktop Nav Links — between logo and user menu -->
    <div class="scout-nav-links">
        <x-nav-link :href="url('/')" :active="request()->is('/') && !request()->routeIs('dashboard')">
            {{ __('Home') }}
        </x-nav-link>
        <x-nav-link :href="route('fixture.betting-tips.index')" :active="request()->routeIs('fixture.betting-tips*')">
            {{ __('Betting Tips') }}
        </x-nav-link>
        <x-nav-link :href="route('accumulator.index')" :active="request()->routeIs('accumulator.*')">
            {{ __('Accumulator') }}
        </x-nav-link>
        <x-nav-link :href="route('bookmakers.index')" :active="request()->routeIs('bookmakers.*')">
            {{ __('Bookmakers') }}
        </x-nav-link>
    </div>

    @auth
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>

        <!-- User Dropdown — pushed to the far right -->
        <div style="margin-left:auto;display:flex;align-items:center;gap:.75rem">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="scout-dropdown-trigger">
                        {{ Auth::user()->name }}
                        <svg style="width:12px;height:12px;fill:currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    @if(Auth::user()->hasRole('admin'))
                    <x-dropdown-link :href="route('admin.dashboard')" style="color:var(--accent)">
                        ⚙ Admin Panel
                    </x-dropdown-link>
                    @endif
                    @if(Auth::user()->hasRole('tipster') || Auth::user()->hasRole('admin'))
                    <div style="height:1px;background:var(--border);margin:.25rem 0"></div>
                    <div style="font-size:.62rem;color:var(--muted);padding:.35rem 1rem .1rem;text-transform:uppercase;letter-spacing:.08em">Tipster Portal</div>
                    <x-dropdown-link :href="route('tipster.dashboard')" style="color:var(--accent2)">
                        🎯 Dashboard
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('tipster.tips.create')">
                        ✏️ Submit a Tip
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('tipster.tips.index')">
                        📋 My Tips
                    </x-dropdown-link>
                    <div style="height:1px;background:var(--border);margin:.25rem 0"></div>
                    @endif
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    @else

        <!-- Guest nav — pushed to the far right -->
        <div class="scout-guest-nav" style="margin-left:auto;display:flex;align-items:center;gap:.75rem">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="scout-nav-link">{{ __('Log in') }}</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="scout-btn scout-btn-primary" style="font-size:.8rem;padding:.45rem 1rem">{{ __('Register') }}</a>
            @endif
        </div>
    @endauth

  </div>
</nav>

