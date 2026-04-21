<x-tipster-layout title="Dashboard">
    <x-slot name="actions">
        <a href="{{ route('tipster.tips.create') }}" class="btn-submit">+ Submit a Tip</a>
    </x-slot>

    {{-- Stat grid --}}
    <div class="ts-stat-grid">
        <div class="ts-stat">
            <div class="ts-stat-value">{{ $totalTips }}</div>
            <div class="ts-stat-label">Total Tips</div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-value accent">{{ $publishedTips }}</div>
            <div class="ts-stat-label">Published</div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-value accent2">{{ $pendingTips }}</div>
            <div class="ts-stat-label">Pending Review</div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-value red">{{ $rejectedTips }}</div>
            <div class="ts-stat-label">Rejected</div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-value {{ $winRate !== null ? ($winRate >= 55 ? 'accent' : 'accent2') : '' }}">
                {{ $winRate !== null ? $winRate.'%' : '—' }}
            </div>
            <div class="ts-stat-label">Win Rate</div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-value {{ $roi !== null ? ($roi >= 0 ? 'accent' : 'red') : '' }}">
                {{ $roi !== null ? ($roi >= 0 ? '+'.$roi : $roi).'%' : '—' }}
            </div>
            <div class="ts-stat-label">ROI ({{ $settledCount }} settled)</div>
        </div>
    </div>

    {{-- Recent tips --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="padding:.75rem 1rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
            <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.07em;color:var(--text)">Recent Tips</span>
            <a href="{{ route('tipster.tips.index') }}" style="font-size:.75rem;color:var(--accent2);text-decoration:none">View all →</a>
        </div>

        @if($recentTips->isEmpty())
        <div style="padding:2.5rem;text-align:center;color:var(--muted)">
            <div style="font-size:2rem;margin-bottom:.75rem">⚡</div>
            <div style="font-size:.9rem">No tips yet.</div>
            <a href="{{ route('tipster.tips.create') }}" style="display:inline-block;margin-top:.75rem;font-size:.82rem;color:var(--accent2)">Submit your first tip →</a>
        </div>
        @else
        <table class="ts-table">
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Market / Selection</th>
                    <th>Odds</th>
                    <th>Conf.</th>
                    <th>Status</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTips as $tip)
                <tr>
                    <td>
                        <a href="{{ route('tipster.tips.show', $tip) }}" style="text-decoration:none;color:var(--text)">
                            <div style="font-size:.84rem;font-weight:600">{{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}</div>
                            <div style="font-size:.68rem;color:var(--muted)">{{ $tip->fixture->match_date->format('d M H:i') }}</div>
                        </a>
                    </td>
                    <td>
                        <div style="font-size:.75rem;color:var(--muted)">{{ $tip->market }}</div>
                        <div style="font-weight:600;font-size:.88rem">{{ $tip->selection }}</div>
                    </td>
                    <td style="font-family:var(--fm);color:var(--accent2)">{{ $tip->odds ? number_format($tip->odds, 2) : '—' }}</td>
                    <td>
                        <div style="font-family:var(--fm);font-size:.88rem;font-weight:700;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--accent2)' }}">{{ $tip->confidence }}%</div>
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
                        <span style="font-size:.75rem;color:var(--muted)">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</x-tipster-layout>
