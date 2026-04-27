<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin – {{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .admin-shell { display:flex; min-height:100vh; background:var(--bg); }

        /* ── Sidebar ── */
        .admin-sidebar {
            width: 230px;
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
            overflow-y: auto;
        }
        .admin-sidebar-logo {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .admin-sidebar-section {
            padding: .5rem 0;
            border-bottom: 1px solid var(--border);
        }
        .admin-sidebar-label {
            font-size: .6rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            padding: .55rem 1.25rem .25rem;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .5rem 1.25rem;
            font-size: .83rem;
            color: var(--muted);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .admin-nav-link:hover {
            color: var(--text);
            background: var(--card);
        }
        .admin-nav-link.active {
            color: var(--accent);
            border-left-color: var(--accent);
            background: rgba(0,229,160,.06);
        }
        .admin-nav-link .icon { font-size: 1rem; width: 1.1rem; text-align: center; }

        /* ── Main ── */
        .admin-main {
            margin-left: 230px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .admin-topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-content {
            padding: 1.5rem;
            flex: 1;
        }

        /* ── Cards / Stat boxes ── */
        .admin-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .admin-stat {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem 1.1rem;
        }
        .admin-stat-value {
            font-family: var(--fm);
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }
        .admin-stat-label {
            font-size: .72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: .3rem;
        }

        /* ── Table ── */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th {
            text-align: left;
            font-size: .68rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 500;
            padding: .5rem .85rem;
            border-bottom: 1px solid var(--border);
            background: var(--card2, #0f172a);
        }
        .admin-table td {
            padding: .65rem .85rem;
            font-size: .85rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        .admin-table tr:hover td { background: var(--card2, #0f172a); }

        /* ── Badges ── */
        .badge { display:inline-block; font-size:.67rem; padding:.15rem .45rem; border-radius:4px; font-weight:600; letter-spacing:.04em; }
        .badge-green  { background:rgba(0,229,160,.12); color:var(--accent);  border:1px solid rgba(0,229,160,.25); }
        .badge-yellow { background:rgba(245,197,24,.12); color:var(--accent2); border:1px solid rgba(245,197,24,.25); }
        .badge-red    { background:rgba(239,68,68,.12);  color:#ef4444;        border:1px solid rgba(239,68,68,.25); }
        .badge-gray   { background:rgba(100,116,139,.12);color:var(--muted);   border:1px solid var(--border); }

        /* ── Form inputs ── */
        .admin-input, .admin-select, .admin-textarea {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            padding: .5rem .75rem;
            border-radius: 6px;
            font-size: .88rem;
            font-family: var(--fp);
            transition: border-color .15s;
        }
        .admin-input:focus, .admin-select:focus, .admin-textarea:focus {
            outline: none;
            border-color: var(--accent);
        }
        .admin-textarea { resize: vertical; min-height: 100px; }
        .admin-label {
            display: block;
            font-size: .75rem;
            color: var(--muted);
            margin-bottom: .35rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .admin-form-group { margin-bottom: 1.1rem; }

        /* ── Buttons ── */
        .btn-primary { background:var(--accent); color:#07090e; font-family:var(--fh); font-size:.85rem; letter-spacing:.06em; padding:.5rem 1.1rem; border-radius:5px; border:none; cursor:pointer; text-decoration:none; display:inline-block; }
        .btn-primary:hover { opacity:.85; }
        .btn-secondary { background:transparent; color:var(--text); border:1px solid var(--border); font-size:.85rem; padding:.5rem 1rem; border-radius:5px; cursor:pointer; text-decoration:none; display:inline-block; }
        .btn-secondary:hover { border-color:var(--dim); }
        .btn-danger { background:rgba(239,68,68,.1); color:#ef4444; border:1px solid rgba(239,68,68,.3); font-size:.82rem; padding:.35rem .7rem; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-block; }
        .btn-danger:hover { background:rgba(239,68,68,.2); }
        .btn-sm { padding:.3rem .65rem; font-size:.78rem; }

        /* ── Sidebar overlay (mobile) ── */
        .admin-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 190;
            backdrop-filter: blur(2px);
        }
        .admin-overlay.open { display: block; }

        /* ── Hamburger (hidden on desktop) ── */
        .admin-hamburger {
            display: none;
            background: none;
            border: 1px solid var(--border);
            color: var(--muted);
            padding: .35rem .5rem;
            border-radius: 5px;
            cursor: pointer;
            margin-right: .6rem;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
        }

        /* ── Mobile breakpoint ── */
        @media (max-width: 767px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform .25s ease;
            }
            .admin-sidebar.open {
                transform: translateX(0);
                box-shadow: 4px 0 30px rgba(0,0,0,.5);
            }
            .admin-main  { margin-left: 0; }
            .admin-topbar { padding: .6rem 1rem; }
            .admin-content { padding: 1rem .85rem; }
            .admin-hamburger { display: flex; }
            /* Scrollable tables */
            .admin-table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
            }
            /* Stat grid — already auto-fit, just shrink min */
            .admin-stat-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
            /* Two-col dashboard grid */
            .admin-two-col { grid-template-columns: 1fr !important; }
            /* Action buttons wrap */
            .admin-topbar > div:last-child { flex-wrap: wrap; }
        }
    </style>
</head>
<body style="background:var(--bg)">
<div class="admin-shell" x-data="{ sidebarOpen: false }">

    {{-- Overlay (closes sidebar on tap outside) --}}
    <div class="admin-overlay" :class="{ 'open': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- ═══════════════ SIDEBAR ═══════════════ --}}
    <aside class="admin-sidebar" :class="{ 'open': sidebarOpen }">
        <div class="admin-sidebar-logo">
            <a href="{{ route('home') }}" style="text-decoration:none;display:flex;align-items:center;gap:.4rem">
                <span style="font-family:var(--fh);font-size:1.3rem;letter-spacing:.1em;color:var(--accent)">SCOUT</span>
                <span style="font-size:.65rem;background:var(--accent);color:#07090e;padding:.1rem .3rem;border-radius:3px;font-weight:700">ADMIN</span>
            </a>
        </div>

        <nav style="flex:1">
            <div class="admin-sidebar-section">
                <div class="admin-sidebar-label">Overview</div>
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">📊</span> Dashboard
                </a>
            </div>

            <div class="admin-sidebar-section">
                <div class="admin-sidebar-label">AI Pipeline</div>
                <a href="{{ route('admin.run-control.index') }}" class="admin-nav-link {{ request()->routeIs('admin.run-control*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🤖</span> Run Control
                    @php
                        $pendingTips = \App\Models\Tip::where('status','published')->where('result','pending')
                            ->whereHas('fixture', fn($q)=>$q->where('match_date','<=',now()))->count();
                    @endphp
                    @if($pendingTips > 0)
                    <span style="margin-left:auto;background:rgba(245,197,24,.2);color:var(--accent2);font-size:.62rem;padding:.1rem .4rem;border-radius:10px;font-family:var(--fm)">{{ $pendingTips }}</span>
                    @endif
                </a>
            </div>

            <div class="admin-sidebar-section">
                <div class="admin-sidebar-label">Users</div>
                <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">👥</span> All Users
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'tipster']) }}" class="admin-nav-link {{ request()->routeIs('admin.users*') && request('role') === 'tipster' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🎯</span> Tipsters
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'bettor']) }}" class="admin-nav-link {{ request()->routeIs('admin.users*') && request('role') === 'bettor' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🏅</span> Bettors
                </a>
            </div>

            <div class="admin-sidebar-section">
                <div class="admin-sidebar-label">Content</div>
                <a href="{{ route('admin.tips.index') }}" class="admin-nav-link {{ request()->routeIs('admin.tips*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">⚡</span> Tips
                </a>
                <a href="{{ route('admin.virtual-games.index') }}" class="admin-nav-link {{ request()->routeIs('admin.virtual-games*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🎮</span> Virtual Games
                </a>
                <a href="{{ route('admin.fixtures.index') }}" class="admin-nav-link {{ request()->routeIs('admin.fixtures*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">⚽</span> Fixtures
                </a>
                <a href="{{ route('admin.leagues.index') }}" class="admin-nav-link {{ request()->routeIs('admin.leagues*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🏆</span> Leagues
                </a>
                <a href="{{ route('admin.countries.index') }}" class="admin-nav-link {{ request()->routeIs('admin.countries*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🌍</span> Countries
                </a>
                <a href="{{ route('admin.bookmakers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.bookmakers*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🏦</span> Bookmakers
                </a>
                <a href="{{ route('admin.bet-markets.index') }}" class="admin-nav-link {{ request()->routeIs('admin.bet-markets*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">📋</span> Bet Markets
                </a>
            </div>

            <div class="admin-sidebar-section">
                <div class="admin-sidebar-label">System</div>
                <a href="{{ route('admin.click-analytics.index') }}" class="admin-nav-link {{ request()->routeIs('admin.click-analytics*') ? 'active' : '' }}" @click="sidebarOpen = false">
                    <span class="icon">🖱️</span> Click Analytics
                </a>
                <a href="{{ route('home') }}" class="admin-nav-link" target="_blank">
                    <span class="icon">🌐</span> View Site
                </a>
            </div>
        </nav>

        <div style="padding:1rem 1.25rem;border-top:1px solid var(--border)">
            <div style="font-size:.78rem;color:var(--text);font-weight:600;margin-bottom:.15rem">{{ auth()->user()->name }}</div>
            <div style="font-size:.68rem;color:var(--muted);margin-bottom:.6rem">Administrator</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;font-size:.75rem;color:var(--muted);padding:0;text-align:left" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
                    Sign out →
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════ MAIN ═══════════════ --}}
    <div class="admin-main">

        {{-- Top bar --}}
        <div class="admin-topbar">
            <div style="display:flex;align-items:center">
                <button class="admin-hamburger" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div>
                <h1 style="font-family:var(--fh);font-size:1.15rem;letter-spacing:.07em;color:var(--text)">{{ $title ?? 'Admin' }}</h1>
                @isset($breadcrumb)
                <div style="font-size:.72rem;color:var(--muted);margin-top:.1rem">{{ $breadcrumb }}</div>
                @endisset
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:.75rem">
                {{ $actions ?? '' }}
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
        <div style="background:rgba(0,229,160,.08);border-bottom:1px solid rgba(0,229,160,.2);padding:.65rem 1.5rem;font-size:.83rem;color:var(--accent)">
            ✓ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="background:rgba(239,68,68,.08);border-bottom:1px solid rgba(239,68,68,.2);padding:.65rem 1.5rem;font-size:.83rem;color:#ef4444">
            ✗ {{ session('error') }}
        </div>
        @endif

        <div class="admin-content">
            {{ $slot }}
        </div>
    </div>

</div>{{-- end admin-shell --}}

<!-- ══ MOBILE BOTTOM NAV (admin) ══ -->
<nav class="scout-bottom-nav" aria-label="Admin mobile navigation">

    <a href="{{ route('admin.dashboard') }}" class="scout-bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
    </a>

    <a href="{{ route('admin.users.index') }}" class="scout-bottom-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Users
    </a>

    <a href="{{ route('admin.tips.index') }}" class="scout-bottom-nav-item {{ request()->routeIs('admin.tips*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Tips
    </a>

    <a href="{{ route('admin.fixtures.index') }}" class="scout-bottom-nav-item {{ request()->routeIs('admin.fixtures*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
        Fixtures
    </a>

    <a href="{{ route('admin.leagues.index') }}" class="scout-bottom-nav-item {{ request()->routeIs('admin.leagues*') || request()->routeIs('admin.bookmakers*') || request()->routeIs('admin.countries*') || request()->routeIs('admin.bet-markets*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
        Content
    </a>

</nav>

</body>
</html>
