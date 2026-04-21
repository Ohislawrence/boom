<x-admin-layout title="Bookmakers">
    <x-slot name="actions">
        <a href="{{ route('admin.bookmakers.create') }}" class="btn-primary">+ Add Bookmaker</a>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Rating</th>
                    <th>Welcome Offer</th>
                    <th>Markets</th>
                    <th>Status</th>
                    <th>Sort</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookmakers as $bm)
                <tr>
                    <td style="color:var(--muted);font-family:var(--fm)">{{ $bm->id }}</td>
                    <td>
                        <div style="font-weight:600">{{ $bm->name }}</div>
                        <div style="font-size:.7rem;color:var(--muted)">{{ $bm->slug }}</div>
                    </td>
                    <td>
                        <div style="display:flex;gap:.15rem;align-items:center">
                            @for($s=1;$s<=5;$s++)
                            <span style="color:{{ $s <= round($bm->rating) ? 'var(--accent2)' : 'var(--border)' }};font-size:.85rem">★</span>
                            @endfor
                            <span style="font-size:.7rem;color:var(--muted);margin-left:.2rem">{{ number_format($bm->rating,1) }}</span>
                        </div>
                    </td>
                    <td style="font-size:.8rem;color:var(--accent2)">{{ $bm->welcome_offer ?? '—' }}</td>
                    <td style="font-size:.78rem;color:var(--muted)">{{ $bm->bet_markets_count }}</td>
                    <td>
                        @if($bm->is_active)
                        <span class="badge badge-green">Active</span>
                        @else
                        <span class="badge badge-gray">Inactive</span>
                        @endif
                    </td>
                    <td style="font-family:var(--fm);font-size:.8rem;color:var(--muted)">{{ $bm->sort_order }}</td>
                    <td>
                        <div style="display:flex;gap:.5rem;align-items:center">
                            <a href="{{ route('admin.bookmakers.edit', $bm) }}" class="btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.bookmakers.destroy', $bm) }}" onsubmit="return confirm('Delete {{ $bm->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:2rem;color:var(--muted)">No bookmakers yet. <a href="{{ route('admin.bookmakers.create') }}" style="color:var(--accent)">Add one</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-admin-layout>
