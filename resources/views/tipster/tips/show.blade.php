<x-tipster-layout title="Tip Detail">
    <x-slot name="breadcrumb">My Tips / {{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('tipster.tips.index') }}" class="btn-secondary">← Back</a>
        @if($tip->status !== 'published')
        <form method="POST" action="{{ route('tipster.tips.destroy', $tip) }}"
              onsubmit="return confirm('Delete this tip?')" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">Delete</button>
        </form>
        @endif
    </x-slot>

    <div style="max-width:680px">

        {{-- Match + tip card --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
            <div style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:start">
                <div>
                    <div style="font-family:var(--fh);font-size:1.35rem;letter-spacing:.06em;color:var(--text);margin-bottom:.2rem">
                        {{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}
                    </div>
                    <div style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem">
                        {{ $tip->fixture->local_match_date->format('D d M Y, H:i') }}
                        @if($tip->fixture->league)
                        · {{ $tip->fixture->league->name }} ({{ $tip->fixture->league->country }})
                        @endif
                    </div>

                    <div style="display:flex;gap:.6rem;flex-wrap:wrap;align-items:center;margin-bottom:.75rem">
                        <span style="font-size:.75rem;color:var(--muted)">{{ $tip->market }}</span>
                        <span style="font-family:var(--fm);font-size:1.1rem;font-weight:700;color:var(--text)">{{ $tip->selection }}</span>
                        @if($tip->odds)
                        <span style="font-family:var(--fm);font-size:1rem;color:var(--accent2)">@ {{ number_format($tip->odds, 2) }}</span>
                        @endif
                        @if($tip->is_value_bet)
                        <span class="badge badge-yellow">VALUE</span>
                        @endif
                    </div>
                </div>

                <div style="text-align:center;min-width:70px">
                    <div style="font-family:var(--fm);font-size:2rem;font-weight:700;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--accent2)' }}">
                        {{ $tip->confidence }}%
                    </div>
                    <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Confidence</div>
                    <div class="conf-bar" style="margin-top:.4rem">
                        <div class="conf-fill" style="width:{{ $tip->confidence }}%;background:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--accent2)' }}"></div>
                    </div>
                </div>
            </div>

            <div style="padding-top:.75rem;border-top:1px solid var(--border);display:flex;gap:.75rem;align-items:center;flex-wrap:wrap">
                @if($tip->status === 'published')
                <span class="badge badge-green">✓ Published</span>
                <span style="font-size:.75rem;color:var(--muted)">Your tip is live on the site.</span>
                @elseif($tip->status === 'pending')
                <span class="badge badge-yellow">⏳ Pending Review</span>
                <span style="font-size:.75rem;color:var(--muted)">An admin will review it shortly.</span>
                @else
                <span class="badge badge-red">✗ Rejected</span>
                <span style="font-size:.75rem;color:var(--muted)">This tip was not approved.</span>
                @endif
            </div>
        </div>

        {{-- Reasoning --}}
        @if($tip->reasoning)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
            <div style="font-family:var(--fh);font-size:.9rem;letter-spacing:.07em;color:var(--text);margin-bottom:.65rem">Your Reasoning</div>
            <p style="font-size:.85rem;color:var(--muted);line-height:1.9;margin:0">{{ $tip->reasoning }}</p>
        </div>
        @else
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1rem;margin-bottom:1rem;font-size:.82rem;color:var(--muted)">
            No reasoning provided for this tip.
        </div>
        @endif

        {{-- Result --}}
        @if($tip->tipResult)
        @php $res = $tip->tipResult; @endphp
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.1rem;margin-bottom:1rem;display:flex;align-items:center;gap:1rem">
            <div style="font-size:1.5rem">{{ $res->result === 'win' ? '✅' : ($res->result === 'loss' ? '❌' : '↩️') }}</div>
            <div>
                <div style="font-family:var(--fh);font-size:1rem;color:var(--text)">{{ strtoupper($res->result) }}</div>
                @if($res->profit_loss !== null)
                <div style="font-size:.8rem;color:var(--muted)">
                    P&L: <span style="color:{{ $res->profit_loss >= 0 ? 'var(--accent)' : '#ef4444' }};font-family:var(--fm)">
                        {{ $res->profit_loss >= 0 ? '+' : '' }}{{ number_format($res->profit_loss, 2) }} units
                    </span>
                </div>
                @endif
                @if($res->closing_odds)
                <div style="font-size:.78rem;color:var(--muted)">
                    Closing odds: <span style="font-family:var(--fm)">{{ number_format($res->closing_odds, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        @else
        <div style="font-size:.78rem;color:var(--muted);padding:.5rem 0">Result not yet recorded.</div>
        @endif

        {{-- Published tip link --}}
        @if($tip->status === 'published')
        <div style="margin-top:.5rem">
            <a href="{{ route('tips.show', $tip) }}" class="btn-secondary" target="_blank">View public tip page →</a>
        </div>
        @endif

    </div>

</x-tipster-layout>
