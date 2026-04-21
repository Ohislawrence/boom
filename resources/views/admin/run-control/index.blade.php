<x-admin-layout title="AI Run Control">
    <x-slot name="breadcrumb">Manage the daily analysis pipeline</x-slot>

    {{-- Artisan output flash --}}
    @if(session('artisan_output'))
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.85rem 1rem;margin-bottom:1.25rem;font-family:var(--fm);font-size:.75rem;color:var(--muted);white-space:pre-wrap;max-height:200px;overflow-y:auto">{{ session('artisan_output') }}</div>
    @endif

    {{-- ── Pipeline workflow — 3 steps ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.5rem" class="admin-three-col">

        {{-- Step 1: Fetch Fixtures --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.3rem;position:relative">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem">
                <span style="background:rgba(0,229,160,.15);color:var(--accent);font-family:var(--fm);font-size:.65rem;padding:.1rem .45rem;border-radius:10px;border:1px solid rgba(0,229,160,.25)">Step 1</span>
                <span style="font-family:var(--fh);font-size:1rem;letter-spacing:.07em;color:var(--accent)">Fetch Fixtures</span>
            </div>
            <p style="font-size:.75rem;color:var(--muted);margin-bottom:1rem;line-height:1.6">
                Pull scheduled fixtures from API-Football and store them in the database. No AI involved — fast and cheap.
            </p>
            <form method="POST" action="{{ route('admin.run-control.fetch') }}" x-data="{ mode: 'ahead' }">
                @csrf
                <div style="display:flex;gap:.4rem;margin-bottom:.75rem">
                    <button type="button" @click="mode='ahead'" :style="mode==='ahead'?'background:rgba(0,229,160,.15);color:var(--accent);border:1px solid rgba(0,229,160,.3)':'background:var(--surface);color:var(--muted);border:1px solid var(--border)'" style="font-size:.68rem;padding:.2rem .5rem;border-radius:4px;cursor:pointer">Days Ahead</button>
                    <button type="button" @click="mode='date'" :style="mode==='date'?'background:rgba(0,229,160,.15);color:var(--accent);border:1px solid rgba(0,229,160,.3)':'background:var(--surface);color:var(--muted);border:1px solid var(--border)'" style="font-size:.68rem;padding:.2rem .5rem;border-radius:4px;cursor:pointer">Specific Date</button>
                </div>
                <div style="display:flex;gap:.6rem;align-items:flex-end;flex-wrap:wrap">
                    <div x-show="mode==='ahead'">
                        <label class="admin-label">Target</label>
                        <select name="days_ahead" class="admin-select" style="width:auto">
                            <option value="1">Tomorrow (D+1)</option>
                            <option value="2">Day after (D+2)</option>
                            <option value="3">3 days (D+3)</option>
                        </select>
                    </div>
                    <div x-show="mode==='date'" style="flex:1;min-width:120px">
                        <label class="admin-label">Date</label>
                        <input type="date" name="date" class="admin-input" value="{{ now()->addDay()->toDateString() }}">
                    </div>
                    <button type="submit" class="btn-secondary btn-sm" style="border-color:var(--accent);color:var(--accent)" onclick="return confirm('Fetch fixtures from API-Football?')">
                        ↓ Fetch
                    </button>
                </div>
            </form>
            <div style="margin-top:.85rem;padding-top:.75rem;border-top:1px solid var(--border);font-size:.68rem;color:var(--muted)">
                API cost: <span style="color:var(--text)">1 call per active league</span>
            </div>
        </div>

        {{-- Step 2: Analyse Games --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.3rem">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem">
                <span style="background:rgba(99,102,241,.15);color:#818cf8;font-family:var(--fm);font-size:.65rem;padding:.1rem .45rem;border-radius:10px;border:1px solid rgba(99,102,241,.25)">Step 2</span>
                <span style="font-family:var(--fh);font-size:1rem;letter-spacing:.07em;color:#818cf8">Analyse Games</span>
            </div>
            <p style="font-size:.75rem;color:var(--muted);margin-bottom:1rem;line-height:1.6">
                Run DeepSeek AI on fixtures already stored in the database. No API-Football calls — uses cached data only.
            </p>
            <form method="POST" action="{{ route('admin.run-control.analyse-only') }}" x-data="{ mode: 'ahead' }">
                @csrf
                <div style="display:flex;gap:.4rem;margin-bottom:.75rem">
                    <button type="button" @click="mode='ahead'" :style="mode==='ahead'?'background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)':'background:var(--surface);color:var(--muted);border:1px solid var(--border)'" style="font-size:.68rem;padding:.2rem .5rem;border-radius:4px;cursor:pointer">Days Ahead</button>
                    <button type="button" @click="mode='date'" :style="mode==='date'?'background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)':'background:var(--surface);color:var(--muted);border:1px solid var(--border)'" style="font-size:.68rem;padding:.2rem .5rem;border-radius:4px;cursor:pointer">Specific Date</button>
                </div>
                <div style="display:flex;gap:.6rem;align-items:flex-end;flex-wrap:wrap">
                    <div x-show="mode==='ahead'">
                        <label class="admin-label">Target</label>
                        <select name="days_ahead" class="admin-select" style="width:auto">
                            <option value="1">Tomorrow (D+1)</option>
                            <option value="2">Day after (D+2)</option>
                            <option value="3">3 days (D+3)</option>
                        </select>
                    </div>
                    <div x-show="mode==='date'" style="flex:1;min-width:120px">
                        <label class="admin-label">Date</label>
                        <input type="date" name="date" class="admin-input" value="{{ now()->addDay()->toDateString() }}">
                    </div>
                    <div style="display:flex;align-items:center;gap:.4rem;padding-bottom:.05rem">
                        <input type="hidden" name="force" value="0">
                        <input type="checkbox" name="force" value="1" id="force-analyse" style="width:13px;height:13px;accent-color:#818cf8">
                        <label for="force-analyse" style="font-size:.68rem;color:var(--muted);cursor:pointer">Force</label>
                    </div>
                    <button type="submit" class="btn-secondary btn-sm" style="border-color:#818cf8;color:#818cf8" onclick="return confirm('Run AI analysis on stored fixtures? This uses DeepSeek credits.')">
                        🤖 Analyse
                    </button>
                </div>
            </form>
            <div style="margin-top:.85rem;padding-top:.75rem;border-top:1px solid var(--border);font-size:.68rem;color:var(--muted)">
                AI cost: <span style="color:var(--text)">1 DeepSeek call per fixture</span>
            </div>
        </div>

        {{-- Step 3: Full Run (fetch + analyse) --}}
        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.3rem">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem">
                <span style="background:rgba(245,197,24,.12);color:var(--accent2);font-family:var(--fm);font-size:.65rem;padding:.1rem .45rem;border-radius:10px;border:1px solid rgba(245,197,24,.25)">Full</span>
                <span style="font-family:var(--fh);font-size:1rem;letter-spacing:.07em;color:var(--accent2)">Full Run</span>
            </div>
            <p style="font-size:.75rem;color:var(--muted);margin-bottom:1rem;line-height:1.6">
                Fetch fixtures from API-Football <em>and</em> immediately run AI analysis. This is what the scheduler runs automatically.
            </p>
            <form method="POST" action="{{ route('admin.run-control.analysis') }}" x-data="{ mode: 'ahead' }">
                @csrf
                <div style="display:flex;gap:.4rem;margin-bottom:.75rem">
                    <button type="button" @click="mode='ahead'" :style="mode==='ahead'?'background:rgba(245,197,24,.12);color:var(--accent2);border:1px solid rgba(245,197,24,.3)':'background:var(--surface);color:var(--muted);border:1px solid var(--border)'" style="font-size:.68rem;padding:.2rem .5rem;border-radius:4px;cursor:pointer">Days Ahead</button>
                    <button type="button" @click="mode='date'" :style="mode==='date'?'background:rgba(245,197,24,.12);color:var(--accent2);border:1px solid rgba(245,197,24,.3)':'background:var(--surface);color:var(--muted);border:1px solid var(--border)'" style="font-size:.68rem;padding:.2rem .5rem;border-radius:4px;cursor:pointer">Specific Date</button>
                </div>
                <div style="display:flex;gap:.6rem;align-items:flex-end;flex-wrap:wrap">
                    <div x-show="mode==='ahead'">
                        <label class="admin-label">Target</label>
                        <select name="days_ahead" class="admin-select" style="width:auto">
                            <option value="1">Tomorrow (D+1)</option>
                            <option value="2">Day after (D+2)</option>
                            <option value="3">3 days (D+3)</option>
                        </select>
                    </div>
                    <div x-show="mode==='date'" style="flex:1;min-width:120px">
                        <label class="admin-label">Date</label>
                        <input type="date" name="date" class="admin-input" value="{{ now()->addDay()->toDateString() }}">
                    </div>
                    <div style="display:flex;align-items:center;gap:.4rem;padding-bottom:.05rem">
                        <input type="hidden" name="force" value="0">
                        <input type="checkbox" name="force" value="1" id="force-full" style="width:13px;height:13px;accent-color:var(--accent2)">
                        <label for="force-full" style="font-size:.68rem;color:var(--muted);cursor:pointer">Force</label>
                    </div>
                    <button type="submit" class="btn-primary" onclick="return confirm('Run full fetch + AI analysis? This will use API and AI credits.')">
                        ▶ Run All
                    </button>
                </div>
            </form>
            <div style="margin-top:.85rem;padding-top:.75rem;border-top:1px solid var(--border);font-size:.68rem;color:var(--muted)">
                Scheduled: <span style="color:var(--text)">22:00 (D+1) &amp; 08:00 (D+2)</span>
            </div>
        </div>

    </div>

    {{-- ── Result Resolution ── --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.3rem;margin-bottom:1.5rem">
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
            <div style="flex:1;min-width:200px">
                <div style="font-family:var(--fh);font-size:1rem;letter-spacing:.07em;color:var(--accent2);margin-bottom:.2rem">Resolve Results</div>
                <p style="font-size:.75rem;color:var(--muted);margin:0;line-height:1.5">
                    Check final scores and mark tips Win / Loss / Void.
                    @if($pendingCount > 0)
                    <span style="background:rgba(245,197,24,.15);color:var(--accent2);padding:.1rem .45rem;border-radius:4px;font-family:var(--fm);font-size:.68rem;margin-left:.4rem">{{ $pendingCount }} pending</span>
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('admin.run-control.resolve') }}" style="display:flex;gap:.6rem;align-items:flex-end">
                @csrf
                <div>
                    <label class="admin-label">Look back</label>
                    <select name="days" class="admin-select" style="width:auto">
                        @foreach([1,2,3,5,7] as $d)
                        <option value="{{ $d }}" {{ $d === 2 ? 'selected' : '' }}>{{ $d }} day{{ $d > 1 ? 's' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-secondary btn-sm" style="border-color:var(--accent2);color:var(--accent2)" onclick="return confirm('Resolve tip results now?')">
                    ✓ Resolve Now
                </button>
            </form>
            <div style="font-size:.68rem;color:var(--muted)">
                Scheduled: <span style="color:var(--text)">14:00 &amp; 23:00 daily</span>
            </div>
        </div>
    </div>

    {{-- ── Run Log History ── --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <div style="padding:.65rem 1rem;background:var(--card2,#0f172a);border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">Analysis Run History</span>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Fixtures</th>
                    <th>Tips Generated</th>
                    <th>Started</th>
                    <th>Completed</th>
                    <th>Duration</th>
                    <th>Error</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-family:var(--fm);font-size:.85rem;font-weight:600;color:var(--text)">
                        {{ $log->run_date->format('D d M Y') }}
                    </td>
                    <td>
                        <span class="badge {{ $log->status === 'completed' ? 'badge-green' : ($log->status === 'running' ? 'badge-yellow' : 'badge-red') }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td style="font-family:var(--fm);font-size:.88rem;color:var(--text)">{{ $log->fixtures_fetched }}</td>
                    <td style="font-family:var(--fm);font-size:.88rem;color:var(--accent)">{{ $log->tips_generated }}</td>
                    <td style="font-size:.75rem;color:var(--muted)">{{ $log->started_at?->format('H:i:s') ?? '—' }}</td>
                    <td style="font-size:.75rem;color:var(--muted)">{{ $log->completed_at?->format('H:i:s') ?? '—' }}</td>
                    <td style="font-family:var(--fm);font-size:.75rem;color:var(--muted)">
                        @if($log->started_at && $log->completed_at)
                        {{ $log->started_at->diffInSeconds($log->completed_at) }}s
                        @else
                        —
                        @endif
                    </td>
                    <td style="max-width:240px">
                        @if($log->error_message)
                        <span style="font-size:.72rem;color:#ef4444;white-space:normal">{{ Str::limit($log->error_message, 80) }}</span>
                        @else
                        <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:2rem;color:var(--muted)">No runs yet. Trigger one above or wait for the scheduler.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div style="margin-top:1rem">{{ $logs->links() }}</div>
    @endif

</x-admin-layout>
