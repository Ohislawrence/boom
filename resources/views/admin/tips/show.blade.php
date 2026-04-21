<x-admin-layout title="Tip Detail">
    <x-slot name="breadcrumb">Tips / {{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.tips.index') }}" class="btn-secondary">← Back</a>
        <a href="{{ route('admin.tips.edit', $tip) }}" class="btn-primary btn-sm">✏ Edit Prediction</a>
        @if($tip->status !== 'published')
        <form method="POST" action="{{ route('admin.tips.publish', $tip) }}" style="display:inline">
            @csrf @method('PATCH')
            <button type="submit" class="btn-primary">✓ Publish</button>
        </form>
        @endif
        @if($tip->status !== 'rejected')
        <form method="POST" action="{{ route('admin.tips.reject', $tip) }}" style="display:inline">
            @csrf @method('PATCH')
            <button type="submit" class="btn-danger">✗ Reject</button>
        </form>
        @endif
    </x-slot>

    <div style="max-width:700px">

        {{-- Summary card --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
            <div style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:start">
                <div>
                    <div style="font-family:var(--fh);font-size:1.4rem;letter-spacing:.06em;color:var(--text);margin-bottom:.25rem">
                        {{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}
                    </div>
                    <div style="font-size:.78rem;color:var(--muted);margin-bottom:.75rem">
                        {{ $tip->fixture->match_date->format('D d M Y, H:i') }}
                        @if($tip->fixture->league)
                        · {{ $tip->fixture->league->name }} ({{ $tip->fixture->league->country }})
                        @endif
                    </div>

                    <div style="display:flex;gap:.6rem;flex-wrap:wrap;align-items:center">
                        <span style="font-size:.75rem;color:var(--muted)">{{ $tip->market }}</span>
                        <span style="font-family:var(--fm);font-size:1.1rem;font-weight:700;color:var(--accent)">{{ $tip->selection }}</span>
                        @if($tip->odds)
                        <span style="font-family:var(--fm);font-size:1rem;color:var(--accent2)">@ {{ number_format($tip->odds,2) }}</span>
                        @endif
                    </div>
                </div>

                <div style="text-align:center">
                    <div style="font-family:var(--fm);font-size:2rem;font-weight:700;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--accent2)' }}">{{ $tip->confidence }}%</div>
                    <div style="font-size:.62rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Confidence</div>
                    @if($tip->is_ai_generated)<div><span class="badge badge-green" style="margin-top:.35rem">AI</span></div>@endif
                    @if($tip->is_value_bet)<div><span class="badge badge-yellow" style="margin-top:.25rem">VALUE</span></div>@endif
                </div>
            </div>

            <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--border);display:flex;gap:.75rem;align-items:center">
                <span style="font-size:.75rem;color:var(--muted)">Status:</span>
                @if($tip->status === 'published')
                <span class="badge badge-green">Published</span>
                @elseif($tip->status === 'pending')
                <span class="badge badge-yellow">Pending</span>
                @else
                <span class="badge badge-red">Rejected</span>
                @endif
                @if($tip->submittedBy)
                <span style="font-size:.75rem;color:var(--muted)">· Submitted by {{ $tip->submittedBy->name }}</span>
                @endif
            </div>
        </div>

        {{-- AI Reasoning --}}
        @if($tip->reasoning)
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1rem">
            <div style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text);margin-bottom:.7rem">AI Reasoning</div>
            <p style="font-size:.85rem;color:var(--muted);line-height:1.9">{{ $tip->reasoning }}</p>
        </div>
        @endif

        {{-- Result --}}
        @if($tip->tipResult)
        @php $res = $tip->tipResult; @endphp
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.1rem;display:flex;align-items:center;gap:1rem">
            <div style="font-size:1.5rem">{{ $res->result === 'win' ? '✅' : ($res->result === 'loss' ? '❌' : '↩️') }}</div>
            <div>
                <div style="font-family:var(--fh);font-size:1rem;color:var(--text)">{{ strtoupper($res->result) }}</div>
                @if($res->profit_loss !== null)
                <div style="font-size:.8rem;color:var(--muted)">P&L: <span style="color:{{ $res->profit_loss >= 0 ? 'var(--accent)' : '#ef4444' }};font-family:var(--fm)">{{ $res->profit_loss >= 0 ? '+' : '' }}{{ number_format($res->profit_loss, 2) }} units</span></div>
                @endif
                @if($res->closing_odds)
                <div style="font-size:.78rem;color:var(--muted)">Closing odds: <span style="font-family:var(--fm)">{{ number_format($res->closing_odds,2) }}</span></div>
                @endif
            </div>
        </div>
        @endif

        <div style="margin-top:1rem">
            <form method="POST" action="{{ route('admin.tips.destroy', $tip) }}" onsubmit="return confirm('Delete this tip permanently?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">Delete tip permanently</button>
            </form>
        </div>

    </div>

</x-admin-layout>
