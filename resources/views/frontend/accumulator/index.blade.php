<x-app-layout>
<x-slot name="title">Accumulator Builder — Pick Your Acca</x-slot>
<x-slot name="description">Build your accumulator bet. Filter tips by date, league, country and market, add selections to your slip and see combined odds instantly.</x-slot>

<style>
/* ── Layout ── */
.acca-wrap{display:grid;grid-template-columns:1fr 300px;gap:1.25rem;align-items:start}
@media(max-width:860px){.acca-wrap{grid-template-columns:1fr}}

/* ── Filter bar ── */
.acca-filters{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1rem 1.1rem;display:flex;flex-wrap:wrap;gap:.65rem;align-items:flex-end}
.acca-filter-group{display:flex;flex-direction:column;gap:.2rem;min-width:130px;flex:1}
.acca-filter-group label{font-size:.62rem;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)}
.acca-filter-group select,.acca-filter-group input[type=date]{background:var(--surface);border:1px solid var(--border);border-radius:5px;color:var(--text);font-size:.82rem;padding:.38rem .55rem;width:100%;outline:none}
.acca-filter-group select:focus,.acca-filter-group input[type=date]:focus{border-color:var(--accent)}
.acca-filter-apply{background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.8rem;letter-spacing:.06em;padding:.42rem 1.1rem;border:none;border-radius:5px;cursor:pointer;white-space:nowrap;align-self:flex-end}
.acca-filter-reset{background:transparent;color:var(--muted);font-size:.78rem;padding:.42rem .7rem;border:1px solid var(--border);border-radius:5px;cursor:pointer;white-space:nowrap;align-self:flex-end;text-decoration:none;display:inline-flex;align-items:center}

/* ── Tip card ── */
.acca-tip-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1rem 1.1rem;display:grid;grid-template-columns:1fr auto;gap:.75rem;align-items:center;transition:border-color .15s}
.acca-tip-card:hover{border-color:var(--dim)}
.acca-tip-card.in-slip{border-color:var(--accent)!important}
.conf-bar-wrap{background:var(--surface);border-radius:3px;height:4px;width:100%;overflow:hidden;margin-top:.3rem}
.conf-bar{height:4px;border-radius:3px;background:var(--accent)}

/* ── Slip ── */
.acca-slip{background:var(--card);border:1px solid var(--accent);border-radius:8px;padding:1rem;position:sticky;top:70px}
.slip-empty{text-align:center;padding:1.5rem 0;font-size:.82rem;color:var(--muted)}
.slip-item{background:var(--surface);border:1px solid var(--border);border-radius:5px;padding:.55rem .7rem;margin-bottom:.45rem;position:relative}
.slip-remove{position:absolute;top:.35rem;right:.5rem;background:none;border:none;color:var(--muted);cursor:pointer;font-size:.9rem;line-height:1}
.slip-odds-total{background:rgba(0,229,160,.08);border:1px solid rgba(0,229,160,.25);border-radius:6px;padding:.65rem .8rem;margin:.75rem 0;display:flex;align-items:center;justify-content:space-between}
.acca-add-btn{background:var(--surface);border:1px solid var(--border);color:var(--muted);border-radius:5px;padding:.4rem .7rem;font-size:.75rem;cursor:pointer;transition:all .15s;white-space:nowrap}
.acca-add-btn.added{background:var(--accent);border-color:var(--accent);color:#07090e;font-weight:700}

/* ── Mobile slip drawer ── */
.acca-slip-mobile-bar{display:none;position:fixed;bottom:56px;left:0;right:0;z-index:100;background:var(--card);border-top:1px solid var(--accent);padding:.6rem 1rem;align-items:center;justify-content:space-between;gap:.75rem}
@media(max-width:860px){.acca-slip{display:none}.acca-slip-mobile-bar{display:flex}}
.slip-toggle-btn{background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.8rem;letter-spacing:.06em;padding:.4rem .9rem;border:none;border-radius:5px;cursor:pointer}

/* ── Mobile slip full overlay ── */
.acca-slip-overlay{display:none;position:fixed;inset:0;z-index:200;background:rgba(7,9,14,.85);align-items:flex-end}
.acca-slip-overlay.open{display:flex}
.acca-slip-sheet{background:var(--card);border-radius:12px 12px 0 0;padding:1.25rem;width:100%;max-height:80vh;overflow-y:auto}
.slip-close-btn{background:none;border:none;color:var(--muted);font-size:1.2rem;cursor:pointer;float:right;line-height:1}
</style>

<div class="scout-page-wrap" style="max-width:1280px;margin:0 auto;padding:1.5rem 2rem">

    {{-- Page header --}}
    <div style="margin-bottom:1.25rem">
        <div style="font-size:.72rem;color:var(--muted);margin-bottom:.4rem">
            <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
            <span style="margin:0 .35rem">›</span>
            <span style="color:var(--text)">Accumulator Builder</span>
        </div>
        <h1 style="font-family:var(--fh);font-size:1.9rem;letter-spacing:.08em;color:var(--text);margin-bottom:.2rem">Accumulator Builder</h1>
        <p style="font-size:.82rem;color:var(--muted)">Filter tips by date, league, country or market — then add selections to your acca slip to see combined odds.</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('accumulator.index') }}" id="acca-filter-form">
    <div class="acca-filters" style="margin-bottom:1.25rem">

        <div class="acca-filter-group" style="max-width:150px">
            <label>Date</label>
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}">
        </div>

        <div class="acca-filter-group">
            <label>Country</label>
            <select name="country" id="filter-country">
                <option value="">All Countries</option>
                @foreach($countries as $c)
                <option value="{{ $c }}" {{ request('country') == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>

        <div class="acca-filter-group">
            <label>League</label>
            <select name="league" id="filter-league">
                <option value="">All Leagues</option>
                @foreach($availableLeagues as $league)
                <option value="{{ $league->id }}"
                    data-country="{{ $league->country }}"
                    {{ request('league') == $league->id ? 'selected' : '' }}>
                    {{ $league->name }}
                    @if($league->country) ({{ $league->country }})@endif
                </option>
                @endforeach
            </select>
        </div>

        <div class="acca-filter-group">
            <label>Market</label>
            <select name="market">
                <option value="">All Markets</option>
                @foreach($markets as $market)
                <option value="{{ $market->id }}" {{ request('market') == $market->id ? 'selected' : '' }}>
                    {{ $market->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="acca-filter-group" style="max-width:120px">
            <label>Min Confidence</label>
            <select name="min_confidence">
                <option value="">Any</option>
                @foreach([50,60,70,75,80,85,90] as $c)
                <option value="{{ $c }}" {{ request('min_confidence') == $c ? 'selected' : '' }}>{{ $c }}%+</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="acca-filter-apply">Apply</button>
        <a href="{{ route('accumulator.index') }}" class="acca-filter-reset">Reset</a>
    </div>
    </form>

    {{-- Results summary --}}
    <div style="font-size:.78rem;color:var(--muted);margin-bottom:.9rem">
        Showing <strong style="color:var(--text)">{{ $tips->count() }}</strong>
        tip{{ $tips->count() === 1 ? '' : 's' }}
        for <strong style="color:var(--text)">{{ $date->format('D, d M Y') }}</strong>
        @if(request('country')) · {{ request('country') }} @endif
    </div>

    {{-- Main layout --}}
    <div class="acca-wrap">

        {{-- Tips list --}}
        <div>
            @forelse($tips as $tip)
            @php
                $fixture = $tip->fixture;
                $league  = $fixture->league ?? null;
                $conf    = $tip->confidence ?? 0;
                $confColor = $conf >= 80 ? 'var(--accent)' : ($conf >= 65 ? 'var(--accent2)' : 'var(--muted)');
            @endphp

            <div class="acca-tip-card" id="card-{{ $tip->id }}" style="margin-bottom:.75rem">
                {{-- Left: fixture info --}}
                <div>
                    {{-- League / date --}}
                    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
                        @if($league && $league->logo_url)
                        <img src="{{ $league->logo_url }}" style="width:16px;height:16px;object-fit:contain;opacity:.8" alt="">
                        @endif
                        <span style="font-size:.67rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">
                            {{ $league ? $league->name : 'Unknown League' }}
                            @if($league && $league->country) · {{ $league->country }} @endif
                        </span>
                        <span style="font-size:.65rem;color:var(--muted);margin-left:auto">
                            {{ $fixture->match_date->format('H:i') }}
                        </span>
                    </div>

                    {{-- Teams --}}
                    <div style="display:flex;align-items:center;gap:.65rem;margin-bottom:.55rem">
                        @if($fixture->home_logo)
                        <img src="{{ $fixture->home_logo }}" style="width:24px;height:24px;object-fit:contain" alt="">
                        @endif
                        <span style="font-size:.92rem;font-weight:600;color:var(--text)">{{ $fixture->home_team }}</span>
                        <span style="font-size:.72rem;color:var(--dim);flex-shrink:0">vs</span>
                        <span style="font-size:.92rem;font-weight:600;color:var(--text)">{{ $fixture->away_team }}</span>
                        @if($fixture->away_logo)
                        <img src="{{ $fixture->away_logo }}" style="width:24px;height:24px;object-fit:contain" alt="">
                        @endif
                    </div>

                    {{-- Market + selection + odds --}}
                    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                        <span style="font-size:.68rem;background:var(--surface);border:1px solid var(--border);color:var(--muted);padding:.12rem .38rem;border-radius:3px">
                            {{ $tip->betMarket ? $tip->betMarket->name : $tip->market }}
                        </span>
                        <span style="font-size:.88rem;font-weight:700;color:var(--text)">{{ $tip->selection }}</span>
                        @if($tip->odds)
                        <span style="font-size:.82rem;color:var(--accent2);font-family:var(--fm)">@ {{ number_format($tip->odds, 2) }}</span>
                        @endif
                        @if($tip->is_value_bet)
                        <span style="font-size:.6rem;background:rgba(245,197,24,.12);color:var(--accent2);border:1px solid rgba(245,197,24,.25);padding:.1rem .32rem;border-radius:3px;letter-spacing:.04em">VALUE</span>
                        @endif
                        @if($tip->is_ai_generated)
                        <span style="font-size:.6rem;background:rgba(0,229,160,.08);color:var(--accent);border:1px solid rgba(0,229,160,.2);padding:.1rem .32rem;border-radius:3px;letter-spacing:.04em">AI</span>
                        @endif
                    </div>

                    {{-- Confidence bar --}}
                    <div style="margin-top:.55rem;display:flex;align-items:center;gap:.5rem">
                        <div class="conf-bar-wrap" style="flex:1">
                            <div class="conf-bar" style="width:{{ $conf }}%;background:{{ $confColor }}"></div>
                        </div>
                        <span style="font-size:.68rem;color:{{ $confColor }};font-family:var(--fm);flex-shrink:0">{{ $conf }}%</span>
                    </div>
                </div>

                {{-- Right: Add to slip button --}}
                <div style="display:flex;flex-direction:column;align-items:center;gap:.4rem;flex-shrink:0">
                    <button class="acca-add-btn"
                        id="btn-{{ $tip->id }}"
                        onclick="toggleSlip({{ $tip->id }}, '{{ addslashes($fixture->home_team) }} vs {{ addslashes($fixture->away_team) }}', '{{ addslashes($tip->selection) }}', {{ $tip->odds ?? 0 }}, '{{ addslashes($tip->betMarket ? $tip->betMarket->name : $tip->market) }}')"
                        title="Add to acca slip">
                        + Add
                    </button>
                    <a href="{{ route('fixture.betting-tips', $fixture) }}"
                       style="font-size:.65rem;color:var(--muted);text-decoration:none"
                       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
                       Analysis →
                    </a>
                </div>
            </div>
            @empty
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:3rem;text-align:center">
                <div style="font-size:2rem;margin-bottom:.75rem">🔍</div>
                <div style="font-size:.9rem;color:var(--text);margin-bottom:.3rem">No tips found</div>
                <div style="font-size:.78rem;color:var(--muted)">Try a different date, market or relax some filters.</div>
            </div>
            @endforelse
        </div>

        {{-- Desktop acca slip --}}
        <div class="acca-slip" id="desktop-slip">
            <div style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--text);margin-bottom:.75rem;display:flex;align-items:center;justify-content:space-between">
                <span>Acca Slip <span id="slip-count-d" style="font-size:.75rem;color:var(--accent);font-family:var(--fm)"></span></span>
                <button onclick="clearSlip()" style="background:none;border:none;font-size:.7rem;color:var(--muted);cursor:pointer">Clear all</button>
            </div>
            <div id="slip-items-d">
                <div class="slip-empty" id="slip-empty-d">No selections yet.<br>Click <strong>+ Add</strong> on any tip.</div>
            </div>
            <div id="slip-odds-d" style="display:none">
                <div class="slip-odds-total">
                    <span style="font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">Combined Odds</span>
                    <span id="combined-odds-d" style="font-family:var(--fm);font-size:1.1rem;color:var(--accent);font-weight:700"></span>
                </div>
                <button onclick="copySlip()" style="width:100%;background:var(--surface);border:1px solid var(--border);color:var(--text);font-size:.8rem;padding:.5rem;border-radius:5px;cursor:pointer;margin-bottom:.4rem">📋 Copy Acca to Clipboard</button>
            </div>
            <p style="font-size:.65rem;color:var(--muted);line-height:1.6;margin-top:.5rem">18+ · Gamble responsibly · Combined odds are indicative only.</p>
        </div>

    </div>

</div>

{{-- Mobile slip bar --}}
<div class="acca-slip-mobile-bar" id="mobile-slip-bar">
    <div style="font-size:.82rem;color:var(--muted)">
        Slip: <strong style="color:var(--text)" id="slip-count-m">0</strong> selections
        <span style="margin:0 .4rem">·</span>
        Odds: <strong style="color:var(--accent);font-family:var(--fm)" id="combined-odds-m">—</strong>
    </div>
    <button class="slip-toggle-btn" onclick="document.getElementById('acca-overlay').classList.add('open')">View Slip</button>
</div>

{{-- Mobile slip overlay --}}
<div class="acca-slip-overlay" id="acca-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="acca-slip-sheet">
        <button class="slip-close-btn" onclick="document.getElementById('acca-overlay').classList.remove('open')">✕</button>
        <div style="font-family:var(--fh);font-size:1.2rem;letter-spacing:.06em;color:var(--text);margin-bottom:1rem">Acca Slip</div>
        <div id="slip-items-m">
            <div class="slip-empty">No selections yet.</div>
        </div>
        <div id="slip-odds-m" style="display:none">
            <div class="slip-odds-total">
                <span style="font-size:.72rem;color:var(--muted)">Combined Odds</span>
                <span id="combined-odds-m-detail" style="font-family:var(--fm);font-size:1.1rem;color:var(--accent);font-weight:700"></span>
            </div>
            <button onclick="copySlip()" style="width:100%;background:var(--surface);border:1px solid var(--border);color:var(--text);font-size:.8rem;padding:.5rem;border-radius:5px;cursor:pointer;margin-bottom:.4rem">📋 Copy Acca</button>
        </div>
        <button onclick="clearSlip();document.getElementById('acca-overlay').classList.remove('open')" style="width:100%;background:none;border:1px solid var(--border);color:var(--muted);font-size:.78rem;padding:.45rem;border-radius:5px;cursor:pointer;margin-top:.4rem">Clear All</button>
        <p style="font-size:.63rem;color:var(--muted);margin-top:.75rem;line-height:1.6">18+ · Gamble responsibly · Combined odds indicative only.</p>
    </div>
</div>

<script>
// ── Slip state ────────────────────────────────────────────────────────────
let slip = {};

function toggleSlip(id, fixture, selection, odds, market) {
    if (slip[id]) {
        removeFromSlip(id);
    } else {
        addToSlip(id, fixture, selection, odds, market);
    }
}

function addToSlip(id, fixture, selection, odds, market) {
    slip[id] = { id, fixture, selection, odds, market };
    renderSlip();
    document.getElementById('card-' + id)?.classList.add('in-slip');
    const btn = document.getElementById('btn-' + id);
    if (btn) { btn.classList.add('added'); btn.textContent = '✓ Added'; }
}

function removeFromSlip(id) {
    delete slip[id];
    renderSlip();
    document.getElementById('card-' + id)?.classList.remove('in-slip');
    const btn = document.getElementById('btn-' + id);
    if (btn) { btn.classList.remove('added'); btn.textContent = '+ Add'; }
}

function clearSlip() {
    Object.keys(slip).forEach(id => {
        document.getElementById('card-' + id)?.classList.remove('in-slip');
        const btn = document.getElementById('btn-' + id);
        if (btn) { btn.classList.remove('added'); btn.textContent = '+ Add'; }
    });
    slip = {};
    renderSlip();
}

function renderSlip() {
    const items = Object.values(slip);
    const count = items.length;

    // Combined odds
    const combined = items.reduce((acc, t) => t.odds > 0 ? acc * t.odds : acc, 1);
    const oddsStr = count > 0 ? combined.toFixed(2) : '—';

    // Count badges
    ['slip-count-d', 'slip-count-m'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = count > 0 ? '(' + count + ')' : '';
    });
    document.getElementById('combined-odds-m').textContent = count > 0 ? oddsStr : '—';
    document.getElementById('combined-odds-d').textContent = oddsStr;
    document.getElementById('combined-odds-m-detail').textContent = oddsStr;

    // Toggle odds block
    ['slip-odds-d', 'slip-odds-m'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = count > 0 ? 'block' : 'none';
    });

    // Render item lists
    renderSlipList('slip-items-d', 'slip-empty-d');
    renderSlipList('slip-items-m', null);
}

function renderSlipList(containerId, emptyId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const items = Object.values(slip);

    if (items.length === 0) {
        container.innerHTML = emptyId
            ? '<div class="slip-empty" id="' + emptyId + '">No selections yet.<br>Click <strong>+ Add</strong> on any tip.</div>'
            : '<div class="slip-empty">No selections yet.</div>';
        return;
    }

    container.innerHTML = items.map(t => `
        <div class="slip-item">
            <button class="slip-remove" onclick="removeFromSlip(${t.id})">✕</button>
            <div style="font-size:.72rem;color:var(--muted);margin-bottom:.15rem">${escHtml(t.market)}</div>
            <div style="font-size:.78rem;color:var(--text);font-weight:600;margin-bottom:.1rem;padding-right:1rem">${escHtml(t.fixture)}</div>
            <div style="display:flex;align-items:center;gap:.5rem">
                <span style="font-size:.82rem;color:var(--accent)">${escHtml(t.selection)}</span>
                ${t.odds > 0 ? `<span style="font-size:.75rem;color:var(--accent2);font-family:var(--fm)">@ ${Number(t.odds).toFixed(2)}</span>` : ''}
            </div>
        </div>
    `).join('');
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function copySlip() {
    const items = Object.values(slip);
    if (!items.length) return;
    const combined = items.reduce((acc, t) => t.odds > 0 ? acc * t.odds : acc, 1);
    const text = items.map((t,i) => `${i+1}. ${t.fixture} — ${t.market}: ${t.selection}${t.odds > 0 ? ' @ ' + Number(t.odds).toFixed(2) : ''}`).join('\n')
        + `\n\nCombined Odds: ${combined.toFixed(2)}\nBuilt with {{ config('app.name') }} accumulator builder`;
    navigator.clipboard?.writeText(text).then(() => alert('Acca copied to clipboard!')).catch(() => prompt('Copy this:', text));
}

// ── Country → league filter ───────────────────────────────────────────────
document.getElementById('filter-country')?.addEventListener('change', function () {
    const country = this.value;
    const leagueSelect = document.getElementById('filter-league');
    Array.from(leagueSelect.options).forEach(opt => {
        if (!opt.value) { opt.hidden = false; return; }
        opt.hidden = country ? opt.dataset.country !== country : false;
    });
    // Reset league if now hidden
    const selected = leagueSelect.options[leagueSelect.selectedIndex];
    if (selected?.hidden) leagueSelect.value = '';
});
</script>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
