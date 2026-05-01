<x-admin-layout title="Tips Moderation">
    <x-slot name="actions">
        <div style="display:flex;gap:.4rem">
            @foreach(['pending' => 'Pending', 'published' => 'Published', 'rejected' => 'Rejected', 'all' => 'All'] as $s => $label)
            <a href="{{ route('admin.tips.index', ['status' => $s]) }}"
               class="{{ $status === $s ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Market / Selection</th>
                    <th>Conf.</th>
                    <th>Odds</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tips as $tip)
                <tr>
                    <td>
                        <div style="font-size:.85rem;font-weight:600">{{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</div>
                        <div style="font-size:.68rem;color:var(--muted)">
                            {{ $tip->fixture->local_match_date->format('d M H:i') }}
                            @if($tip->fixture->league)
                            · {{ $tip->fixture->league->name }}
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.82rem;color:var(--muted)">{{ $tip->market }}</div>
                        <div style="font-family:var(--fm);font-size:.88rem;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--text)' }};font-weight:700">{{ $tip->selection }}</div>
                    </td>
                    <td>
                        <div style="font-family:var(--fm);font-size:.9rem;font-weight:700;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : ($tip->confidence >= 65 ? 'var(--accent2)' : 'var(--muted)') }}">
                            {{ $tip->confidence }}%
                        </div>
                    </td>
                    <td style="font-family:var(--fm);font-size:.88rem;color:var(--accent2)">{{ $tip->odds ? number_format($tip->odds,2) : '—' }}</td>
                    <td>
                        @if($tip->is_ai_generated)
                        <span class="badge badge-green">AI</span>
                        @elseif($tip->submittedBy)
                        <span class="badge badge-gray" style="font-size:.62rem">{{ $tip->submittedBy->name }}</span>
                        @endif
                        @if($tip->is_value_bet)
                        <span class="badge badge-yellow">VALUE</span>
                        @endif
                    </td>
                    <td>
                        @if($tip->status === 'published')
                        <span class="badge badge-green">Published</span>
                        @elseif($tip->status === 'pending')
                        <span class="badge badge-yellow">Pending</span>
                        @else
                        <span class="badge badge-red">Rejected</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.3rem;align-items:center">
                            <a href="{{ route('admin.tips.show', $tip) }}" class="btn-secondary btn-sm">View</a>
                            <a href="{{ route('admin.tips.edit', $tip) }}" class="btn-secondary btn-sm">✏</a>
                            @if($tip->status !== 'published')
                            <form method="POST" action="{{ route('admin.tips.publish', $tip) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge badge-green" style="cursor:pointer;border:none;padding:.2rem .5rem">✓ Publish</button>
                            </form>
                            @endif
                            @if($tip->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.tips.reject', $tip) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge badge-red" style="cursor:pointer;border:none;padding:.2rem .5rem">✗ Reject</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">No {{ $status }} tips found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tips->hasPages())
    <div style="margin-top:1rem">{{ $tips->withQueryString()->links() }}</div>
    @endif

</x-admin-layout>
