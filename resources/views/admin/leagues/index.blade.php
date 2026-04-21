<x-admin-layout title="Leagues">
    <x-slot name="actions">
        <a href="{{ route('admin.leagues.create') }}" class="btn-primary">+ Add League</a>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Season</th>
                    <th>API ID</th>
                    <th>Fixtures</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($leagues as $league)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem">
                            @if($league->logo_url)
                            <img src="{{ $league->logo_url }}" style="width:22px;height:22px;object-fit:contain">
                            @endif
                            <span style="font-weight:600">{{ $league->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:.82rem;color:var(--muted)">{{ $league->country }}</td>
                    <td style="font-family:var(--fm);font-size:.8rem">{{ $league->season ?? '—' }}</td>
                    <td style="font-family:var(--fm);font-size:.78rem;color:var(--muted)">{{ $league->api_football_id ?? '—' }}</td>
                    <td style="font-size:.82rem">{{ $league->fixtures_count }}</td>
                    <td>
                        @if($league->is_active)
                        <span class="badge badge-green">Active</span>
                        @else
                        <span class="badge badge-gray">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.5rem;align-items:center">
                            <a href="{{ route('admin.leagues.edit', $league) }}" class="btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.leagues.toggle', $league) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-secondary btn-sm" style="color:{{ $league->is_active ? 'var(--muted)' : 'var(--accent)' }}">
                                    {{ $league->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">
                        No leagues yet. <a href="{{ route('admin.leagues.create') }}" style="color:var(--accent)">Add one</a> or run the daily scheduler to auto-import.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-admin-layout>
