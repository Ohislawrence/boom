<x-admin-layout title="Dashboard">

    {{-- Stats grid --}}
    <div class="admin-stat-grid">
        <div class="admin-stat">
            <div class="admin-stat-value" style="color:var(--accent)">{{ $stats['tips_today'] }}</div>
            <div class="admin-stat-label">Tips Today</div>
        </div>
        <div class="admin-stat">
            <div class="admin-stat-value">{{ $stats['tips_total'] }}</div>
            <div class="admin-stat-label">Total Tips</div>
        </div>
        <div class="admin-stat" style="border-color:{{ $stats['tips_pending'] > 0 ? 'var(--accent2)' : 'var(--border)' }}">
            <div class="admin-stat-value" style="{{ $stats['tips_pending'] > 0 ? 'color:var(--accent2)' : '' }}">{{ $stats['tips_pending'] }}</div>
            <div class="admin-stat-label">Pending Review</div>
        </div>
        <div class="admin-stat">
            <div class="admin-stat-value">{{ $stats['fixtures_today'] }}</div>
            <div class="admin-stat-label">Fixtures Today</div>
        </div>
        <div class="admin-stat">
            <div class="admin-stat-value">{{ $stats['leagues_active'] }}</div>
            <div class="admin-stat-label">Active Leagues</div>
        </div>
        <div class="admin-stat">
            <div class="admin-stat-value">{{ $stats['bookmakers'] }}</div>
            <div class="admin-stat-label">Live Bookmakers</div>
        </div>
    </div>

    <div class="admin-two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">

        {{-- Pending tips --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
            <div style="padding:.65rem 1rem;background:var(--card2,#0f172a);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">Pending Tips</span>
                <a href="{{ route('admin.tips.index', ['status'=>'pending']) }}" style="font-size:.72rem;color:var(--accent);text-decoration:none">View all →</a>
            </div>
            @forelse($pendingTips as $tip)
            <div style="padding:.6rem 1rem;border-bottom:1px solid var(--border);display:grid;grid-template-columns:1fr auto auto;gap:.75rem;align-items:center">
                <div>
                    <div style="font-size:.82rem;color:var(--text)">
                        {{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}
                    </div>
                    <div style="font-size:.68rem;color:var(--muted)">
                        {{ $tip->market }} · {{ $tip->fixture->league?->name ?? 'Unknown league' }}
                    </div>
                </div>
                <span style="font-family:var(--fm);font-size:.8rem;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--muted)' }}">{{ $tip->confidence }}%</span>
                <div style="display:flex;gap:.3rem">
                    <form method="POST" action="{{ route('admin.tips.publish', $tip) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="badge badge-green btn-sm" style="cursor:pointer;border:none;font-size:.67rem;padding:.2rem .4rem">✓</button>
                    </form>
                    <form method="POST" action="{{ route('admin.tips.reject', $tip) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="badge badge-red btn-sm" style="cursor:pointer;border:none;font-size:.67rem;padding:.2rem .4rem">✗</button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:1.5rem;text-align:center;font-size:.82rem;color:var(--muted)">No pending tips</div>
            @endforelse
        </div>

        {{-- Recent scheduler runs --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
            <div style="padding:.65rem 1rem;background:var(--card2,#0f172a);border-bottom:1px solid var(--border)">
                <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">Scheduler Runs</span>
            </div>
            @forelse($recentLogs as $log)
            <div style="padding:.6rem 1rem;border-bottom:1px solid var(--border);display:grid;grid-template-columns:auto 1fr auto auto;gap:.75rem;align-items:center">
                <span class="badge {{ $log->status === 'completed' ? 'badge-green' : ($log->status === 'running' ? 'badge-yellow' : 'badge-red') }}">
                    {{ $log->status }}
                </span>
                <div style="font-family:var(--fm);font-size:.8rem;color:var(--text)">{{ $log->run_date }}</div>
                <div style="font-size:.72rem;color:var(--muted)">{{ $log->fixtures_fetched }} fix</div>
                <div style="font-size:.72rem;color:var(--accent)">{{ $log->tips_generated }} tips</div>
            </div>
            @empty
            <div style="padding:1.5rem;text-align:center;font-size:.82rem;color:var(--muted)">No runs yet</div>
            @endforelse
        </div>

    </div>

    <x-slot name="actions">
        <a href="{{ route('admin.bookmakers.create') }}" class="btn-primary btn-sm">+ Add Bookmaker</a>
    </x-slot>

</x-admin-layout>
