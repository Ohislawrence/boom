<x-app-layout>

<x-slot name="title">Search results for "{{ $query ?: 'Search' }}"</x-slot>
<x-slot name="description">Search football tips, fixtures, leagues and bookmakers across boomodd.</x-slot>

<div class="scout-page-wrap" style="max-width:1280px;margin:0 auto;padding:1.5rem 2rem">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem">
        <div style="min-width:230px">
            <div style="font-size:.85rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.45rem">Search</div>
            <h1 style="font-family:var(--fh);font-size:2rem;color:var(--text);margin:0">Find tips, fixtures, leagues and bookmakers</h1>
        </div>

        <form action="{{ route('search') }}" method="GET" style="display:flex;gap:.5rem;width:100%;max-width:540px">
            <input name="q" type="search" value="{{ $query }}" placeholder="Search teams, tips, leagues or bookmakers"
                style="flex:1;min-width:0;background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.75rem 1rem;color:var(--text);outline:none">
            <button type="submit" style="background:var(--accent);color:#07090e;border:none;border-radius:999px;padding:.75rem 1rem;font-weight:700;cursor:pointer">Search</button>
        </form>
    </div>

    @if(!$query)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:2rem;text-align:center">
            <div style="font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:.5rem">Start your search</div>
            <p style="color:var(--muted);max-width:680px;margin:0 auto">Enter a team name, league, bookmaker or tip keyword and press Search to find matching fixtures, published tips, leagues and bookmakers.</p>
        </div>
    @else
        @php
            $resultCount = $fixtures->count() + $tips->count() + $bookmakers->count() + $leagues->count();
        @endphp

        <div style="margin-bottom:1.5rem;color:var(--muted);font-size:.95rem">
            {{ $resultCount }} {{ $resultCount === 1 ? 'result' : 'results' }} found for "{{ $query }}"
        </div>

        @if($resultCount === 0)
            <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:2rem;text-align:center">
                <div style="font-size:1.2rem;font-weight:700;color:var(--text);margin-bottom:.5rem">No results found</div>
                <p style="color:var(--muted);max-width:680px;margin:0 auto">Try a different keyword like a team name, league or bookmaker.</p>
            </div>
        @endif

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem">
            @if($fixtures->isNotEmpty())
                <section style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem">
                    <h2 style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:.85rem">Fixtures</h2>
                    <div style="display:flex;flex-direction:column;gap:.75rem">
                        @foreach($fixtures as $fixture)
                            <a href="{{ route('fixture.betting-tips', $fixture) }}" style="display:block;padding:.85rem 1rem;border:1px solid var(--border);border-radius:10px;text-decoration:none;color:inherit;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                                <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">{{ $fixture->home_team }} vs {{ $fixture->away_team }}</div>
                                <div style="font-size:.85rem;color:var(--muted)">{{ $fixture->local_match_date->format('d M Y H:i') }} · {{ $fixture->league?->name ?? 'League' }}</div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($tips->isNotEmpty())
                <section style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem">
                    <h2 style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:.85rem">Tips</h2>
                    <div style="display:flex;flex-direction:column;gap:.75rem">
                        @foreach($tips as $tip)
                            <a href="{{ route('tips.show', $tip) }}" style="display:block;padding:.85rem 1rem;border:1px solid var(--border);border-radius:10px;text-decoration:none;color:inherit;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                                <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">{{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</div>
                                <div style="font-size:.85rem;color:var(--muted)">{{ $tip->market }} · {{ $tip->selection }} · {{ $tip->confidence }}% confidence</div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($leagues->isNotEmpty())
                <section style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem">
                    <h2 style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:.85rem">Leagues</h2>
                    <div style="display:flex;flex-direction:column;gap:.75rem">
                        @foreach($leagues as $league)
                            <a href="{{ route('league.show', $league) }}" style="display:block;padding:.85rem 1rem;border:1px solid var(--border);border-radius:10px;text-decoration:none;color:inherit;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                                <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">{{ $league->name }}</div>
                                <div style="font-size:.85rem;color:var(--muted)">League page</div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($bookmakers->isNotEmpty())
                <section style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem">
                    <h2 style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:.85rem">Bookmakers</h2>
                    <div style="display:flex;flex-direction:column;gap:.75rem">
                        @foreach($bookmakers as $bookmaker)
                            <a href="{{ route('bookmakers.show', $bookmaker) }}" style="display:block;padding:.85rem 1rem;border:1px solid var(--border);border-radius:10px;text-decoration:none;color:inherit;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                                <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">{{ $bookmaker->name }}</div>
                                <div style="font-size:.85rem;color:var(--muted)">Bookmaker profile</div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    @endif
</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
