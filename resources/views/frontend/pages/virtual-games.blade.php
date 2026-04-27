<x-app-layout>
<x-slot name="title">Virtual Games — {{ config('app.name') }}</x-slot>
<x-slot name="description">Explore virtual casino-style games with a clean arcade-style selection.</x-slot>

<div style="max-width:1160px;margin:0 auto;padding:2rem 2rem">
    <div style="display:flex;flex-wrap:wrap;gap:1.25rem;align-items:flex-start;margin-bottom:1.5rem">
        <div style="flex:1;min-width:300px;max-width:720px">
            <div style="display:inline-flex;padding:.45rem .9rem;border-radius:999px;background:rgba(99,102,241,.1);color:var(--accent);font-size:.78rem;font-weight:700;letter-spacing:.06em;margin-bottom:.9rem">Virtual Games</div>
            <h1 style="font-family:var(--fh);font-size:2.6rem;letter-spacing:.05em;color:var(--text);margin-bottom:.75rem;line-height:1.05">Casino-style games, styled like a modern virtual games lobby.</h1>
            <p style="font-size:.96rem;color:var(--muted);line-height:1.8;max-width:720px;margin-bottom:1.25rem">Browse featured virtual casino games, crash-style rounds, live-style tables, and slot experiences in a familiar arcade layout. This page is designed to feel like a slick casino lobby while staying consistent with the app's existing style.</p>
            <div style="display:flex;flex-wrap:wrap;gap:.75rem">
                <a href="#featured" style="background:var(--accent);color:#07090e;text-decoration:none;padding:.75rem 1.05rem;border-radius:999px;font-size:.82rem;font-weight:700;">Featured</a>
                <a href="#popular" style="background:var(--surface);color:var(--text);text-decoration:none;padding:.75rem 1.05rem;border-radius:999px;font-size:.82rem;border:1px solid var(--border);">Popular</a>
                <a href="#new" style="background:var(--surface);color:var(--text);text-decoration:none;padding:.75rem 1.05rem;border-radius:999px;font-size:.82rem;border:1px solid var(--border);">New</a>
            </div>
        </div>

        <div style="flex:0 0 360px;min-width:280px;background:linear-gradient(180deg,rgba(99,102,241,.12),rgba(15,23,42,.05));border:1px solid rgba(99,102,241,.12);border-radius:20px;padding:1.5rem;">
            <div style="font-size:.78rem;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.8rem">Game lobby</div>
            <div style="display:grid;gap:.9rem">
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1rem">
                    <div style="font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:.35rem">Live-style lobby</div>
                    <div style="font-size:.8rem;color:var(--muted);line-height:1.7">Filtered selection of virtual casino-style games with quick actions and game metrics.</div>
                </div>
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1rem">
                    <div style="font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:.35rem">Fast access</div>
                    <div style="font-size:.8rem;color:var(--muted);line-height:1.7">Tap any game to open its details page or play preview mode.</div>
                </div>
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1rem">
                    <div style="font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:.35rem">Styled for speed</div>
                    <div style="font-size:.8rem;color:var(--muted);line-height:1.7">A streamlined, responsive layout that works on desktop and mobile.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="featured" style="margin-bottom:2rem">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.8rem;margin-bottom:1rem">
            <div>
                <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.4rem">Featured games</div>
                <h2 style="font-family:var(--fh);font-size:1.8rem;color:var(--text);margin:0">Hot virtual titles</h2>
            </div>
            <a href="#popular" style="font-size:.82rem;color:var(--accent);text-decoration:none;font-weight:700">See popular games →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
            @foreach($games as $game)
            <div style="background:var(--card);border:1px solid var(--border);border-radius:22px;overflow:hidden;display:flex;flex-direction:column;">
                <div style="background:{{ $game['color'] }};padding:1.6rem 1.35rem;display:flex;align-items:flex-end;justify-content:space-between;min-height:160px;">
                    <div>
                        <div style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:.35rem">{{ $game['name'] }}</div>
                        <div style="font-size:.78rem;color:rgba(255,255,255,.82);line-height:1.6">{{ $game['tagline'] }}</div>
                    </div>
                    <div style="font-size:2.8rem;line-height:1;">{{ $game['icon'] }}</div>
                </div>
                <div style="padding:1.2rem 1.25rem 1.35rem;flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.85rem;margin-bottom:1.1rem">
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:.8rem;font-size:.78rem;color:var(--muted);">Provider<br><strong style="color:var(--text)">{{ $game['provider'] }}</strong></div>
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:.8rem;font-size:.78rem;color:var(--muted);">Volatility<br><strong style="color:var(--text)">{{ $game['volatility'] }}</strong></div>
                    </div>
                    <div style="display:flex;gap:.7rem;flex-wrap:wrap;margin-bottom:1rem">
                        <span style="font-size:.75rem;color:var(--muted);">RTP</span>
                        <strong style="font-size:.95rem;color:var(--text)">{{ $game['rtp'] }}</strong>
                    </div>
                    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                        <a href="{{ route('page.virtual-game', ['slug' => $game['slug']]) }}" style="flex:1;display:inline-flex;justify-content:center;padding:.9rem 1rem;border-radius:999px;background:var(--accent);color:#07090e;text-decoration:none;font-weight:700;font-size:.85rem">Play</a>
                        <a href="{{ route('page.virtual-game', ['slug' => $game['slug']]) }}" style="flex:1;display:inline-flex;justify-content:center;padding:.9rem 1rem;border-radius:999px;background:var(--surface);color:var(--text);text-decoration:none;font-weight:700;font-size:.85rem;border:1px solid var(--border)">Details</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div id="popular" style="margin-bottom:2rem">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.8rem;margin-bottom:1rem">
            <div>
                <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.4rem">Popular games</div>
                <h2 style="font-family:var(--fh);font-size:1.8rem;color:var(--text);margin:0">Fast favourites</h2>
            </div>
            <a href="#new" style="font-size:.82rem;color:var(--accent);text-decoration:none;font-weight:700">See new games →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;">
            @foreach($popularGames as $game)
            <a href="{{ route('page.virtual-game', ['slug' => $game['slug']]) }}" style="text-decoration:none;color:inherit;">
                <div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:1.15rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.9rem">
                        <div>
                            <div style="font-size:1.05rem;font-weight:700;color:var(--text)">{{ $game['name'] }}</div>
                            <div style="font-size:.78rem;color:var(--muted);margin-top:.25rem">{{ $game['provider'] }}</div>
                        </div>
                        <div style="font-size:1.8rem;">{{ $game['icon'] }}</div>
                    </div>
                    <p style="font-size:.79rem;color:var(--muted);line-height:1.7;margin-bottom:1rem">{{ $game['tagline'] }}</p>
                    <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                        <span style="font-size:.75rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.5rem .75rem">{{ $game['volatility'] }}</span>
                        <span style="font-size:.75rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.5rem .75rem">{{ $game['rtp'] }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <div id="new" style="margin-bottom:2rem">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.8rem;margin-bottom:1rem">
            <div>
                <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.4rem">New drops</div>
                <h2 style="font-family:var(--fh);font-size:1.8rem;color:var(--text);margin:0">Latest launches</h2>
            </div>
            <a href="#featured" style="font-size:.82rem;color:var(--accent);text-decoration:none;font-weight:700">Back to top →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
            @foreach($newGames as $game)
            <a href="{{ route('page.virtual-game', ['slug' => $game['slug']]) }}" style="text-decoration:none;color:inherit;">
                <div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:1.2rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
                        <div style="font-size:1rem;font-weight:700;color:var(--text)">{{ $game['name'] }}</div>
                        <div style="font-size:1.6rem;">{{ $game['icon'] }}</div>
                    </div>
                    <div style="font-size:.82rem;color:var(--muted);line-height:1.7;margin-bottom:1rem">{{ $game['tagline'] }}</div>
                    <div style="display:flex;gap:.6rem;flex-wrap:wrap">
                        <span style="font-size:.75rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.5rem .75rem">{{ $game['provider'] }}</span>
                        <span style="font-size:.75rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.5rem .75rem">{{ $game['rtp'] }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
