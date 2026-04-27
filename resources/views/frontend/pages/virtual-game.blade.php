<x-app-layout>
<x-slot name="title">{{ $game['name'] }} — Virtual Games — {{ config('app.name') }}</x-slot>
<x-slot name="description">{{ $game['description'] }}</x-slot>

<div style="max-width:1240px;margin:0 auto;padding:2rem 2rem">
    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:flex-start;margin-bottom:2rem">
        <div style="flex:1;min-width:320px;max-width:720px">
            <div style="display:inline-flex;padding:.45rem .9rem;border-radius:999px;background:rgba(99,102,241,.1);color:var(--accent);font-size:.78rem;font-weight:700;letter-spacing:.06em;margin-bottom:.9rem">Virtual Game</div>
            <h1 style="font-family:var(--fh);font-size:2.8rem;letter-spacing:.04em;color:var(--text);margin-bottom:.8rem;line-height:1.05">{{ $game['name'] }}</h1>
            <p style="font-size:1rem;color:var(--muted);line-height:1.8;margin-bottom:1.25rem">{{ $game['tagline'] }}</p>
            <div style="display:flex;flex-wrap:wrap;gap:.85rem">
                <a href="#play" style="background:var(--accent);color:#07090e;text-decoration:none;padding:.95rem 1.25rem;border-radius:999px;font-size:.88rem;font-weight:700;">Play Now</a>
                <a href="#features" style="background:var(--surface);color:var(--text);text-decoration:none;padding:.95rem 1.25rem;border-radius:999px;font-size:.88rem;border:1px solid var(--border);">View Features</a>
            </div>
        </div>
    </div>

    <div id="play" style="display:grid;grid-template-columns:1fr;gap:1.5rem;align-items:start;margin-bottom:2rem">
        <div style="background:var(--card);border:1px solid var(--border);border-radius:24px;padding:1.5rem;">
            <h2 style="font-family:var(--fh);font-size:1.5rem;color:var(--text);margin-bottom:.85rem">Play area</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;margin-bottom:1rem;">
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1rem;font-size:.88rem;color:var(--muted);">
                    <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">Game type</div>
                    <div>{{ $game['name'] }}</div>
                </div>
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1rem;font-size:.88rem;color:var(--muted);">
                    <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">Expected RTP</div>
                    <div>{{ $game['rtp'] }}</div>
                </div>
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1rem;font-size:.88rem;color:var(--muted);">
                    <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">Volatility</div>
                    <div>{{ $game['volatility'] }}</div>
                </div>
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1rem;font-size:.88rem;color:var(--muted);">
                    <div style="font-weight:700;color:var(--text);margin-bottom:.35rem">Status</div>
                    <div>{{ $game['script_path'] ?? null ? 'Ready' : 'Script missing' }}</div>
                </div>
            </div>
            <div style="position:relative;border-radius:24px;overflow:hidden;border:1px solid var(--border);margin-bottom:1rem;background:#07090e;">
                <canvas id="virtual-game-canvas" width="1600" height="860" style="width:100%;height:620px;display:block;background:#020617;"></canvas>
                <div id="virtual-game-status" style="position:absolute;left:1rem;bottom:1rem;padding:.55rem 1rem;border-radius:999px;background:rgba(15,23,42,.82);color:#fff;font-size:.85rem;">Loading game...</div>
            </div>
            <p style="font-size:.93rem;color:var(--muted);line-height:1.8;margin-bottom:1rem">{{ $game['description'] }}</p>
            <ol style="padding-left:1.2rem;color:var(--muted);font-size:.92rem;line-height:1.85;">
                <li>Use the canvas above once the game script loads.</li>
                <li>Game scripts are uploaded by admin and will initialize automatically.</li>
                <li>Developers should expose <code>window.initVirtualGame(canvasId)</code> inside the JS file.</li>
                <li>If the game fails to load, ask admin to upload a valid JS file.</li>
            </ol>
            <div style="margin-top:1.25rem;display:flex;gap:.9rem;flex-wrap:wrap">
                <a href="#virtual-game-canvas" style="background:var(--accent);color:#07090e;text-decoration:none;padding:.95rem 1.15rem;border-radius:999px;font-size:.88rem;font-weight:700;">Focus Canvas</a>
                <a href="{{ route('page.virtual-games') }}" style="background:var(--surface);color:var(--text);text-decoration:none;padding:.95rem 1.15rem;border-radius:999px;font-size:.88rem;border:1px solid var(--border);">Back to lobby</a>
            </div>
        </div>
    </div>
    <script>
        (function() {
            const statusEl = document.getElementById('virtual-game-status');
            const scriptUrl = @json($game['script_url'] ?? null);
            const canvasId = 'virtual-game-canvas';

            if (!scriptUrl) {
                statusEl.textContent = 'No game script uploaded yet. Please ask admin to upload a JS file.';
                return;
            }

            const script = document.createElement('script');
            script.src = scriptUrl;
            script.async = true;
            script.onload = function() {
                if (typeof window.initVirtualGame === 'function') {
                    statusEl.textContent = 'Initializing game…';
                    try {
                        window.initVirtualGame(canvasId);
                        statusEl.textContent = 'Game loaded. Use the canvas to play.';
                    } catch (error) {
                        statusEl.textContent = 'Game script loaded, but initialization failed.';
                        console.error('Virtual game init error:', error);
                    }
                } else {
                    statusEl.textContent = 'Game script loaded, but initVirtualGame() was not found.';
                }
            };
            script.onerror = function() {
                statusEl.textContent = 'Failed to load game script. Please contact admin.';
            };
            document.body.appendChild(script);
        })();
    </script>

    <div id="features" style="margin-bottom:2rem;padding:1.5rem;background:var(--surface);border:1px solid var(--border);border-radius:24px;">
        <h2 style="font-family:var(--fh);font-size:1.6rem;color:var(--text);margin-bottom:1rem">Game features</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
            @foreach($game['features'] as $feature)
            <div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:1rem;min-height:120px;">
                <div style="font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:.55rem">{{ $feature }}</div>
                <div style="font-size:.82rem;color:var(--muted);line-height:1.75">A slick experience designed to mirror the same lobby behavior as modern casino platforms.</div>
            </div>
            @endforeach
        </div>
    </div>

    <div style="margin-bottom:2rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.8rem;margin-bottom:1rem">
            <div>
                <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.4rem">Related games</div>
                <h2 style="font-family:var(--fh);font-size:1.8rem;color:var(--text);margin:0">More games to explore</h2>
            </div>
            <a href="{{ route('page.virtual-games') }}" style="font-size:.82rem;color:var(--accent);text-decoration:none;font-weight:700">Back to lobby →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;">
            @foreach($related as $item)
            <a href="{{ route('page.virtual-game', ['slug' => $item['slug']]) }}" style="text-decoration:none;color:inherit;">
                <div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:1.2rem;transition:transform .15s ease,box-shadow .15s ease;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.8rem">
                        <div style="font-size:1.05rem;font-weight:700;color:var(--text);">{{ $item['name'] }}</div>
                        <div style="font-size:1.6rem;">{{ $item['icon'] }}</div>
                    </div>
                    <p style="font-size:.82rem;color:var(--muted);line-height:1.75;margin-bottom:1rem;">{{ $item['tagline'] }}</p>
                    <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                        <span style="font-size:.75rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.45rem .75rem">{{ $item['volatility'] }}</span>
                        <span style="font-size:.75rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.45rem .75rem">{{ $item['rtp'] }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
