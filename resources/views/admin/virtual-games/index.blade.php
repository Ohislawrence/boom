<x-admin-layout title="Virtual Games">
    <x-slot name="breadcrumb">Manage uploaded JS games</x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.virtual-games.create') }}" class="btn-primary btn-sm">+ Add Game</a>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Script</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($games as $game)
                <tr>
                    <td>{{ $game->id }}</td>
                    <td>
                        <div style="font-weight:600">{{ $game->name }}</div>
                        <div style="font-size:.78rem;color:var(--muted)">{{ $game->tagline }}</div>
                    </td>
                    <td>{{ $game->slug }}</td>
                    <td>{{ $game->provider ?? '—' }}</td>
                    <td>
                        @if($game->is_active)
                        <span class="badge badge-green">Active</span>
                        @else
                        <span class="badge badge-red">Inactive</span>
                        @endif
                    </td>
                    <td style="font-size:.78rem;color:var(--muted)">{{ $game->script_path ? basename($game->script_path) : 'No script uploaded' }}</td>
                    <td style="display:flex;gap:.45rem;flex-wrap:wrap">
                        <a href="{{ route('admin.virtual-games.edit', $game) }}" class="btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.virtual-games.destroy', $game) }}" onsubmit="return confirm('Delete this game?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-red btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">No virtual games uploaded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
