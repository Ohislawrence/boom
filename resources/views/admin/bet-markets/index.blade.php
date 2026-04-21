<x-admin-layout title="Bet Markets">
    <x-slot name="actions">
        <a href="{{ route('admin.bet-markets.create') }}" class="btn-primary btn-sm">+ Add Market</a>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Category</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($betMarkets as $market)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:.88rem">{{ $market->name }}</div>
                        @if($market->description)
                        <div style="font-size:.72rem;color:var(--muted);margin-top:.15rem">{{ Str::limit($market->description, 60) }}</div>
                        @endif
                    </td>
                    <td style="font-family:var(--fm);font-size:.78rem;color:var(--muted)">{{ $market->slug }}</td>
                    <td>
                        @if($market->category)
                        <span class="badge badge-gray">{{ $market->category }}</span>
                        @else
                        <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td style="font-family:var(--fm);font-size:.82rem;color:var(--muted)">{{ $market->sort_order }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.bet-markets.toggle', $market) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge {{ $market->is_active ? 'badge-green' : 'badge-gray' }}" style="cursor:pointer;border:none">
                                {{ $market->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div style="display:flex;gap:.35rem">
                            <a href="{{ route('admin.bet-markets.edit', $market) }}" class="btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.bet-markets.destroy', $market) }}" onsubmit="return confirm('Delete this bet market?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">No bet markets yet. <a href="{{ route('admin.bet-markets.create') }}" style="color:var(--accent)">Add one →</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($betMarkets->hasPages())
    <div style="margin-top:1rem">{{ $betMarkets->links() }}</div>
    @endif

</x-admin-layout>
