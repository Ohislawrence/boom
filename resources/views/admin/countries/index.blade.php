<x-admin-layout title="Countries">
    <x-slot name="actions">
        <a href="{{ route('admin.countries.create') }}" class="btn-primary btn-sm">+ Add Country</a>
    </x-slot>

    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Flag</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                <tr>
                    <td style="width:48px">
                        @if($country->flag_url)
                        <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" style="height:20px;border-radius:2px">
                        @else
                        <span style="font-size:1.2rem">🏳️</span>
                        @endif
                    </td>
                    <td style="font-weight:600;font-size:.88rem">{{ $country->name }}</td>
                    <td style="font-family:var(--fm);font-size:.82rem;color:var(--muted)">{{ $country->code ?? '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.countries.toggle', $country) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge {{ $country->is_active ? 'badge-green' : 'badge-gray' }}" style="cursor:pointer;border:none">
                                {{ $country->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div style="display:flex;gap:.35rem">
                            <a href="{{ route('admin.countries.edit', $country) }}" class="btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.countries.destroy', $country) }}" onsubmit="return confirm('Delete {{ $country->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">No countries yet. <a href="{{ route('admin.countries.create') }}" style="color:var(--accent)">Add one →</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($countries->hasPages())
    <div style="margin-top:1rem">{{ $countries->links() }}</div>
    @endif

</x-admin-layout>
