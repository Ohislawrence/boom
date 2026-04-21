<x-tipster-layout title="My Tips">
    <x-slot name="actions">
        <div style="display:flex;gap:.4rem">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'published' => 'Published', 'rejected' => 'Rejected'] as $s => $label)
            <a href="{{ route('tipster.tips.index', ['status' => $s]) }}"
               class="{{ $status === $s ? 'btn-submit' : 'btn-secondary' }} btn-sm">
                {{ $label }}
            </a>
            @endforeach
        </div>
        <a href="{{ route('tipster.tips.create') }}" class="btn-submit">+ Submit a Tip</a>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="ts-table">
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Market / Selection</th>
                    <th>Odds</th>
                    <th>Conf.</th>
                    <th>Status</th>
                    <th>Result</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tips as $tip)
                <tr>
                    <td>
                        <a href="{{ route('tipster.tips.show', $tip) }}" style="text-decoration:none;color:var(--text)">
                            <div style="font-size:.85rem;font-weight:600">{{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</div>
                            <div style="font-size:.68rem;color:var(--muted)">
                                {{ $tip->fixture->match_date->format('d M H:i') }}
                                @if($tip->fixture->league)· {{ $tip->fixture->league->name }}@endif
                            </div>
                        </a>
                    </td>
                    <td>
                        <div style="font-size:.75rem;color:var(--muted)">{{ $tip->market }}</div>
                        <div style="font-weight:600;font-size:.88rem">{{ $tip->selection }}</div>
                    </td>
                    <td style="font-family:var(--fm);color:var(--accent2)">{{ $tip->odds ? number_format($tip->odds, 2) : '—' }}</td>
                    <td>
                        <div style="font-family:var(--fm);font-size:.88rem;font-weight:700;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--accent2)' }}">
                            {{ $tip->confidence }}%
                        </div>
                        <div class="conf-bar" style="width:50px">
                            <div class="conf-fill" style="width:{{ $tip->confidence }}%;background:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--accent2)' }}"></div>
                        </div>
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
                        @if($tip->tipResult)
                            @if($tip->tipResult->result === 'win')
                            <span class="badge badge-green">✓ Win</span>
                            @elseif($tip->tipResult->result === 'loss')
                            <span class="badge badge-red">✗ Loss</span>
                            @else
                            <span class="badge badge-gray">↩ Push</span>
                            @endif
                        @else
                        <span style="font-size:.72rem;color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.3rem">
                            <a href="{{ route('tipster.tips.show', $tip) }}" class="btn-secondary btn-sm">View</a>
                            @if($tip->status !== 'published')
                            <form method="POST" action="{{ route('tipster.tips.destroy', $tip) }}"
                                  onsubmit="return confirm('Delete this tip?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">×</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">
                        No {{ $status !== 'all' ? $status : '' }} tips yet.
                        <a href="{{ route('tipster.tips.create') }}" style="color:var(--accent2)">Submit one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tips->hasPages())
    <div style="margin-top:1rem">{{ $tips->withQueryString()->links() }}</div>
    @endif

</x-tipster-layout>
