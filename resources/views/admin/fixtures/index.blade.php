<x-admin-layout title="Fixtures">
    <x-slot name="breadcrumb">Showing fixtures near today</x-slot>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.fixtures.index') }}" style="display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:1.25rem;align-items:flex-end">
        <div>
            <label class="admin-label" style="display:block;margin-bottom:.2rem">Date</label>
            <input type="date" name="date" class="admin-input" style="width:auto" value="{{ request('date') }}">
        </div>
        <div>
            <label class="admin-label" style="display:block;margin-bottom:.2rem">League</label>
            <select name="league" class="admin-select" style="width:auto">
                <option value="">All Leagues</option>
                @foreach($leagues as $league)
                <option value="{{ $league->id }}" {{ request('league') == $league->id ? 'selected' : '' }}>{{ $league->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label" style="display:block;margin-bottom:.2rem">Status</label>
            <select name="status" class="admin-select" style="width:auto">
                <option value="">All</option>
                @foreach($statuses as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary btn-sm">Filter</button>
        <a href="{{ route('admin.fixtures.index') }}" class="btn-secondary btn-sm">Reset</a>
    </form>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date / Time</th>
                    <th>Match</th>
                    <th>League</th>
                    <th>Status</th>
                    <th>Score</th>
                    <th>Tips</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fixtures as $fixture)
                <tr>
                    <td style="font-family:var(--fm);font-size:.78rem;color:var(--muted);white-space:nowrap">
                        {{ $fixture->match_date->format('d M') }}<br>
                        <span style="color:var(--text)">{{ $fixture->match_date->format('H:i') }}</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.4rem">
                            @if($fixture->home_logo)
                            <img src="{{ $fixture->home_logo }}" style="height:16px;width:16px;object-fit:contain">
                            @endif
                            <span style="font-size:.85rem;font-weight:600">{{ $fixture->home_team }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:.4rem;margin-top:.2rem">
                            @if($fixture->away_logo)
                            <img src="{{ $fixture->away_logo }}" style="height:16px;width:16px;object-fit:contain">
                            @endif
                            <span style="font-size:.85rem">{{ $fixture->away_team }}</span>
                        </div>
                    </td>
                    <td style="font-size:.78rem;color:var(--muted)">{{ $fixture->league?->name ?? '—' }}</td>
                    <td>
                        @php
                            $statusColors = ['FT'=>'badge-green','NS'=>'badge-gray','LIVE'=>'badge-yellow','PST'=>'badge-red','CANC'=>'badge-red'];
                            $cls = $statusColors[$fixture->status] ?? 'badge-gray';
                        @endphp
                        <span class="badge {{ $cls }}">{{ $fixture->status }}</span>
                    </td>
                    <td style="font-family:var(--fm);font-size:.9rem;color:var(--text);text-align:center">
                        @if(!is_null($fixture->score_home))
                        {{ $fixture->score_home }} – {{ $fixture->score_away }}
                        @else
                        <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        @if($fixture->tips_count > 0)
                        <a href="{{ route('admin.tips.index') }}" class="badge badge-green">{{ $fixture->tips_count }}</a>
                        @else
                        <span style="color:var(--muted);font-size:.82rem">0</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">No fixtures found for the selected filters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fixtures->hasPages())
    <div style="margin-top:1rem">{{ $fixtures->appends(request()->query())->links() }}</div>
    @endif

</x-admin-layout>
